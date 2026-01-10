<?php

use humhub\modules\crm\models\Event;
use humhub\modules\crm\models\Contact;
use humhub\modules\ui\form\widgets\TimePicker;
use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\crm\widgets\ContactMultiselectDropdown;
use yii\jui\DatePicker;
use yii\helpers\Json;

/* @var $model Event */
/* @var $form humhub\modules\ui\form\widgets\ActiveForm */

// build a map [contact_id => organization_name] for reactive JS display
$contactOrgMap = [];
if ($model->content->container) {
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

            <?= $form->field($model, 'title')->textInput(['placeholder' => 'Titel der Veranstaltung'])->hint(false) ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'date')->widget(DatePicker::class, [
                        'dateFormat' => 'php:d.m.Y',
                        'clientOptions' => [],
                        'options' => ['class' => 'form-control', 'placeholder' => 'TT.MM.JJJJ']
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'time')->widget(TimePicker::class); ?>
                </div>
            </div>

            <?= $form->field($model, 'type')->dropDownList(Event::getTypeOptions(), ['prompt' => 'Bitte auswählen...']) ?>

            <div id="event-contact-picker-wrapper">
                <?= $form->field($model, 'contactIds')->widget(ContactMultiselectDropdown::class, [
                    'contentContainer' => $model->content->container,
                    'options' => ['id' => 'event-contact-picker']
                ])->label('Teilnehmende Kontaktpersonen') ?>
            </div>

            <div class="form-group">
                <label class="control-label">Betroffene Organisationen</label>
                <input type="text" id="event-affected-organizations-display"
                       class="form-control" disabled
                       placeholder="Wird automatisch befüllt..."
                       style="background-color: #f9f9f9; color: #555;">
            </div>

            <?= $form->field($model, 'description')->widget(RichTextField::class) ?>
        </div>
    </div>

<?php
// JS Logic for reactive organization display (same pattern as Interaction)
$contactOrgMapJson = Json::encode($contactOrgMap);

$script = <<<JS
(function() {
    var contactOrgMap = $contactOrgMapJson;
    var pickerId = '#event-contact-picker';
    var displayId = '#event-affected-organizations-display';
    var lastValue = '';

    var updateOrgs = function() {
        var val = $(pickerId).val();

        var currentKey = JSON.stringify(val);
        if (currentKey === lastValue) {
            return;
        }
        lastValue = currentKey;

        var orgs = [];
        var ids = [];

        if (Array.isArray(val)) {
            ids = val;
        } else if (typeof val === 'string' && val.trim() !== '') {
            ids = val.split(',');
        } else if (val) {
            ids = [val];
        }

        ids.forEach(function(id) {
            if (contactOrgMap.hasOwnProperty(id)) {
                var orgName = contactOrgMap[id];
                if (orgs.indexOf(orgName) === -1) {
                    orgs.push(orgName);
                }
            }
        });

        $(displayId).val(orgs.join(', '));
    };

    // Polling every 800ms
    var orgPollTimer = setInterval(updateOrgs, 800);
    updateOrgs();

    $(document).on('hidden.bs.modal', function (e) {
        if ($(e.target).find(pickerId).length > 0) {
            clearInterval(orgPollTimer);
        }
    });
})();
JS;

$this->registerJs($script);
?>
