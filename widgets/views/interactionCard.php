<?php

use yii\helpers\Html;
use Yii;

/* @var $interaction array */

$statusClass = 'label-default';
$statusText = $interaction['status'] ?? 'UNKNOWN';
$borderClass = 'border-left: 4px solid #ccc;';


switch (($interaction['status'] ?? '')) {
    case 'PLANNED':
        $statusClass = 'label-info';
        $statusText = ' GEPLANT';
        $borderClass = 'border-left: 4px solid #17a2b8;';
        break;
    case 'OVERDUE':
        $statusClass = 'label-danger';
        $statusText = 'ÜBERFÄLLIG';
        $borderClass = 'border-left: 4px solid #d9534f;';
        break;
    case 'CANCELLED':
        $statusClass = 'label-warning';
        $statusText = 'ABGESAGT';
        $borderClass = 'border-left: 4px solid #FFC107;';
        break;
    case 'DONE':
        $statusClass = 'label-success';
        $statusText = 'ERLEDIGT';
        $borderClass = 'border-left: 4px solid #97d271;';
        break;


    case 'UNKNOWN':
        $statusClass = 'label-danger';
        $statusText = 'UNBEKANNT';
        $borderClass = 'border-left: 4px solid #d9534f;';
        break;
}

// generate unique ID for collapse
$collapseId = 'interaction-collapse-' . ($interaction['id'] ?? uniqid());
?>

<div class="panel panel-default" style="<?= $borderClass ?> margin-bottom: 10px;">
    <!-- Header -->
    <div class="panel-heading" role="button" data-toggle="collapse" href="#<?= $collapseId ?>" aria-expanded="false"
         style="background-color: #fff; cursor: pointer;">
        <div class="media">
            <div class="media-left">
                <i class="fa fa-comments-o fa-2x text-info" style="margin-top: 5px; margin-right: 10px"></i>
            </div>
            <div class="media-body">
                <h4 class="media-heading" style="font-size: 16px; font-weight: 600;">
                    <?= Html::encode($interaction['title']) ?>
                    <i class="fa fa-angle-down pull-right text-muted"></i>
                </h4>
                <div class="text-muted" style="font-size: 12px;">
                    <?= Html::encode($interaction['creator'] ?? 'Unbekannt') ?> • CRM-Space •
                    <!-- TODO: Zeitlogik => (TimeAgo) -->
                    vor 2 Stunden •
                    <i class="fa fa-users"></i> <?= count($interaction['contacts'] ?? []) ?> •
                    <i class="fa fa-user"></i> <?= count($interaction['responsible'] ?? []) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- body -->
    <div id="<?= $collapseId ?>" class="panel-collapse collapse">
        <div class="panel-body" style="border-top: 1px solid #eee;">

            <!-- info row -->
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-sm-4">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Status</small><br>
                    <span class="label <?= $statusClass ?>"><?= Html::encode($statusText) ?></span>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Zeitpunkt</small><br>
                    <strong><?= Yii::$app->formatter->asDate($interaction['date'] ?? 'now', 'php:d.m.y') ?></strong>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Kanal</small><br>
                    <strong><i class="fa fa-share-alt"></i> <?= Html::encode($interaction['channel'] ?? '-') ?></strong>
                </div>
            </div>

            <!-- description -->
            <div style="margin-bottom: 15px;">
                <strong>Beschreibung</strong>
                <div class="well well-sm" style="background: #fff; border: 1px solid #ddd; margin-top: 5px;">
                    <?= nl2br(Html::encode($interaction['description'] ?? '')) ?>
                </div>
            </div>

            <!-- People (Contacts & resp. Users) -->
            <div class="row">
                <div class="col-sm-6">
                    <strong style="color: #17a2b8;"><i class="fa fa-users"></i> Kontaktpersonen
                        (<?= count($interaction['contacts'] ?? []) ?>)</strong>
                    <ul class="list-unstyled" style="margin-top: 5px; font-size: 13px;">
                        <?php foreach (($interaction['contacts'] ?? []) as $contact): ?>
                            <li style="margin-bottom: 4px;">
                                <i class="fa fa-user"></i> <strong><?= Html::encode($contact['name']) ?></strong><br>
                                <span class="text-muted" style="margin-left: 16px; font-size: 11px;">
                                    <i class="fa fa-building-o"></i> <?= Html::encode($contact['org']) ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="col-sm-6">
                    <strong style="color: #17a2b8;"><i class="fa fa-user-circle"></i> Verantwortliche
                        (<?= count($interaction['responsible'] ?? []) ?>)</strong>
                    <ul class="list-unstyled" style="margin-top: 5px;">
                        <?php foreach (($interaction['responsible'] ?? []) as $user): ?>
                            <li><i class="fa fa-user"></i> <?= Html::encode($user) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="text-right" style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #eee;">
                <button class="btn btn-default btn-sm">Kommentieren</button>
                <button class="btn btn-info btn-sm"><i class="fa fa-pencil"></i> Bearbeiten</button>
                <!-- TODO: Humhub-natives Erscheinungbild gem. Figmafile draus machen!-->
            </div>

        </div>
    </div>
</div>
