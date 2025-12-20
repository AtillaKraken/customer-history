<?php

use app\modules\crm\models\Interaction;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\user\widgets\UserPickerField;
use humhub\modules\content\widgets\richtext\RichTextField;
use app\modules\crm\widgets\ContactMultiselectDropdown;
use humhub\modules\ui\form\widgets\TimePicker;
use humhub\widgets\Link;
use yii\jui\DatePicker;

/* @var $form ActiveForm */
/* @var $model Interaction */
?>

<div style="padding-top: 15px;">
    <div class="modal-body">

        <!-- Name -->
        <?= $form->field($model, 'title')->textInput(['placeholder' => 'Name der Interaktion'])->hint(false) ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'date')->widget(DatePicker::class, [
                    'clientOptions' => [],
                    'options' => ['class' => 'form-control']
                ]) ?>
            </div>
            <div class="col-md-6">
                <!-- Time -->
                <?= $form->field($model, 'time')->widget(TimePicker::class); ?>
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
            'contentContainer' => $model->content->container
        ])->label('Kontaktpersonen') ?>

        <!-- (thus) affected organizations -->
        <div class="form-group">
            <label for="affected-organizations-display" class="control-label">Betroffene Organisationen</label>
            <input type="text" id="affected-organizations-display"
                   class="form-control" disabled
                   placeholder="Wird automatisch befüllt..."
                   style="background-color: #f9f9f9;">
        </div>
        <!-- TODO: Einbauen dass sich das feld tatsächlich dynamisch befüllt nach contact-selections-->

        <!-- Description (Rich Text) -->
        <?= $form->field($model, 'description')->widget(RichTextField::class) ?>

        <!-- TODO: Toggle Logic einbauen | Wenn Status === DONE zeige result an, else description-->

        <!-- Result (Rich Text) -->
        <?= $form->field($model, 'result')->widget(RichTextField::class) ?>

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
