<?php

use app\modules\crm\models\Interaction;
use yii\helpers\Html;
use humhub\modules\like\widgets\LikeLink;
use humhub\modules\comment\widgets\CommentLink;
use humhub\modules\comment\widgets\Comments;
use humhub\modules\comment\models\Comment;

/* @var $interaction app\modules\crm\models\Interaction */
/* @var $isStream bool "is this Card inside the global/space Stream or inside the Module/Interaction Dashboard?" */
/* @var $startCollapsed bool "is this Card initially collapsed when opened rendered?" */

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

// logic for inititial collapse state
$collapseClass = $startCollapsed ? 'collapse' : 'collapse in';
$ariaExpanded = $startCollapsed ? 'false' : 'true';
?>

<style>
    /* rotate arrow animation when card is opened */
    .panel-heading[aria-expanded="true"] .interaction-toggle-icon {
        transform: rotate(180deg);
    }

    .interaction-toggle-icon {
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
        color: #17a2b8; /* Interaction Blue */
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
    <!-- Header -->
    <div class="panel-heading" role="button" data-toggle="collapse" href="#<?= $collapseId ?>"
         aria-expanded="<?= $ariaExpanded ?>"
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

                    <?php if (!$isStream): ?>
                        <a href="<?= $interaction->content->createdBy->getUrl() ?>" class="text-muted">
                            <?= Html::encode($interaction->content->createdBy->displayName ?? 'System') ?>
                        </a> •

                        <?= Yii::$app->formatter->asRelativeTime($interaction->content->created_at) ?> •
                    <?php endif; ?>

                    <i class="fa fa-users" title="Kontaktpersonen"></i> <?= count($interaction->contacts) ?> •
                    <i class="fa fa-user"
                       title="Verantwortliche Nutzer"></i> <?= count($interaction->responsibleUsers) ?>

                    <?php if (!$isStream): ?>
                        • <i class="fa fa-comment-o" title="Kommentare"></i> <?= $commentCount ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- body -->
    <div id="<?= $collapseId ?>" class="panel-collapse <?= $collapseClass ?>">
        <div class="panel-body" style="border-top: 1px solid #eee;">

            <!-- info row -->
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-sm-4">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Status</small><br>
                    <span class="label <?= $statusClass ?>"><?= $interaction->status ?></span>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Zeitpunkt</small><br>
                    <strong><?= Yii::$app->formatter->asDate($interaction->date, 'php:d.m.Y') ?></strong>
                    <?php if ($interaction->time): ?>
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

            <?php if (!$isStream && !empty($interaction->content->getTags(humhub\modules\topic\models\Topic::class)->count())): ?>
                <div style="margin-bottom: 15px;">
                    <i class="fa fa-tags text-muted"></i>
                    <?php foreach ($interaction->content->getTags(humhub\modules\topic\models\Topic::class)->all() as $topic): ?>
                        <span class="label label-default"
                              style="margin-right: 2px;"><?= \yii\helpers\Html::encode($topic->name) ?></span>
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

            <!-- TODO: <a>/Hover-Bereiche vereinheitlichen -->

            <div class="row" style="margin-top: 20px; border-top: 1px dashed #eee; padding-top: 15px;">

                <div class="col-sm-4">
                    <strong style="color: #17a2b8;"><i class="fa fa-user-circle"></i> Verantwortliche Nutzer</strong>
                    <?php if (empty($interaction->responsibleUsers)): ?>
                        <div class="text-muted small" style="margin-top:5px;">-</div>
                    <?php else: ?>
                        <ul class="list-unstyled" style="margin-top: 10px; font-size: 12px;">
                            <?php foreach ($interaction->responsibleUsers as $user): ?>
                            <a href="<?= $user->getUrl() ?>">
                                <li style="margin-bottom: 8px; display: flex; align-items: center;">
                                    <img src="<?= $user->getProfileImage()->getUrl() ?>" class="img-rounded"
                                         style="width: 24px; height: 24px; margin-right: 8px;"
                                         alt="<?= Html::encode($user->displayName) ?>"/>
                                    <div style="line-height: 1.2;">
                                        <strong><?= Html::encode($user->displayName) ?></strong>
                                        <br>
                                        <span class="text-muted" style="font-size: 10px;">
                                            <?= Html::encode($user->profile->title ?? 'Mitglied') ?>
                                        </span>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="col-sm-4">
                    <strong style="color: #17a2b8;"><i class="fa fa-users"></i> Kontaktpersonen</strong>
                    <?php if (empty($interaction->contacts)): ?>
                        <div class="text-muted small" style="margin-top:5px;">-</div>
                    <?php else: ?>
                        <ul class="crm-related-list" style="margin-top: 10px;">
                            <?php foreach ($interaction->contacts as $contact): ?>
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
                    <?php if (empty($interaction->organizations)): ?>
                        <div class="text-muted small" style="margin-top:5px;">-</div>
                    <?php else: ?>
                        <ul class="crm-related-list" style="margin-top: 10px;">
                            <?php foreach ($interaction->organizations as $org): ?>
                                <li>
                                    <div class="icon-col"><i class="fa fa-building-o"></i></div>
                                    <div class="content-col">
                                        <strong>
                                            <a href="#" data-action-click="ui.modal.load"
                                               data-action-url="<?= $org->content->container->createUrl('/crm/organization/view', ['id' => $org->id]) ?>">
                                                <?= Html::encode($org->name) ?>
                                            </a>
                                        </strong>
                                        <small><?= $org->hasAttribute('city') ? Html::encode($org->city) : '' ?></small>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

            </div>

            <div class="text-right" style="margin-top: 20px; padding-top: 10px; border-top: 1px dashed #eee;">

                <?php if (!$isStream): ?>
                    <span style="margin-right: 15px;">
                        <?= LikeLink::widget(['object' => $interaction]) ?>
                         &middot;
                        <?= CommentLink::widget(['object' => $interaction]) ?>
                    </span>
                <?php endif; ?>

                <?php
                echo \humhub\widgets\Button::defaultType('Bearbeiten')
                    ->icon('fa-pencil')
                    ->xs()
                    ->action('ui.modal.load', $interaction->content->container->createUrl('/crm/interaction/edit', ['id' => $interaction->id]))
                ?>
            </div>

            <?php if (!$isStream): ?>
                <div class="wall-entry-comments"
                     style="margin-top: 15px; background-color: #f9f9f9; padding: 10px; border-radius: 4px;">
                    <?= Comments::widget(['object' => $interaction]) ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

