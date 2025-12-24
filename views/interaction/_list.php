<?php

use humhub\widgets\Button;
use yii\helpers\Html;
use humhub\widgets\Label;
use app\modules\crm\models\Interaction;
use humhub\widgets\LinkPager;

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
            <th>Verantwortlich</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($interactions as $interaction): ?>
            <tr>
                <td>
                    <a href="#" data-action-click="ui.modal.load"
                       data-action-url="<?= $interaction->content->container->createUrl('/crm/interaction/view', ['id' => $interaction->id]) ?>">
                        <strong><?= Html::encode($interaction->title) ?></strong>
                    </a>
                </td>
                <td><?= Yii::$app->formatter->asDate($interaction->date) ?></td>
                <td>
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
                <td><?= Html::encode($interaction->channel) ?></td>
                <td>
                    <span class="badge"><?= count($interaction->responsibleUsers) ?></span>
                </td>
                <td class="text-right">
                    <?= Button::primary()
                        ->icon('fa-pencil')
                        ->xs()
                        ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/interaction/edit', ['id' => $interaction->id]))
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-center">
        <?= LinkPager::widget(['pagination' => $pagination]) ?>
    </div>
<?php endif; ?>
