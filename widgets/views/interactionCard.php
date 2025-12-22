<?php

use app\modules\crm\models\Interaction;
use yii\helpers\Html;
use humhub\modules\like\widgets\LikeLink;
use humhub\modules\comment\widgets\CommentLink;
use humhub\modules\comment\widgets\Comments;
use humhub\modules\comment\models\Comment;

/* @var $interaction app\modules\crm\models\Interaction */

$statusClass = 'label-default';
$borderClass = 'border-left: 4px solid #ccc;';

switch ($interaction->status) {
    case Interaction::STATUS_PLANNED:
        $statusClass = 'label-info';
        $borderClass = 'border-left: 4px solid #17a2b8;';
        break;
    case Interaction::STATUS_OVERDUE:
        $statusClass = 'label-danger';
        $borderClass = 'border-left: 4px solid #d9534f;';
        break;
    case Interaction::STATUS_CANCELLED:
        $statusClass = 'label-warning';
        $borderClass = 'border-left: 4px solid #FFC107;';
        break;
    case Interaction::STATUS_DONE:
        $statusClass = 'label-success';
        $borderClass = 'border-left: 4px solid #97d271;';
        break;
}

// generate unique ID for collapse
$collapseId = 'interaction-collapse-' . $interaction->id;
$commentCount = Comment::getCommentCount(Interaction::class, $interaction->id);
?>

<style>
    /* rotate arrow animation when card is opened */
    .panel-heading[aria-expanded="true"] .interaction-toggle-icon {
        transform: rotate(180deg);
    }
    .interaction-toggle-icon {
        transition: transform 0.3s ease;
    }
</style>

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
                    <?= Html::encode($interaction->title) ?>
                    <i class="fa fa-angle-down pull-right text-muted interaction-toggle-icon"></i>
                </h4>
                <div class="text-muted" style="font-size: 12px;">

                    <strong class="text-dark" style="margin-right: 5px;">
                        <i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDate($interaction->date, 'php:d.m.Y') ?>
                    </strong> •

                    <a href="<?= $interaction->content->createdBy->getUrl() ?>" class="text-muted">
                        <?= Html::encode($interaction->content->createdBy->displayName ?? 'System') ?>
                    </a> •

                    <?= Yii::$app->formatter->asRelativeTime($interaction->content->created_at) ?> •

                    <i class="fa fa-users" title="Kontaktpersonen"></i> <?= count($interaction->contacts) ?> •
                    <i class="fa fa-user" title="Verantwortliche"></i> <?= count($interaction->responsibleUsers) ?> •
                    <i class="fa fa-comment-o" title="Kommentare"></i> <?= $commentCount ?>
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
                    <span class="label <?= $statusClass ?>"><?= $interaction->status ?></span>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Zeitpunkt</small><br>
                    <!-- TODO: if status==DONE => Result ebenfalls einblenden | Ebenfalls mit obigen RichText sobald gefixt -->
                    <strong><?= Yii::$app->formatter->asDate($interaction->date, 'php:d.m.Y') ?></strong>
                    <?php if($interaction->time): ?>
                        <small>(<?= Html::encode($interaction->time) ?>)</small>
                    <?php endif; ?>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Kanal</small><br>
                    <strong><i class="fa fa-share-alt"></i> <?= Html::encode($interaction->channel ?? '-') ?></strong>
                </div>
            </div>

            <!-- description -->
            <div style="margin-bottom: 15px;">
                <strong>Beschreibung</strong>
                <div class="well well-sm" style="background: #fff; border: 1px solid #ddd; margin-top: 5px;">
                    <div class="crm-richtext-container" id="desc-container-<?= $interaction->id ?>">
                        <?= \humhub\modules\content\widgets\richtext\RichText::output($interaction->description) ?>
                    </div>
                </div>
            </div>

            <?php if ($interaction->status === Interaction::STATUS_DONE && !empty($interaction->result)): ?>
                <div style="margin-bottom: 15px;">
                    <strong>Ergebnis</strong>
                    <div class="well well-sm" style="background: #eaffea; border: 1px solid #c3e6cb; margin-top: 5px;">
                        <div class="crm-richtext-container" id="res-container-<?= $interaction->id ?>">
                            <?= \humhub\modules\content\widgets\richtext\RichText::output($interaction->result) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($interaction->content->getTags(humhub\modules\topic\models\Topic::class)->count())): ?>
                <div style="margin-bottom: 15px;">
                    <i class="fa fa-tags text-muted"></i>
                    <?php foreach ($interaction->content->getTags(humhub\modules\topic\models\Topic::class)->all() as $topic): ?>
                        <span class="label label-default" style="margin-right: 2px;"><?= \yii\helpers\Html::encode($topic->name) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($interaction->externalLinks)): ?>
                <div style="margin-bottom: 15px;">
                    <strong><i class="fa fa-link"></i> Verknüpfte Links</strong>
                    <ul class="list-unstyled" style="margin-top: 5px; padding-left: 10px; border-left: 2px solid #eee;">
                        <?php foreach ($interaction->externalLinks as $link): ?>
                            <li style="margin-bottom: 3px;">
                                <a href="<?= Html::encode($link->url) ?>" target="_blank" rel="noopener noreferrer">
                                    <i class="fa fa-external-link-square"></i> <?= Html::encode($link->url) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- People (Contacts & resp. Users) -->
            <div class="row">
                <div class="col-sm-6">
                    <strong style="color: #17a2b8;"><i class="fa fa-users"></i> Kontaktpersonen</strong>
                    <ul class="list-unstyled" style="margin-top: 5px; font-size: 13px;">
                        <?php foreach ($interaction->contacts as $contact): ?>
                            <li style="margin-bottom: 4px;">
                                <i class="fa fa-user"></i> <strong><?= Html::encode($contact->name) ?></strong><br>
                                <?php if ($contact->organization): ?>
                                    <span class="text-muted" style="margin-left: 16px; font-size: 11px;">
                                        <i class="fa fa-building-o"></i> <?= Html::encode($contact->organization->name) ?>
                                    </span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="col-sm-6">
                    <strong style="color: #17a2b8;"><i class="fa fa-user-circle"></i> Verantwortliche</strong>
                    <ul class="list-unstyled" style="margin-top: 5px;">
                        <?php foreach ($interaction->responsibleUsers as $user): ?>
                            <li>
                                <a href="<?= $user->getUrl() ?>">
                                    <img src="<?= $user->getProfileImage()->getUrl() ?>" class="img-rounded" style="width: 16px; height: 16px; margin-right: 5px;"/>
                                    <?= Html::encode($user->displayName) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="text-right" style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #eee;">
                <span style="margin-right: 15px;">
                    <?= LikeLink::widget(['object' => $interaction]) ?>
                     &middot;
                    <?= CommentLink::widget(['object' => $interaction]) ?>
                </span>
                <?= \humhub\widgets\Button::defaultType('Bearbeiten')
                    ->icon('fa-pencil')
                    ->xs()
                    ->action('ui.modal.load', \humhub\modules\content\helpers\ContentContainerHelper::getCurrent()->createUrl('/crm/interaction/edit', ['id' => $interaction->id]))
                ?>
                <!-- TODO: URL/Benachrichtigung/Stream Fixen-->
            </div>

            <div class="wall-entry-comments" style="margin-top: 15px; background-color: #f9f9f9; padding: 10px; border-radius: 4px;">
                <?= Comments::widget(['object' => $interaction]) ?>
            </div>

        </div>
    </div>
</div>
