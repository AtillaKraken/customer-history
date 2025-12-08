<?php

use app\modules\crm\models\Event;
use humhub\widgets\ModalDialog;
use humhub\widgets\ModalButton;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\widgets\Tabs;

/* @var $model Event */

// set modal title dynamically
$header = $model->isNewRecord
    ? 'Neue <strong>Veranstaltung</strong> erstellen'
    : 'Veranstaltung <strong>bearbeiten</strong>';

$buttonText = $model->isNewRecord ? 'Erstellen' : 'Speichern';
?>

<?php ModalDialog::begin(['header' => 'Neue <strong>Veranstaltung</strong>', 'size' => 'large']) ?>
<?php $form = ActiveForm::begin(); ?>
<?= Tabs::widget([
    'items' => [
        [
            'label' => 'Allgemein',
            'content' => $this->render('edit-basic', ['model' => $model, 'form' => $form]),
            'active' => true, // initially the first tab is active
        ],
        [
            'label' => 'Anhänge',
            'content' => $this->render('edit-attachments', ['model' => $model, 'form' => $form]),
        ],
    ]
]); ?>

<div class="modal-footer">
    <?= ModalButton::submitModal('Speichern', ['class' => 'btn btn-primary']) ?>
    <?= ModalButton::cancel('Abbrechen') ?>
</div>
<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
