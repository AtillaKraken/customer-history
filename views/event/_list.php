<?php

use humhub\widgets\Button;
use yii\helpers\Html;
use humhub\widgets\LinkPager;

/* @var $events app\modules\crm\models\Event[] */
/* @var $pagination yii\data\Pagination */
?>

<?php if (empty($events)): ?>
    <div class="alert alert-info">Keine Veranstaltungen gefunden.</div>
<?php else: ?>
    <table class="table table-hover">
        <thead>
        <tr>
            <th>Titel</th>
            <th>Datum</th>
            <th>Typ</th>
            <th>Links</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($events as $event): ?>
            <tr>
                <td>
                    <a href="#" data-action-click="ui.modal.load" data-action-url="<?= $event->content->container->createUrl('/crm/event/view', ['id' => $event->id]) ?>">
                        <strong><?= Html::encode($event->title) ?></strong>
                    </a>
                </td>
                <td>
                    <?= Yii::$app->formatter->asDate($event->date) ?>
                    <?php if($event->time): ?>
                        <small class="text-muted"><?= Html::encode($event->time) ?> Uhr</small>
                    <?php endif; ?>
                </td>
                <td>
                    <?php
                    $types = \app\modules\crm\models\Event::getTypeOptions();
                    echo Html::encode($types[$event->type] ?? $event->type);
                    ?>
                </td>
                <td><?= Html::encode($event->links) ?></td>
                <td class="text-right">
                    <?= Button::primary()
                        ->icon('fa-pencil')
                        ->xs()
                        ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/event/edit', ['id' => $event->id]))
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
