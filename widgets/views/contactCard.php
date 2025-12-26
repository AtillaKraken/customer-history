<?php

use app\modules\crm\models\Contact;
use app\modules\crm\models\Interaction;
use humhub\modules\content\widgets\richtext\RichText;
use yii\helpers\Html;
use humhub\modules\like\widgets\LikeLink;
use humhub\modules\comment\widgets\CommentLink;
use humhub\modules\comment\widgets\Comments;
use humhub\modules\comment\models\Comment;

/* @var $contact app\modules\crm\models\Contact */
/* @var $isStream bool */
/* @var $startCollapsed bool */

$borderClass = 'border-left: 4px solid #ff8d00;'; // Orange für Kontakte
$collapseId = 'contact-collapse-' . $contact->id;
$commentCount = Comment::getCommentCount(Contact::class, $contact->id);
$countEvents = $contact->getParticipations()->count();

$collapseClass = $startCollapsed ? 'collapse' : 'collapse in';
$ariaExpanded = $startCollapsed ? 'false' : 'true';

// get interactions including this contact
$interactions = Interaction::find()
    ->innerJoinWith('contacts')
    ->where(['crm_contact.id' => $contact->id])
    ->orderBy(['date' => SORT_DESC])
    //->limit(5)
    ->all();

// get all resp users out of those Interactions
$responsibleUsers = [];
foreach ($interactions as $interaction) {
    foreach ($interaction->responsibleUsers as $user) {
        $responsibleUsers[$user->id] = $user;
    }
}
?>

<style>
    .panel-heading[aria-expanded="true"] .contact-toggle-icon {
        transform: rotate(180deg);
    }

    .contact-toggle-icon {
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
        color: #ff8d00;
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
                <i class="fa fa-user-circle-o fa-2x text-warning" style="margin-top: 5px; margin-right: 10px"></i>
            </div>
            <div class="media-body">
                <h4 class="media-heading" style="font-size: 16px; font-weight: 600;">
                    <?php if (empty($contact->name)): ?>
                        <span class="text-danger">
                <?= Html::encode($contact->getDisplayName()) ?>
            </span>
                    <?php else: ?>
                        <?= Html::encode($contact->getDisplayName()) ?>
                    <?php endif; ?>

                    <i class="fa fa-angle-down pull-right text-muted contact-toggle-icon"></i>
                </h4>

                <div class="text-muted" style="font-size: 12px;">
                    <?php if ($contact->organization): ?>
                        <i class="fa fa-building-o"></i>
                        <strong><?= Html::encode($contact->organization->name) ?></strong>
                    <?php endif; ?>

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
                <div class="col-sm-3">
                    <small class="text-muted text-uppercase">Persönliche Daten</small><br>
                    <?php if (empty($contact->name)): ?>
                        <i class="fa fa-warning"></i> <strong>Name fehlt </strong>
                        <strong class="text-danger"><i class="fa fa-long-arrow-right"></i>
                            ID: <?= Html::encode($contact->id) ?></strong>
                    <?php endif ?>
                    <strong><?= Html::encode($contact->name) ?></strong><br>
                    <?php switch ($contact->gender) {
                        case 'male':
                            echo '<i class="fa fa-male"></i>';
                            break;
                        case 'female':
                            echo '<i class="fa fa-female"></i>';
                            break;
                        case 'diverse':
                            echo '<i class="fa fa-genderless"></i>';
                            break;
                        case null:
                            echo '<i class="fa fa-warning"></i> <strong>Geschlecht fehlt</strong>';
                    } ?>
                    <strong><?= Html::encode($contact->gender) ?></strong> <br>

                </div>
                <div class="col-sm-3">
                    <small class="text-muted text-uppercase">Mitglied von</small><br>
                    <?php if ($contact->organization): ?>
                        <i class="fa fa-building-o"></i>
                        <strong><?= Html::encode($contact->organization->name) ?></strong>
                    <?php endif; ?>
                </div>
                <div class="col-sm-3">
                    <small class="text-muted text-uppercase">Rollen / Funktionen</small><br>
                    <?php foreach ($contact->roleList as $role): ?>
                        <span class="label label-default">
                        <?= Html::encode($role) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
                <div class="col-sm-3">
                    <small class="text-muted text-uppercase">Kommunikation</small><br>
                    <?php if ($contact->email): ?>
                        <i class="fa fa-envelope-o"></i> <a
                            href="mailto:<?= Html::encode($contact->email) ?>"><?= Html::encode($contact->email) ?></a>
                        <br>
                    <?php endif; ?>
                    <?php if ($contact->phone_number): ?>
                        <i class="fa fa-phone"></i> <a
                            href="tel:<?= Html::encode($contact->phone_number) ?>"><?= Html::encode($contact->phone_number) ?></a>
                    <?php endif; ?>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <strong style="font-size: 14px;">Notiz</strong>
                <div class="well well-sm"
                     style="background: #fff; border: 1px solid #ddd; margin-top: 5px; font-size: 13px;">
                    <?php if (!empty($contact->note)): ?>
                        <?= RichText::output($contact->note) ?>
                    <?php else: ?>
                        <em class="text-muted">Keine Notizen hinterlegt.</em>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($interactions)): ?>
                <div class="row" style="margin-top: 20px; border-top: 1px dashed #eee; padding-top: 15px;">
                    <div class="col-sm-4">
                        <strong style="color: #ff8d00; margin-bottom: 10px; display:block;">
                            <i class="fa fa-history"></i> Letzte Interaktionen
                        </strong>
                        <ul class="list-unstyled" style="font-size: 12px;">
                            <?php foreach ($interactions as $interaction): ?>
                                <li style="margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid #f0f0f0;">
                                    <div style="font-weight: 600;">
                                        <a href="#" data-action-click="ui.modal.load"
                                           data-action-url="<?= $interaction->content->container->createUrl('/crm/interaction/view', ['id' => $interaction->id]) ?>">
                                            <?= Html::encode($interaction->title) ?>
                                        </a>
                                    </div>
                                    <div class="text-muted">
                                        <i class="fa fa-calendar-o"></i> <?= Yii::$app->formatter->asDate($interaction->date, 'php:d.m.y') ?>
                                        &middot;
                                        <span class="label label-default"
                                              style="font-size: 9px; padding: 1px 4px;"><?= Html::encode($interaction->status) ?></span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="col-sm-4">
                        <strong style="color: #ff8d00; margin-bottom: 10px; display:block;">
                            <i class="fa fa-user-circle"></i> Durchgeführt worden von:
                        </strong>
                        <?php if (empty($responsibleUsers)): ?>
                            <span class="text-muted small">-</span>
                        <?php else: ?>
                            <ul class="list-unstyled" style="font-size: 12px;">
                                <?php foreach ($responsibleUsers

                                as $user): ?>
                                <a href="<?= $user->getUrl() ?>">
                                    <li style="margin-bottom: 8px; display: flex; align-items: center;">
                                        <img src="<?= $user->getProfileImage()->getUrl() ?>" class="img-rounded"
                                             style="width: 24px; height: 24px; margin-right: 8px;"
                                             alt="<?= Html::encode($user->displayName) ?>"/>
                                        <div>
                                            <a href="<?= $user->getUrl() ?>">
                                                <strong><?= Html::encode($user->displayName) ?></strong><br>
                                                <span class="text-muted"
                                                      style="font-size: 10px;"><?= Html::encode($user->profile->title ?? 'Mitglied') ?></span>
                                            </a>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-3">
                        <strong style="color: #ff8d00;"><i class="fa fa-calendar"></i> Teilnahmen (<?= $countEvents ?>)</strong>
                        <ul class="crm-related-list" style="margin-top: 10px;">
                            <?php foreach ($contact->participations as $event): ?>
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
                                <li class="text-muted">Keine erfassten Teilnahmen an Veranstaltungen
                                </li> <?php endif; ?>
                        </ul>
                    </div>

                </div>
            <?php endif; ?>


            <div class="text-right" style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #eee;">
                <?php if (!$isStream): ?>
                    <span style="margin-right: 15px;">
                        <?= LikeLink::widget(['object' => $contact]) ?> &middot;
                        <?= CommentLink::widget(['object' => $contact]) ?>
                    </span>
                <?php endif; ?>

                <?= \humhub\widgets\Button::defaultType('Bearbeiten')
                    ->icon('fa-pencil')->xs()
                    ->action('ui.modal.load', $contact->content->container->createUrl('/crm/contact/edit', ['id' => $contact->id])) ?>
            </div>

            <?php if (!$isStream): ?>
                <div class="wall-entry-comments"
                     style="margin-top: 15px; background-color: #f9f9f9; padding: 10px; border-radius: 4px;">
                    <?= Comments::widget(['object' => $contact]) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
