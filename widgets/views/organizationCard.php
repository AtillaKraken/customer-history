<?php

use humhub\modules\crm\models\Organization;
use humhub\modules\crm\models\Interaction;
use humhub\modules\crm\models\Event;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\widgets\Button;
use yii\helpers\Html;
use humhub\modules\like\widgets\LikeLink;
use humhub\modules\comment\widgets\CommentLink;
use humhub\modules\comment\widgets\Comments;
use humhub\modules\comment\models\Comment;

/* @var $organization humhub\modules\crm\models\Organization */
/* @var $isStream bool */
/* @var $startCollapsed bool */

// Design config
$borderClass = 'border-left: 4px solid #17a2b8;';
$collapseId = 'organization-collapse-' . $organization->id;
$commentCount = Comment::getCommentCount(Organization::class, $organization->id);

// Load related data counts for the header stats
$countContacts = $organization->getContacts()->count();
$countInteractions = $organization->getInteractions()->count();
$countEvents = $organization->getParticipations()->count();

$collapseClass = $startCollapsed ? 'collapse' : 'collapse in';
$ariaExpanded = $startCollapsed ? 'false' : 'true';
?>

<style>
    .panel-heading[aria-expanded="true"] .org-toggle-icon {
        transform: rotate(180deg);
    }

    .org-toggle-icon {
        transition: transform 0.3s ease;
    }

    /* Styling for the 3-column lists */
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
        color: #777;
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
         aria-expanded="<?= $ariaExpanded ?>" style="background-color: #fff; cursor: pointer;">
        <div class="media">
            <div class="media-left">
                <i class="fa fa-building-o fa-2x text-info" style="margin-top: 5px; margin-right: 10px;"></i>
            </div>
            <div class="media-body">
                <h4 class="media-heading" style="font-size: 16px; font-weight: 600;">
                    <?= Html::encode($organization->name) ?>
                    <i class="fa fa-angle-down pull-right text-muted org-toggle-icon"></i>
                </h4>

                <div class="text-muted" style="font-size: 12px;">
                    <?php if (!$isStream): ?>
                        <a href="<?= $organization->content->createdBy->getUrl() ?>" class="text-muted">
                            <?= Html::encode($organization->content->createdBy->displayName ?? 'System') ?>
                        </a> •
                        <?= Yii::$app->formatter->asRelativeTime($organization->content->created_at) ?> •
                    <?php endif; ?>

                    <span title="Verantwortliche Nutzer"><i class="fa fa-user-circle"></i> <?= count($organization->responsibleUsers) ?></span> •
                    <span title="Kontakte"><i class="fa fa-users"></i> <?= $countContacts ?></span> •
                    <span title="Interaktionen"><i class="fa fa-comments-o"></i> <?= $countInteractions ?></span> •
                    <span title="Veranstaltungen"><i class="fa fa-calendar"></i> <?= $countEvents ?></span>

                    <?php if (!$isStream): ?>
                        • <i class="fa fa-comment-o" title="Kommentare"></i> <?= $commentCount ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="<?= $collapseId ?>" class="panel-collapse <?= $collapseClass ?>">
        <div class="panel-body" style="border-top: 1px solid #eee;">

            <div class="row" style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                <div class="col-sm-3">
                    <small class="text-muted text-uppercase"><i class="fa fa-info-circle"></i> Kategorie</small><br>
                    <strong><?= $organization->hasAttribute('category') ? Html::encode($organization->category) : '-' ?></strong>
                </div>
                <div class="col-sm-3">
                    <small class="text-muted text-uppercase"><i class="fa fa-info-circle"></i> Branche</small><br>
                    <strong><?= $organization->hasAttribute('industry') ? Html::encode($organization->industry) : '-' ?></strong>
                </div>
                <div class="col-sm-3">
                    <small class="text-muted text-uppercase"><i class="fa fa-map-marker"></i> Ort</small><br>
                    <strong><?= $organization->hasAttribute('city') ? Html::encode($organization->city) : '-' ?></strong>
                </div>
                <div class="col-sm-3">
                    <small class="text-muted text-uppercase"><i class="fa fa-users"></i> Größe</small><br>
                    <strong><?= $organization->hasAttribute('size') ? Html::encode($organization->size) . " Mitarbeitende" : '-' ?></strong>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <strong style="font-size: 14px;">Notiz</strong>
                <div class="well well-sm"
                     style="background: #fff; border: 1px solid #ddd; margin-top: 5px; font-size: 13px;">
                    <?php if (!empty($organization->notes)): ?>
                        <?= RichText::output($organization->notes) ?>
                    <?php else: ?>
                        <em class="text-muted">Keine Notizen hinterlegt.</em>
                    <?php endif; ?>
                </div>
            </div>


            <!-- TODO: ALLE ICONS DURCHGEHEN UND KONSISTENZ CHECKEN-->

            <div class="row">
                <div class="col-sm-3">
                    <strong style="color: #17a2b8"><i class="fa fa-user-circle"></i> Verantwortliche Nutzer</strong>
                    <?php if (empty($organization->responsibleUsers)): ?>
                        <div class="text-muted small" style="margin-top:5px;">-</div>
                    <?php else: ?>
                        <ul class="list-unstyled" style="margin-top: 10px; font-size: 12px;">
                            <?php foreach ($organization->responsibleUsers as $user): ?>
                                <li style="margin-bottom: 8px; display: flex; align-items: center;">
                                    <a href="<?= $user->getUrl() ?>">
                                        <img src="<?= $user->getProfileImage()->getUrl() ?>" class="img-rounded"
                                             style="width: 24px; height: 24px; margin-right: 8px;"
                                             alt="<?= Html::encode($user->displayName) ?>"/>
                                    </a>
                                    <div style="line-height: 1.2;">
                                        <a href="<?= $user->getUrl() ?>" style="color: inherit;">
                                            <strong><?= Html::encode($user->displayName) ?></strong>
                                        </a><br>
                                        <span class="text-muted" style="font-size: 10px;">
                                            <?= Html::encode($user->profile->title ?? 'Mitglied') ?>
                                        </span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="col-sm-3">
                    <strong style="color: #17a2b8;"><i class="fa fa-users"></i> Kontaktpersonen (<?= $countContacts ?>)</strong>
                    <ul class="crm-related-list" style="margin-top: 10px;">
                        <?php foreach ($organization->contacts as $contact): ?>
                            <li>
                                <div class="icon-col"><i class="fa fa-user"></i></div>
                                <div class="content-col">
                                    <strong>
                                        <a href="#" data-action-click="ui.modal.load"
                                           data-action-url="<?= $contact->content->container->createUrl('/crm/contact/view', ['id' => $contact->id]) ?>">
                                            <?php if (empty($contact->name)): ?>
                                                <span class="text-danger">
                                                    <?= Html::encode($contact->getDisplayName()) ?>
                                                </span>
                                            <?php else: ?>
                                                <?= Html::encode($contact->getDisplayName()) ?>
                                            <?php endif; ?>
                                        </a>
                                    </strong>
                                    <small><?= $contact->hasAttribute('roles') ? Html::encode($contact->roles) : 'Keine Rollen hinterlegt'; ?></small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        <?php if ($countContacts == 0): ?>
                            <li class="text-muted">Keine Kontakte</li> <?php endif; ?>
                    </ul>
                </div>

                <div class="col-sm-3">
                    <strong style="color: #17a2b8;"><i class="fa fa-comments-o"></i> Interaktionen
                        (<?= $countInteractions ?>)</strong>
                    <ul class="crm-related-list" style="margin-top: 10px;">
                        <?php foreach ($organization->interactions as $interaction): ?>
                            <li>
                                <div class="icon-col"><i class="fa fa-comment-o"></i></div>
                                <div class="content-col">
                                    <strong>
                                        <a href="#" data-action-click="ui.modal.load"
                                           data-action-url="<?= $interaction->content->container->createUrl('/crm/interaction/view', ['id' => $interaction->id]) ?>">
                                            <?= Html::encode($interaction->title) ?>
                                        </a>
                                    </strong>
                                    <small>
                                        <?= Yii::$app->formatter->asDate($interaction->date, 'php:d.m.y') ?>
                                        &middot; Status: <?= $interaction->status ?>
                                    </small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        <?php if ($countInteractions == 0): ?>
                            <li class="text-muted">Keine Interaktionen</li> <?php endif; ?>
                    </ul>
                </div>

                <div class="col-sm-3">
                    <strong style="color: #17a2b8;"><i class="fa fa-calendar"></i> Teilnahmen (<?= $countEvents ?>)</strong>
                    <ul class="crm-related-list" style="margin-top: 10px;">
                        <?php foreach ($organization->participations as $event): ?>
                            <li>
                                <div class="icon-col"><i class="fa fa-calendar-check-o"></i></div>
                                <div class="content-col">
                                    <strong>
                                        <a href="#" data-action-click="ui.modal.load"
                                           data-action-url="<?= $event->content->container->createUrl('/crm/event/view', ['id' => $event->id]) ?>">
                                            <?= Html::encode($event->title) ?>
                                        </a>
                                    </strong>
                                    <small>
                                        <?= Yii::$app->formatter->asDate($event->date, 'php:d.m.y') ?>
                                    </small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        <?php if ($countEvents == 0): ?>
                            <li class="text-muted">Keine Teilnahmen</li> <?php endif; ?>
                    </ul>
                </div>

            </div>

            <div class="text-right" style="margin-top: 20px; padding-top: 10px; border-top: 1px dashed #eee;">
                <?php if (!$isStream): ?>
                    <span style="margin-right: 15px;">
                        <?= LikeLink::widget(['object' => $organization]) ?> &middot;
                        <?= CommentLink::widget(['object' => $organization]) ?>
                    </span>
                <?php endif; ?>

                <?php if ($organization->canEdit()): ?>
                <?= Button::defaultType('Bearbeiten')
                    ->icon('fa-pencil')->xs()
                    ->action('ui.modal.load', $organization->content->container->createUrl('/crm/organization/edit', ['id' => $organization->id])) ?>
                <?php endif; ?>
                <?php if ($organization->canDelete()): ?>
                    <?= Button::danger()
                        ->icon('fa-trash')
                        ->xs()
                        ->action('ui.modal.load', $organization->content->container->createUrl('/crm/organization/delete', ['id' => $organization->id]))->confirm(
                            'Organisation löschen',
                            'Möchtest du diese Organisation wirklich unwiderruflich löschen? Alle zugehörigen Kontakte sowie Verweise auf diese werden dadurch mitgelöscht!',
                            'Löschen',
                            'Abbrechen' )
                    ?>
                <?php endif; ?>
            </div>

            <?php if (!$isStream): ?>
                <div class="wall-entry-comments"
                     style="margin-top: 15px; background-color: #f9f9f9; padding: 10px; border-radius: 4px;">
                    <?= Comments::widget(['object' => $organization]) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
