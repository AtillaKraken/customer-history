<?php

use app\modules\crm\models\Interaction;
use yii\helpers\Html;

/* @var $interactions app\modules\crm\models\Interaction[] */
/* @var $totalCount int */
/* @var $limit int */
/* @var $contentContainer humhub\modules\content\components\ContentContainerActiveRecord */

$showAllUrl = $contentContainer->createUrl('/crm/interaction/load-my-interactions');

$statusClass = 'label-default';
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <strong><i class="fa fa-comments-o"></i> Deine</strong> Interaktionen

        <?php if ($totalCount > $limit): ?>
            <small class="pull-right">
                <a href="#" data-action-click="ui.modal.load" data-action-url="<?= $showAllUrl ?>">
                    (Zeige alle <?= $totalCount ?>)
                </a>
            </small>
        <?php endif; ?>
    </div>
    <div class="panel-body" style="padding: 0;">
        <?php if (empty($interactions)): ?>
            <div style="padding:10px;" class="text-muted text-center">Keine offenen Aufgaben.</div>
        <?php endif; ?>

        <?php foreach ($interactions as $int): ?>
            <?php
            switch ($int->status) {
                case Interaction::STATUS_PLANNED:
                    $statusClass = 'label-info';
                    break;
                case Interaction::STATUS_OVERDUE:
                    $statusClass = 'label-danger';
                    break;
                case Interaction::STATUS_CANCELLED:
                    $statusClass = 'label-warning';
                    break;
                case Interaction::STATUS_DONE:
                    $statusClass = 'label-success';
                    break;
            }


            $viewUrl = $int->content->container->createUrl('/crm/interaction/view', ['id' => $int->id]);
            ?>
            <div style="padding: 10px; border-bottom: 1px solid #eee; cursor: pointer;"
                 onmouseover="this.style.backgroundColor='#f5f5f5';"
                 onmouseout="this.style.backgroundColor='transparent';"
                 data-action-click="ui.modal.load"
                 data-action-url="<?= $viewUrl ?>">

                <div style="margin-bottom: 2px;">
                    <strong style="color:#333;"><?= Html::encode($int->title) ?></strong>
                    <span class="label pull-right <?= $statusClass ?>"><i class="fa fa-flag"></i> <?= $int->status ?></span>
                </div>
                <small class="text-muted">
                    <?= Yii::$app->formatter->asDate($int->date, 'php:d.m.y') ?>
                    <?php
                    $cnts = $int->getContacts()->count();
                    echo $cnts ? '· <i class="fa fa-users"></i>  ' . $cnts : '';
                    ?>
                    <?php
                    $orgs = $int->getOrganizations()->count();
                    echo $orgs ? '· <i class="fa fa-building-o"></i>  ' . $orgs : '';
                    ?>
                </small>
            </div>
        <?php endforeach; ?>
    </div>
</div>
