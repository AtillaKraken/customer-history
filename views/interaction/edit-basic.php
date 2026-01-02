<?php

use app\modules\crm\models\Interaction;
use app\modules\crm\models\Contact;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\user\widgets\UserPickerField;
use humhub\modules\content\widgets\richtext\RichTextField;
use app\modules\crm\widgets\ContactMultiselectDropdown;
use humhub\modules\ui\form\widgets\TimePicker;
use humhub\widgets\Link;
use yii\jui\DatePicker;
use yii\helpers\Json;

/* @var $form ActiveForm */
/* @var $model Interaction */

// build a map [contact_id => organization_name] for the upcomming javascript (needed to reactively display orgs)
$contactOrgMap = [];
if ($model->content->container) {
    // load all contacts of the space including their organization
    $allContacts = Contact::find()
        ->contentContainer($model->content->container)
        ->with('organization')
        ->all();

    foreach ($allContacts as $c) {
        if ($c->organization) {
            $contactOrgMap[$c->id] = $c->organization->name;
        }
    }
}
?>

<div style="padding-top: 15px;">
    <div class="modal-body">

        <!-- Title -->
        <?= $form->field($model, 'title')->textInput(['placeholder' => 'Name der Interaktion'])->hint(false) ?>

        <div class="row">
            <div class="col-md-6">
                <!-- Date -->
                <?= $form->field($model, 'date')->widget(DatePicker::class, [
                    'dateFormat' => 'php:d.m.Y',
                    'clientOptions' => [],
                    'options' => ['class' => 'form-control', 'placeholder' => 'TT.MM.JJJJ']
                ]) ?>
            </div>
            <div class="col-md-6">
                <!-- Time -->
                <?= $form->field($model, 'time')->widget(TimePicker::class) ; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Channel -->
                <?= $form->field($model, 'channel')->dropDownList(Interaction::getChannelOptions(), [
                    'prompt' => 'Bitte auswählen...'
                ]) ?>
            </div>
            <div class="col-md-6">
                <!-- Status -->
                <?= $form->field($model, 'status')->dropDownList(Interaction::getStatusOptions(), [
                    'prompt' => 'Bitte auswählen...'
                ]) ?>
            </div>
        </div>

        <!-- Contact-MultiselectDropdown -->
        <?= $form->field($model, 'contactIds')->widget(ContactMultiselectDropdown::class, [
            'contentContainer' => $model->content->container,
            'options' => ['id' => 'interaction-contact-picker']
        ])->label('Kontaktpersonen') ?>

        <!-- (thus) affected organizations -->
        <div class="form-group">
            <label for="affected-organizations-display" class="control-label">Betroffene Organisationen</label>
            <input type="text" id="affected-organizations-display"
                   class="form-control" disabled
                   placeholder="Wird automatisch befüllt..."
                   style="background-color: #f9f9f9;">
        </div>

        <!-- Description (Rich Text) -->
        <?= $form->field($model, 'description')->widget(RichTextField::class) ?>

        <!-- Result (Rich Text) -->
        <div id="interaction-result-wrapper" style="display:none;">
            <?= $form->field($model, 'result')->widget(RichTextField::class) ?>
        </div>

        <!-- Responsible Users -->
        <?= $form->field($model, 'responsibleUserGuids')->widget(UserPickerField::class, [
            'id' => 'crm-responsible-user-picker',
            'selection' => $model->responsibleUsers,
            'placeholder' => 'Benutzer zuweisen...'
        ]) ?>

        <!-- "Assign me" Button -->
        <div class="text-right">
            <?= Link::userPickerSelfSelect('#crm-responsible-user-picker', 'Mich zuweisen')
                ->icon('fa-check-circle')
                ->options(['class' => 'small']) ?>
        </div>

    </div>
</div>

<?php
// js logic for the reactive organizaton-display & status toggle
// sidenote: injected javascript via php instead of <script> to be CSP safe
$contactOrgMapJson = Json::encode($contactOrgMap);

$script = <<<JS
(function() {
    var contactOrgMap = $contactOrgMapJson;
    var pickerId = '#interaction-contact-picker';
    var displayId = '#affected-organizations-display';
    var lastValue = '';

    // ORG LOGIC:
    var updateOrgs = function() {
        var val = $(pickerId).val();

        // check if value changed (to avoid heavy DOM operations)
        var currentKey = JSON.stringify(val);
        if (currentKey === lastValue) {
            return;
        }
        lastValue = currentKey;

        var orgs = [];
        var ids = [];

        // normalize input (array, string, or single value)
        if (Array.isArray(val)) {
            ids = val;
        } else if (typeof val === 'string' && val.trim() !== '') {
            ids = val.split(',');
        } else if (val) {
            ids = [val];
        }

        // map IDs to Organization Names
        ids.forEach(function(id) {
            if (contactOrgMap.hasOwnProperty(id)) {
                var orgName = contactOrgMap[id];
                if (orgs.indexOf(orgName) === -1) {
                    orgs.push(orgName);
                }
            }
        });

        // update the inputfield
        $(displayId).val(orgs.join(', '));
    };

    // RESULT TOGGLE LOGIC:
    var toggleResult = function() {
        var status = $('#interaction-status').val();
        var resultWrapper = $('#interaction-result-wrapper');

        if (status === 'Erledigt') {
            resultWrapper.slideDown();
        } else {
            resultWrapper.slideUp();
        }
    };

    // --- INIT ---
    // check every 800ms
    var orgPollTimer = setInterval(updateOrgs, 800);
    updateOrgs();

    // status-selection listener
    $('#interaction-status').on('change', toggleResult);
    toggleResult(); // run once on load (for edit mode)

    // cleanup when modal closes
    $(document).on('hidden.bs.modal', function (e) {
        // ensure it only gets cleared if the affected orgs field was actually part of the closed modal
        if ($(e.target).find(pickerId).length > 0) {
            clearInterval(orgPollTimer);
        }
    });
})();
JS;

$this->registerJs($script);
?>
