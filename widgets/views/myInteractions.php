<?php

use yii\helpers\Html;
use humhub\widgets\PanelMenu;

/* @var $interactions app\modules\crm\models\Interaction[] */
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <strong><i class="fa fa-commenting-o"></i> Deine</strong> Interaktionen
        <small class="pull-right"><a href="#">(Zeige alle)</a></small>
    </div>
    <div class="panel-body" style="padding: 0;">
        <?php if (empty($interactions)): ?>
            <div style="padding:10px;" class="text-muted text-center">Keine offenen Aufgaben.</div>
        <?php endif; ?>

        <?php foreach ($interactions as $int): ?>
            <?php
            $isOverdue = (strtotime($int->date) < time() && $int->status !== 'DONE');
            $statusLabel = $isOverdue ? '<span class="label label-danger pull-right" style="font-size: 9px;">ÜBERFÄLLIG</span>' : '';
            ?>
            <div style="padding: 10px; border-bottom: 1px solid #eee;">
                <div style="margin-bottom: 2px;">
                    <strong><a href="#" data-action-click="ui.modal.load"
                               data-action-url="<?= $int->content->container->createUrl('/crm/interaction/view', ['id' => $int->id]) ?>" style="color:#333;"><?= Html::encode($int->title) ?></a></strong>
                                        <?= $statusLabel ?>
                </div>
                <small class="text-muted">
                    <?= Yii::$app->formatter->asDate($int->date, 'php:d.m.y') ?> |
                    Betroffen:
                    <?php
                    $firstOrg = $int->getOrganizations()->one();
                    echo $firstOrg ? '<i class="fa fa-building-o"></i> ' . Html::encode($firstOrg->name) : '-';
                    ?>
                </small>
            </div>
        <?php endforeach; ?>
    </div>
</div>
