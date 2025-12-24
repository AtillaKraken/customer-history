<?php
use app\modules\crm\widgets\InteractionCard;

/* @var $model app\modules\crm\models\Interaction */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <a href="<?= $model->content->container->createUrl('/crm/interaction/index') ?>" class="btn btn-default btn-sm" style="margin-bottom: 15px;">
                        <i class="fa fa-arrow-left"></i> Zurück zur Übersicht
                    </a>

                    <?= InteractionCard::widget(['interaction' => $model]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
