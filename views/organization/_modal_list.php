<?php
use humhub\widgets\ModalDialog;
use humhub\widgets\ModalButton;

/* @var $organizations app\modules\crm\models\Organization[] */
/* @var $title string */
?>

<?php ModalDialog::begin(['header' => $title, 'size' => 'large']) ?>
    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
        <?= $this->render('_accordionList', ['organizations' => $organizations]) ?>
    </div>
    <div class="modal-footer">
        <?= ModalButton::cancel('Schließen') ?>
    </div>
<?php ModalDialog::end() ?>
