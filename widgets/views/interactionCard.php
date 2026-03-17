<?php

use humhub\modules\crm\models\Interaction;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\widgets\Button;
use yii\helpers\Html;
use humhub\modules\like\widgets\LikeLink;
use humhub\modules\comment\widgets\CommentLink;
use humhub\modules\comment\widgets\Comments;
use humhub\modules\comment\models\Comment;

/* @var $interaction humhub\modules\crm\models\Interaction */
/* @var $isStream bool "is this Card inside the global/space Stream or inside the Module/Interaction Dashboard?" */
/* @var $startCollapsed bool "is this Card initially collapsed when opened rendered?" */

$statusClass = 'badge bg-secondary'; // default badge
$borderClass = 'border-left: 4px solid #ccc;';

switch ($interaction->status) {
    case Interaction::STATUS_PLANNED:
        $statusClass = 'badge backgroundInfo';
        $borderClass = 'border-left: 4px solid #17a2b8;';
        break;
    case Interaction::STATUS_OVERDUE:
        $statusClass = 'badge bg-danger';
        $borderClass = 'border-left: 4px solid #d9534f;';
        break;
    case Interaction::STATUS_CANCELLED:
        $statusClass = 'badge bg-warning';
        $borderClass = 'border-left: 4px solid #FFC107;';
        break;
    case Interaction::STATUS_DONE:
        $statusClass = 'badge bg-success';
        $borderClass = 'border-left: 4px solid #97d271;';
        break;
}

// generate unique ID for collapse
$collapseId = 'interaction-collapse-' . $interaction->id;
$commentCount = Comment::getCommentCount(Interaction::class, $interaction->id);

// logic for inititial collapse state
$collapseClass = $startCollapsed ? 'collapse' : 'collapse show';
$ariaExpanded = $startCollapsed ? 'false' : 'true';

$hasLinks = !empty($interaction->externalLinks);
$hasTopics = !$isStream && !empty($interaction->content->getTags(humhub\modules\topic\models\Topic:: class)->count());

// calc Columnwidth for 1st row (3 rows + optional topics)
$row1Columns = 3; // Status, Zeitpunkt, Kanal
if ($hasTopics) $row1Columns = 4;
$row1ColClass = $row1Columns == 3 ? 'col-sm-4' : 'col-sm-3';

// calc Columnwidth for 2nd row (3 rows + optional links)
$row2Columns = 3; // Verantwortliche, Kontakte, Organisationen
if ($hasLinks) $row2Columns = 4;
$row2ColClass = $row2Columns == 3 ? 'col-sm-4' : 'col-sm-3';
?>

<style>
    /* rotate arrow animation when card is opened */
    .card-header[aria-expanded="true"] .interaction-toggle-icon {
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

<div class="card mb-2" style="<?= $borderClass ?>">
    <!-- Header -->
    <div class="card-header" role="button" data-bs-toggle="collapse" href="#<?= $collapseId ?>"
         aria-expanded="<?= $ariaExpanded ?>"
         style="background-color: #fff; cursor: pointer;">
        <div class="d-flex">
            <div class="flex-shrink-0">
                <i class="fa fa-comments-o fa-3x text-info-emphasis" style="margin-top: 5px; margin-right: 10px"></i>
            </div>
            <div class="flex-grow-1">
                <h4 class="media-heading" style="font-size: 16px; font-weight: 600; position: relative;">
                    <?= Html::encode($interaction->title) ?>

                    <!-- Quality Indicator with better explanation -->
                    <span
                        style="margin-left: 5px;"
                        title="Data Quality:  <?= $interaction->getQualityScore() ?>% - Vollständigkeit erfasster Daten"
                        data-bs-toggle="tooltip">
            <i class="fa fa-tachometer" style="color: <?= $interaction->getQualityColor() ?>; font-size: 14px;"></i>
            <span style="font-size: 11px; color: #999;"><?= $interaction->getQualityScore() ?>%</span>
            </span>

                    <i class="fa fa-angle-down float-end text-muted interaction-toggle-icon"></i>
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

                    <span title="Verantwortliche Nutzer"><i
                            class="fa fa-user-circle"></i> <?= count($interaction->responsibleUsers) ?></span> •
                    <span title="Kontaktpersonen"><i
                            class="fa fa-users"></i> <?= count($interaction->contacts) ?></span> •
                    <span title="Organisationen"><i
                            class="fa fa-building-o"></i> <?= count($interaction->organizations) ?></span>

                    <?php if (!$isStream): ?>
                        • <i class="fa fa-comment-o" title="Kommentare"></i> <?= $commentCount ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- body -->
    <div id="<?= $collapseId ?>" class="<?= $collapseClass ?>">
        <div class="card-body bg-white" style="border-top: 1px solid #eee;">

            <!-- info row -->
            <div class="row" style="margin-bottom: 15px;">
                <div class="<?= $row1ColClass ?>">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Status</small><br>
                    <span class="<?= $statusClass ?>"><i
                            class="fa fa-flag"></i> <?= $interaction->status ?></span>
                </div>
                <div class="<?= $row1ColClass ?>">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Zeitpunkt</small><br>
                    <strong><?= Yii::$app->formatter->asDate($interaction->date, 'php:d.m.Y') ?></strong>
                    <?php if ($interaction->time): ?>
                        <small>(<?= Html::encode($interaction->time) ?>)</small>
                    <?php endif; ?>
                </div>
                <div class="<?= $row1ColClass ?>">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Kanal</small><br>
                    <strong><i class="fa fa-share-alt"></i> <?= Html::encode($interaction->channel ?? '-') ?></strong>
                </div>
                <?php if ($hasTopics): ?>
                    <div class="<?= $row1ColClass ?>">
                        <?php if (!$isStream && !empty($interaction->content->getTags(humhub\modules\topic\models\Topic::class)->count())): ?>
                            <small class="text-muted text-uppercase" style="font-size: 10px;">Themen</small><br>
                            <?php foreach ($interaction->content->getTags(humhub\modules\topic\models\Topic::class)->all() as $topic): ?>
                                <span class="badge bg-secondary"
                                      style="margin-right: 2px;"><?= \yii\helpers\Html::encode($topic->name) ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- description -->
            <div style="margin-bottom: 15px;">
                <strong>Beschreibung</strong>
                <div class="bg-white border p-2 rounded" style="margin-top: 5px;">
                    <div class="crm-richtext-container" id="desc-container-<?= $interaction->id ?>">
                        <?php if (!empty($interaction->description)): ?>
                            <?= RichText::output($interaction->description) ?>
                        <?php else: ?>
                            <em class="text-muted">Keine Beschreibung hinterlegt.</em>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($interaction->status === Interaction::STATUS_DONE && !empty($interaction->result)): ?>
                <div style="margin-bottom: 15px;">
                    <strong>Ergebnis</strong>
                    <div class="bg-white border p-2 rounded" style="background: #eaffea !important; border-color: #c3e6cb !important; margin-top: 5px;">
                        <div class="crm-richtext-container" id="res-container-<?= $interaction->id ?>">
                            <?= \humhub\modules\content\widgets\richtext\RichText::output($interaction->result) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row" style="margin-top: 20px; border-top: 1px dashed #eee; padding-top: 15px;">

                <div class="<?= $row2ColClass ?>">
                    <strong style="color: #17a2b8;"><i class="fa fa-user-circle"></i> Verantwortliche Nutzer</strong>
                    <?php if (empty($interaction->responsibleUsers)): ?>
                        <div class="text-muted small" style="margin-top:5px;">-</div>
                    <?php else: ?>
                        <ul class="list-unstyled" style="margin-top: 10px; font-size: 12px;">
                            <?php foreach ($interaction->responsibleUsers as $user): ?>
                            <a href="<?= $user->getUrl() ?>">
                                <li style="margin-bottom: 8px; display: flex; align-items: center;">
                                    <a href="<?= $user->getUrl() ?>">
                                        <img src="<?= $user->getProfileImage()->getUrl() ?>" class="rounded"
                                             style="width: 24px; height: 24px; margin-right: 8px;"
                                             alt="<?= Html::encode($user->displayName) ?>"/>
                                    </a>
                                    <div style="line-height: 1.2;">
                                        <a href="<?= $user->getUrl() ?>">
                                            <strong><?= Html::encode($user->displayName) ?></strong>
                                            <br>
                                            <span class="text-muted" style="font-size: 10px;">
                                            <?= Html::encode($user->profile->title ?? 'Mitglied') ?>
                                        </span>
                                        </a>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="<?= $row2ColClass ?>">
                    <strong style="color: #17a2b8;"><i class="fa fa-users"></i> Kontakte</strong>

                    <?php
                    // get IDs of orgs that have still-existing contact-entries assigned to themselves
                    // => necessary to determine and display the ones that have deleted contacts
                    $activeOrgIds = [];
                    foreach ($interaction->contacts as $contact) {
                        if ($contact->organization_id) {
                            $activeOrgIds[] = $contact->organization_id;
                        }
                    }
                    ?>

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

                        <?php foreach ($interaction->organizations as $org): ?>
                            <?php if (!in_array($org->id, $activeOrgIds)): ?>
                                <li style="opacity: 0.6;">
                                    <div class="icon-col text-muted"><i class="fa fa-user-times"></i></div>
                                    <div class="content-col">
                                        <strong class="text-muted" style="font-style: italic;">Gelöschter
                                            Kontakt</strong>
                                        <small class="text-muted">Ehemals: <?= Html::encode($org->name) ?></small>
                                    </div>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <?php if (empty($interaction->contacts) && empty($interaction->organizations)): ?>
                            <li class="text-muted small">-</li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="<?= $row2ColClass ?>">
                    <strong style="color: #17a2b8;"><i class="fa fa-building-o"></i> Organisationen</strong>
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

                <?php if ($hasLinks): ?>
                    <div class="<?= $row2ColClass ?>">
                        <strong style="color: #17a2b8;"><i class="fa fa-link"></i> Links</strong>
                        <?php if (empty($interaction->externalLinks)): ?>
                            <div class="text-muted small" style="margin-top:5px;">-</div>
                        <?php else: ?>
                            <ul class="crm-related-list" style="margin-top: 10px;">
                                <?php foreach ($interaction->externalLinks as $link): ?>
                                    <li>
                                        <div class="icon-col"><i class="fa fa-external-link-square"></i></div>
                                        <div class="content-col">
                                            <strong class="text-link">
                                                <a href="<?= Html::encode($link->url) ?>" target="_blank"
                                                   rel="noopener noreferrer">
                                                    <?= Html::encode($link->url) ?>
                                                </a>
                                            </strong>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>


            </div>

            <div class="text-end" style="margin-top: 20px; padding-top: 10px; border-top: 1px dashed #eee;">

                <?php if (!$isStream): ?>
                    <span style="margin-right: 15px;">
                        <?= LikeLink::widget(['object' => $interaction]) ?>
                         &middot;
                        <?= CommentLink::widget(['object' => $interaction]) ?>
                    </span>
                <?php endif; ?>

                <?php if ($interaction->canEdit()):
                    echo Button::defaultType('Bearbeiten')
                        ->icon('fa-pencil')
                        ->xs()
                        ->action('ui.modal.load', $interaction->content->container->createUrl('/crm/interaction/edit', ['id' => $interaction->id]))
                    ?>
                <?php endif; ?>
                <?php if ($interaction->canDelete()): ?>
                    <?= Button::danger()
                        ->icon('fa-trash')
                        ->xs()
                        ->action('ui.modal.load', $interaction->content->container->createUrl('/crm/interaction/delete', ['id' => $interaction->id]))->confirm(
                            'Interaktion löschen',
                            'Möchtest du diese Interaktion wirklich unwiderruflich löschen? Alle Verknüpfungen zu ihr gehen verloren.',
                            'Löschen',
                            'Abbrechen') ?>
                <?php endif; ?>

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

