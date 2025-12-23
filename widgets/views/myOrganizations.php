<?php

use yii\helpers\Html;

/* @var $organizations app\modules\crm\models\Organization[] */
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <strong><i class="fa fa-building"></i> Deine</strong> Organisationen
    </div>
    <div class="panel-body" style="padding: 0;">
        <?php if (empty($organizations)): ?>
            <div style="padding:10px;" class="text-muted text-center">Keine Zuweisungen.</div>
        <?php endif; ?>

        <?php foreach ($organizations as $org): ?>
            <div style="padding: 10px; border-bottom: 1px solid #eee;">
                <strong><?= Html::encode($org->name) ?></strong><br>
                <small class="text-muted">
                    <?= Html::encode($org->category) ?> | <?= Html::encode($org->industry) ?> | <?= Html::encode($org->city) ?>
                </small>
            </div>
        <?php endforeach; ?>
    </div>
</div>
