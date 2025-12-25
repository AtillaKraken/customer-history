<?php
use app\modules\crm\widgets\ContactCard;
use humhub\widgets\ModalDialog;
use humhub\widgets\ModalButton;

/* @var $model app\modules\crm\models\contact */

$isAjax = Yii::$app->request->isAjax;
?>

<?php if ($isAjax): ?>
    <?php ModalDialog::begin(['header' => 'Details: ' . \yii\helpers\Html::encode($model->name), 'size' => 'large']) ?>
    <div class="modal-body" style="padding-bottom: 0;">
        <?= ContactCard::widget(['contact' => $model, 'startCollapsed' => false]) ?>
    </div>
    <div class="modal-footer">
        <?= ModalButton::cancel('Schließen') ?>
    </div>
    <?php ModalDialog::end() ?>

<?php else: ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <a href="<?= $model->content->container->createUrl('/crm/contact/index') ?>" class="btn btn-default btn-sm" style="margin-bottom: 15px;">
                            <i class="fa fa-arrow-left"></i> Zurück zur Übersicht
                        </a>
                        <?= ContactCard::widget(['contact' => $model, 'startCollapsed' => false]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
