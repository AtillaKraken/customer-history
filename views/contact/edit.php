<?php

use humhub\modules\crm\models\Contact;
use humhub\widgets\ModalDialog;
use humhub\widgets\ModalButton;
use humhub\modules\ui\form\widgets\ActiveForm;

/* @var $model Contact */
/* @var $organizations array */

$header = $model->isNewRecord
    ? 'Neue <strong>Kontaktperson</strong> erstellen'
    : 'Kontaktperson <strong>bearbeiten</strong>';
?>

<?php ModalDialog::begin(['header' => $header, 'size' => 'large']) ?>
<?php $form = ActiveForm::begin(); ?>
    <div class="modal-body">
        <?= $this->render('edit-basic', [
            'model' => $model,
            'form' => $form,
            'organizations' => $organizations
        ]) ?>
    </div>

    <div class="modal-footer">
        <?= ModalButton::submitModal(null, 'Speichern') ?>
        <?= ModalButton::cancel('Abbrechen') ?>
    </div>
<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
