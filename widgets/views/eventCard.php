<?php

use app\modules\crm\models\Event;
use yii\helpers\Html;
use humhub\modules\like\widgets\LikeLink;
use humhub\modules\comment\widgets\CommentLink;
use humhub\modules\comment\widgets\Comments;
use humhub\modules\comment\models\Comment;

/* @var $event app\modules\crm\models\Event */
/* @var $isStream bool */
/* @var $startCollapsed bool */

$borderClass = 'border-left: 4px solid #6fdbe8;'; // Light blue for events
$collapseId = 'event-collapse-' . $event->id;
$commentCount = Comment::getCommentCount(Event::class, $event->id);

// Logic for start state
$collapseClass = $startCollapsed ? 'collapse' : 'collapse in';
$ariaExpanded = $startCollapsed ? 'false' : 'true';

// Map type constant to readable label
$types = Event::getTypeOptions();
$typeLabel = $types[$event->type] ?? '-';
?>

<style>
    /* Rotate arrow when panel is open */
    .panel-heading[aria-expanded="true"] .event-toggle-icon {
        transform: rotate(180deg);
    }
    .event-toggle-icon {
        transition: transform 0.3s ease;
    }
</style>

<div class="panel panel-default" style="<?= $borderClass ?> margin-bottom: 10px;">
    <div class="panel-heading" role="button" data-toggle="collapse" href="#<?= $collapseId ?>"
         aria-expanded="<?= $ariaExpanded ?>"
         style="background-color: #fff; cursor: pointer;">
        <div class="media">
            <div class="media-left">
                <i class="fa fa-calendar fa-2x text-info" style="margin-top: 5px; margin-right: 10px"></i>
            </div>
            <div class="media-body">
                <h4 class="media-heading" style="font-size: 16px; font-weight: 600;">
                    <?= Html::encode($event->title) ?>
                    <i class="fa fa-angle-down pull-right text-muted event-toggle-icon"></i>
                </h4>

                <div class="text-muted" style="font-size: 12px;">
                    <strong class="text-dark" style="margin-right: 5px;">
                        <?= Yii::$app->formatter->asDate($event->date, 'php:d.m.Y') ?>
                    </strong> •

                    <?php if (!$isStream): ?>
                        <a href="<?= $event->content->createdBy->getUrl() ?>" class="text-muted">
                            <?= Html::encode($event->content->createdBy->displayName ?? 'System') ?>
                        </a> •
                        <?= Yii::$app->formatter->asRelativeTime($event->content->created_at) ?> •
                    <?php endif; ?>

                    <i class="fa fa-users" title="Teilnehmende Kontakte"></i> <?= count($event->contacts) ?>

                    <?php if (!$isStream): ?>
                        • <i class="fa fa-comment-o" title="Kommentare"></i> <?= $commentCount ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="<?= $collapseId ?>" class="panel-collapse <?= $collapseClass ?>">
        <div class="panel-body" style="border-top: 1px solid #eee;">

            <div class="row" style="margin-bottom: 15px;">
                <div class="col-sm-4">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Format</small><br>
                    <span class="label label-info"><?= Html::encode($typeLabel) ?></span>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Zeit</small><br>
                    <strong>
                        <?= Yii::$app->formatter->asDate($event->date, 'php:d.m.Y') ?>
                        <?php if($event->time): ?>
                            (<?= Html::encode($event->time) ?>)
                        <?php endif; ?>
                    </strong>
                </div>
                <div class="col-sm-4">
                </div>
            </div>

            <?php if (!empty($event->description)): ?>
                <div style="margin-bottom: 15px;">
                    <strong>Beschreibung</strong>
                    <div class="well well-sm" style="background: #fff; border: 1px solid #ddd; margin-top: 5px;">
                        <?= \humhub\modules\content\widgets\richtext\RichText::output($event->description) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!$isStream && !empty($event->content->getTags(humhub\modules\topic\models\Topic::class)->count())): ?>
                <div style="margin-bottom: 15px;">
                    <i class="fa fa-tags text-muted"></i>
                    <?php foreach ($event->content->getTags(humhub\modules\topic\models\Topic::class)->all() as $topic): ?>
                        <span class="label label-default" style="margin-right: 2px;"><?= \yii\helpers\Html::encode($topic->name) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($event->externalLinks)): ?>
                <div style="margin-bottom: 15px;">
                    <strong><i class="fa fa-link"></i> Verknüpfte Links</strong>
                    <ul class="list-unstyled" style="margin-top: 5px; padding-left: 10px; border-left: 2px solid #eee;">
                        <?php foreach ($event->externalLinks as $link): ?>
                            <li style="margin-bottom: 3px;">
                                <a href="<?= Html::encode($link->url) ?>" target="_blank" rel="noopener noreferrer">
                                    <i class="fa fa-external-link-square"></i> <?= Html::encode($link->url) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-sm-12">
                    <strong style="color: #17a2b8;"><i class="fa fa-users"></i> Teilnehmende Kontakte</strong>
                    <ul class="list-unstyled" style="margin-top: 5px; font-size: 13px;">
                        <?php foreach ($event->contacts as $contact): ?>
                            <li style="margin-bottom: 4px;">
                                <i class="fa fa-user"></i> <strong><?= Html::encode($contact->name) ?></strong>
                                <?php if ($contact->organization): ?>
                                    <span class="text-muted" style="margin-left: 5px; font-size: 11px;">
                                        (<i class="fa fa-building-o"></i> <?= Html::encode($contact->organization->name) ?>)
                                    </span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="text-right" style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #eee;">

                <?php if (!$isStream): ?>
                    <span style="margin-right: 15px;">
                        <?= LikeLink::widget(['object' => $event]) ?>
                         &middot;
                        <?= CommentLink::widget(['object' => $event]) ?>
                    </span>
                <?php endif; ?>

                <?= \humhub\widgets\Button::defaultType('Bearbeiten')
                    ->icon('fa-pencil')
                    ->xs()
                    ->action('ui.modal.load', $event->content->container->createUrl('/crm/event/edit', ['id' => $event->id]))
                ?>
            </div>

            <?php if (!$isStream): ?>
                <div class="wall-entry-comments" style="margin-top: 15px; background-color: #f9f9f9; padding: 10px; border-radius: 4px;">
                    <?= Comments::widget(['object' => $event]) ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
