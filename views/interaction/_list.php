<?php

use humhub\widgets\Button;
use yii\helpers\Html;
use humhub\widgets\Label;
use app\modules\crm\models\Interaction;
use humhub\widgets\LinkPager;
use humhub\modules\user\widgets\Image as UserImage;

/* @var $interactions app\modules\crm\models\Interaction[] */
/* @var $pagination yii\data\Pagination */
?>

<?php if (empty($interactions)): ?>
    <div class="alert alert-info">Keine Interaktionen gefunden.</div>
<?php else: ?>
    <table class="table table-hover">
        <thead>
        <tr>
            <th>Titel</th>
            <th>Datum</th>
            <th>Status</th>
            <th>Kanal</th>
            <th class="text-left">Betroffen:</th>
            <th>Verantwortlich</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($interactions as $interaction): ?>
            <tr>
                <td style="vertical-align: middle;">
                    <span title="Qualität: <?= $interaction->getQualityScore() ?>%"
                          style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 10px; background-color: <?= $interaction->getQualityColor() ?>;">
                    </span>
                    <a href="#" data-action-click="ui.modal.load"
                       data-action-url="<?= $interaction->content->container->createUrl('/crm/interaction/view', ['id' => $interaction->id]) ?>">
                        <strong><?= Html::encode($interaction->title) ?></strong>
                    </a>
                </td>
                <td style="vertical-align: middle;">
                    <?= Yii::$app->formatter->asDate($interaction->date) ?>
                </td>
                <td style="vertical-align: middle;">
                    <?php
                    switch ($interaction->status) {
                        case Interaction::STATUS_DONE:
                            echo Label::success(Html::encode($interaction->status))->icon('fa fa-flag');
                            break;
                        case Interaction::STATUS_PLANNED:
                            echo Label::info(Html::encode($interaction->status))->icon('fa fa-flag');
                            break;
                        case Interaction::STATUS_OVERDUE:
                            echo Label::danger(Html::encode($interaction->status))->icon('fa fa-flag');
                            break;
                        case Interaction::STATUS_CANCELLED:
                            echo Label::warning(Html::encode($interaction->status))->icon('fa fa-flag');
                    }
                    ?>
                </td>
                <td style="vertical-align: middle;">
                    <?php if (empty($interaction->channel)): ?>

                    <?php else: ?>
                        <span class="label label-default">
                            <?= Html::encode($interaction->channel) ?>
                    </span>

                    <?php endif ?>
                </td>

                <td style="vertical-align: middle;" class="text-left">
                    <i class="fa fa-users text-muted"></i> <?= count($interaction->contacts) ?> <i
                        class="fa fa-long-arrow-right"></i>
                    <i class="fa fa-building text-muted"></i> <?= count($interaction->organizations) ?>
                </td>

                <td style="vertical-align: middle;">
                    <?php foreach ($interaction->responsibleUsers as $user): ?>
                        <?= UserImage::widget(['user' => $user, 'width' => 24, 'showTooltip' => true]) ?>
                    <?php endforeach; ?>
                </td>

                <td class="text-right" style="vertical-align: middle;">
                    <?php if ($interaction->canEdit()): ?>
                        <?= Button::primary()
                            ->icon('fa-pencil')
                            ->xs()
                            ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/interaction/edit', ['id' => $interaction->id]))
                        ?>
                    <?php endif; ?>
                    <?php if ($interaction->canDelete()): ?>
                        <?= Button::danger()
                            ->icon('fa-trash')
                            ->xs()
                            ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/interaction/delete', ['id' => $interaction->id]))->confirm(
                                'Interaktion löschen',
                                'Möchtest du diese Interaktion wirklich unwiderruflich löschen? Alle Verknüpfungen zu ihr gehen verloren.',
                                'Löschen',
                                'Abbrechen') ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-center">
        <?= LinkPager::widget(['pagination' => $pagination]) ?>
    </div>
<?php endif; ?>
