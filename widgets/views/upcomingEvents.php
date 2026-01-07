<?php

use yii\helpers\Html;

/* @var $events humhub\modules\crm\models\Event[] */
/* @var $totalCount int */
/* @var $limit int */
/* @var $contentContainer humhub\modules\content\components\ContentContainerActiveRecord */

$showAllUrl = $contentContainer->createUrl('/crm/event/load-upcoming');
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <strong><i class="fa fa-calendar"></i> Anstehende</strong> Veranstaltungen
        <?php if ($totalCount > $limit): ?>
            <small class="pull-right">
                <a href="#" data-action-click="ui.modal.load" data-action-url="<?= $showAllUrl ?>">
                    (Zeige alle <?= $totalCount ?>)
                </a>
            </small>
        <?php endif; ?>
    </div>
    <div class="panel-body" style="padding: 0;">
        <?php foreach ($events as $evt): ?>
            <?php
            $viewUrl = $evt->content->container->createUrl('/crm/event/view', ['id' => $evt->id]);
            ?>
            <div style="padding: 10px; border-bottom: 1px solid #eee; cursor: pointer;"
                 onmouseover="this.style.backgroundColor='#f5f5f5';"
                 onmouseout="this.style.backgroundColor='transparent';"
                 data-action-click="ui.modal.load"
                 data-action-url="<?= $viewUrl ?>">

                <strong><?= Html::encode($evt->title) ?></strong><br>
                <small class="text-muted">
                    Datum: <?= Yii::$app->formatter->asDate($evt->date, 'php:d.m.y') ?> ·
                    Format: <?= Html::encode($evt->type) ?>
                </small>
            </div>
        <?php endforeach; ?>
    </div>
</div>
