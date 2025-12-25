<?php

use app\modules\crm\models\Contact;
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

$collapseClass = $startCollapsed ? 'collapse' : 'collapse in';
$ariaExpanded = $startCollapsed ? 'false' : 'true';
?>

<style>
    .panel-heading[aria-expanded="true"] .contact-toggle-icon {
        transform: rotate(180deg);
    }

    .contact-toggle-icon {
        transition: transform 0.3s ease;
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
                        <strong><?= Html::encode($contact->organization->name) ?></strong> •
                    <?php endif; ?>

                    <?php if (!$isStream): ?>
                        <i class="fa fa-comment-o" title="Kommentare"></i> <?= $commentCount ?>
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
                        <strong class="text-danger"><i class="fa fa-arrow-right"></i> ID: <?= Html::encode($contact->id) ?></strong>
                    <?php endif?>
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
                    <?= Html::encode($contact->roles ?? '-') ?>
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
