<?php
use yii\helpers\Html;

/* @var $interaction array (Mock Data) */

// Status colors & label logic
$statusClass = 'label-default';
$statusText = $interaction['status'];
$borderClass = 'border-left: 4px solid #ccc;';

if ($interaction['status'] === 'PLANNED') {
    $statusClass = 'label-info';
    $statusText = 'GEPLANT';
    $borderClass = 'border-left: 4px solid #17a2b8;';
} elseif ($interaction['status'] === 'OVERDUE') {
    $statusClass = 'label-danger';
    $statusText = 'ÜBERFÄLLIG';
    $borderClass = 'border-left: 4px solid #d9534f;';
}

$collapseId = 'interaction-collapse-' . $interaction['id'];
?>

<div class="panel panel-default" style="<?= $borderClass ?> margin-bottom: 10px;">
    <div class="panel-heading" role="button" data-toggle="collapse" href="#<?= $collapseId ?>" aria-expanded="false" style="background-color: #fff; cursor: pointer;">
        <div class="media">
            <div class="media-left">
                <i class="fa fa-comments-o fa-2x text-info" style="margin-top: 5px;"></i>
            </div>
            <div class="media-body">
                <h4 class="media-heading" style="font-size: 16px; font-weight: 600;">
                    <?= Html::encode($interaction['title']) ?>
                    <i class="fa fa-angle-down pull-right text-muted"></i>
                </h4>
                <div class="text-muted" style="font-size: 12px;">
                    <?= $interaction['creator'] ?> • CRM-Space • vor 2 Stunden •
                    <i class="fa fa-users"></i> <?= count($interaction['contacts']) ?> •
                    <i class="fa fa-user"></i> <?= count($interaction['responsible']) ?>
                </div>
            </div>
        </div>
    </div>

    <div id="<?= $collapseId ?>" class="panel-collapse collapse">
        <div class="panel-body" style="border-top: 1px solid #eee;">

            <div class="row" style="margin-bottom: 15px;">
                <div class="col-sm-4">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Status</small><br>
                    <span class="label <?= $statusClass ?>"><?= $statusText ?></span>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Zeitpunkt</small><br>
                    <strong><?= Yii::$app->formatter->asDate($interaction['date'], 'php:d.m.y') ?></strong>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Kanal</small><br>
                    <strong><i class="fa fa-users"></i> <?= Html::encode($interaction['channel']) ?></strong>
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <strong>Beschreibung</strong>
                <div class="well well-sm" style="background: #fff; border: 1px solid #ddd; margin-top: 5px;">
                    <?= nl2br(Html::encode($interaction['description'])) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <strong style="color: #17a2b8;"><i class="fa fa-users"></i> Kontaktpersonen (<?= count($interaction['contacts']) ?>)</strong>
                    <ul class="list-unstyled" style="margin-top: 5px; font-size: 13px;">
                        <?php foreach($interaction['contacts'] as $contact): ?>
                            <li style="margin-bottom: 4px;">
                                <i class="fa fa-user"></i> <strong><?= $contact['name'] ?></strong><br>
                                <span class="text-muted" style="margin-left: 16px; font-size: 11px;">
                                    <i class="fa fa-building-o"></i> <?= $contact['org'] ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="col-sm-6">
                    <strong style="color: #17a2b8;"><i class="fa fa-user-circle"></i> Verantwortliche Nutzer (<?= count($interaction['responsible']) ?>)</strong>
                    <ul class="list-unstyled" style="margin-top: 5px;">
                        <?php foreach($interaction['responsible'] as $user): ?>
                            <li><i class="fa fa-pencil"></i><?= $user ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="text-right" style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #eee;">
                <button class="btn btn-default btn-sm">Kommentieren</button>
                <button class="btn btn-info btn-sm"><i class="fa fa-pencil"></i> Bearbeiten</button>
            </div>

        </div>
    </div>
</div>
