<?php

use app\modules\crm\models\Organization;
use humhub\widgets\ModalDialog;
use humhub\widgets\ModalButton;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\user\widgets\UserPickerField;
use humhub\widgets\RichTextEditor;

/* @var $model Organization */
?>

<?php ModalDialog::begin(['header' => 'Neue <strong>Organisation</strong>', 'size' => 'large']) ?>
<?php $form = ActiveForm::begin(); ?>
    <div class="modal-body">

        <!-- Name -->
        <?= $form->field($model, 'name')->textInput(['placeholder' => 'Name der Organisation'])->hint(false) ?>

        <div class="row">
            <div class="col-md-6">
                <!-- Category -->
                <?= $form->field($model, 'category')->dropDownList([
                    'partner' => 'Partner', 'customer' => 'Kunde', 'lead' => 'Interessent'
                ], ['prompt' => 'Bitte auswählen...']) ?>
            </div>
            <div class="col-md-6">
                <!-- Industry -->
                <?= $form->field($model, 'industry')->dropDownList([
                    'it' => 'IT & Software', 'retail' => 'Handel'
                ], ['prompt' => 'Bitte auswählen...']) ?>
            </div>
        </div>

        <!-- Adresse -->
        <?= $form->field($model, 'address')->textInput(['placeholder' => 'Straße, PLZ, Ort']) ?>

        <!-- Notes (Rich Text) -->
        <?= $form->field($model, 'description')->widget(RichTextEditor::class, [
            'placeholder' => 'Notizen bzgl. dieser Organisation...'
        ]) ?>

        <!-- Responsible Users -->
        <!-- TODO: als safe markieren in Model  -->
        <?= $form->field($model, 'assignedUsers')->widget(UserPickerField::class, [
            'selection' => $model->getAssignedUsers(), // TODO: anlegen im Model
            'placeholder' => 'Benutzer zuweisen...'
        ])->hint('Falls leer, kann jeder diese Organisation betreuen') ?>

        <!-- "Mich zuweisen" Link -->
        <div class="text-right">
            <a href="#" class="small"><i class="fa fa-check-circle"></i> Mich zuweisen</a>
        </div>

    </div>
    <div class="modal-footer">
        <?= ModalButton::submitModal('Speichern', ['class' => 'btn btn-primary']) ?>
        <?= ModalButton::cancel('Abbrechen') ?>
    </div>
<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
