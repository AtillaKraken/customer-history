<?php

use app\modules\crm\models\Organization;
use humhub\widgets\ModalDialog;
use humhub\widgets\ModalButton;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\user\widgets\UserPickerField;
use humhub\modules\content\widgets\richtext\RichTextField;

/* @var $model Organization */

// set modal title dynamically
$header = $model->isNewRecord
    ? 'Neue <strong>Organisation</strong> erstellen'
    : 'Organisation <strong>bearbeiten</strong>';

$buttonText = $model->isNewRecord ? 'Erstellen' : 'Speichern';
?>

<?php ModalDialog::begin(['header' => 'Neue <strong>Organisation</strong>', 'size' => 'large']) ?>
<?php $form = ActiveForm::begin(); ?>
<div class="modal-body">
    <!-- embed content from edit-basic.php -->
    <?= $this->render('edit-basic', ['model' => $model, 'form' => $form]) ?>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, 'Speichern') ?>
    <?= ModalButton::cancel('Abbrechen') ?>
</div>
<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
