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

    .crm-related-list {
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }

    .crm-related-list li {
        margin-bottom: 8px;
        padding-bottom: 8px;
        border-bottom: 1px dashed #eee;
        font-size: 12px;
        display: flex;
        align-items: flex-start;
    }

    .crm-related-list li:last-child {
        border-bottom: none;
    }

    .crm-related-list .icon-col {
        width: 20px;
        text-align: center;
        margin-right: 8px;
        color: #17a2b8; /* Event/Info Blue */
        font-size: 14px;
        margin-top: 2px;
    }

    .crm-related-list .content-col {
        flex: 1;
    }

    .crm-related-list strong {
        display: block;
        color: #333;
        font-size: 13px;
    }

    .crm-related-list small {
        color: #999;
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
                    <?php if (!$isStream && !empty($event->content->getTags(humhub\modules\topic\models\Topic::class)->count())): ?>
                        <small class="text-muted text-uppercase" style="font-size: 10px;">Themen</small><br>
                        <?php foreach ($event->content->getTags(humhub\modules\topic\models\Topic::class)->all() as $topic): ?>
                            <span class="label label-default" style="margin-right: 2px;"><?= \yii\helpers\Html::encode($topic->name) ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
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

            <div class="row" style="margin-top: 20px; border-top: 1px dashed #eee; padding-top: 15px;">

                <div class="col-sm-4">
                    <strong style="color: #17a2b8;"><i class="fa fa-users"></i> Teilnehmende Kontakte</strong>
                    <?php if (empty($event->contacts)): ?>
                        <div class="text-muted small" style="margin-top:5px;">-</div>
                    <?php else: ?>
                        <ul class="crm-related-list" style="margin-top: 10px;">
                            <?php foreach ($event->contacts as $contact): ?>
                                <li>
                                    <div class="icon-col"><i class="fa fa-user"></i></div>
                                    <div class="content-col">
                                        <strong>
                                            <a href="#" data-action-click="ui.modal.load"
                                               data-action-url="<?= $contact->content->container->createUrl('/crm/contact/view', ['id' => $contact->id]) ?>">
                                                <?= Html::encode($contact->name) ?>
                                            </a>
                                        </strong>
                                        <small><?= $contact->organization ? Html::encode($contact->organization->name) : '-' ?></small>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="col-sm-4">
                    <strong style="color: #17a2b8;"><i class="fa fa-building-o"></i> Betroffene Organisationen</strong>
                    <?php if (empty($event->organizations)): ?>
                        <div class="text-muted small" style="margin-top:5px;">-</div>
                    <?php else: ?>
                        <ul class="crm-related-list" style="margin-top: 10px;">
                            <?php foreach ($event->organizations as $org): ?>
                                <li>
                                    <div class="icon-col"><i class="fa fa-building-o"></i></div>
                                    <div class="content-col">
                                        <strong>
                                            <a href="#" data-action-click="ui.modal.load"
                                               data-action-url="<?= $org->content->container->createUrl('/crm/organization/view', ['id' => $org->id]) ?>">
                                                <?= Html::encode($org->name) ?>
                                            </a>
                                        </strong>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="col-sm-4">
                    <strong style="color: #17a2b8;"><i class="fa fa-link"></i> Links</strong>
                    <?php if (empty($event->externalLinks)): ?>
                        <div class="text-muted small" style="margin-top:5px;">-</div>
                    <?php else: ?>
                        <ul class="crm-related-list" style="margin-top: 10px;">
                            <?php foreach ($event->externalLinks as $link): ?>
                                <li>
                                    <div class="icon-col"><i class="fa fa-external-link-square"></i></div>
                                    <div class="content-col">
                                        <strong class="text-link">
                                            <a href="<?= Html::encode($link->url) ?>" target="_blank" rel="noopener noreferrer">
                                                <?= Html::encode($link->url) ?>
                                            </a>
                                        </strong>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
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
