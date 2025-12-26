<?php

use yii\helpers\Html;

/* @var $organizations app\modules\crm\models\Organization[] */
/* @var $totalCount int */
/* @var $limit int */
/* @var $contentContainer humhub\modules\content\components\ContentContainerActiveRecord */

$showAllUrl = $contentContainer->createUrl('/crm/organization/load-my-organizations');
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <strong><i class="fa fa-building"></i> Deine</strong> Organisationen
        <?php if ($totalCount > $limit): ?>
            <small class="pull-right">
                <a href="#" data-action-click="ui.modal.load" data-action-url="<?= $showAllUrl ?>">
                    (Zeige alle <?= $totalCount ?>)
                </a>
            </small>
        <?php endif; ?>
    </div>
    <div class="panel-body" style="padding: 0;">
        <?php if (empty($organizations)): ?>
            <div style="padding:10px;" class="text-muted text-center">Keine Zuweisungen.</div>
        <?php endif; ?>

        <?php foreach ($organizations as $org): ?>
            <?php
            $viewUrl = $org->content->container->createUrl('/crm/organization/view', ['id' => $org->id]);
            ?>
            <div style="padding: 10px; border-bottom: 1px solid #eee; cursor: pointer;"
                 onmouseover="this.style.backgroundColor='#f5f5f5';"
                 onmouseout="this.style.backgroundColor='transparent';"
                 data-action-click="ui.modal.load"
                 data-action-url="<?= $viewUrl ?>">

                <strong><?= Html::encode($org->name) ?></strong><br>
                <small class="text-muted">
                    <?= Html::encode($org->category) ?> · <?= Html::encode($org->industry) ?> · <?= Html::encode($org->city) ?>
                </small>
            </div>
        <?php endforeach; ?>
    </div>
</div>
