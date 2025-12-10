<?php

use yii\helpers\Html;

/* @var $events app\modules\crm\models\Event[] */
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <strong><i class="fa fa-calendar"></i> Anstehende</strong> Veranstaltungen
    </div>
    <div class="panel-body" style="padding: 0;">
        <?php foreach ($events as $evt): ?>
            <div style="padding: 10px; border-bottom: 1px solid #eee;">
                <strong><?= Html::encode($evt->title) ?></strong><br>
                <small class="text-muted">
                    Datum: <?= Yii::$app->formatter->asDate($evt->date, 'php:d.m.y') ?> |
                    Typ: <?= Html::encode($evt->type) ?>
                </small>
            </div>
        <?php endforeach; ?>
    </div>
</div>
