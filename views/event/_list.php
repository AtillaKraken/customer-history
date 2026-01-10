<?php

use humhub\widgets\Button;
use yii\helpers\Html;
use humhub\widgets\LinkPager;

/* @var $events humhub\modules\crm\models\Event[] */
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
            <th>Format</th>
            <th>Teilnahmen:</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($events as $event): ?>
            <tr>
                <td style="vertical-align: middle;">
                    <a href="#" data-action-click="ui.modal.load" data-action-url="<?= $event->content->container->createUrl('/crm/event/view', ['id' => $event->id]) ?>">
                        <strong><?= Html::encode($event->title) ?></strong>
                    </a>
                </td>
                <td style="vertical-align: middle;">
                    <?= Yii::$app->formatter->asDate($event->date) ?>
                    <?php if($event->time): ?>
                        <small class="text-muted"><?= Html::encode($event->time) ?> Uhr</small>
                    <?php endif; ?>
                </td>
                <td style="vertical-align: middle;">
                    <?php
                    $types = \humhub\modules\crm\models\Event::getTypeOptions();
                    $label = $types[$event->type] ?? $event->type;
                    ?>
                    <span class="label label-default"><?= Html::encode($label) ?></span>
                </td>

                <td style="vertical-align: middle;" class="text-left">
                        <i class="fa fa-users text-muted"></i> <?= count($event->contacts) ?> <i class="fa fa-long-arrow-right"></i>
                        <i class="fa fa-building text-muted"></i> <?= count($event->organizations) ?>
                </td>

                <td style="vertical-align: middle; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <?= Html::encode($event->links) ?>
                </td>

                <td class="text-right" style="vertical-align: middle;">
                    <?php if ($event->canEdit()): ?>
                    <?= Button::primary()
                        ->icon('fa-pencil')
                        ->xs()
                        ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/event/edit', ['id' => $event->id]))
                    ?>
                    <?php endif; ?>
                    <?php if ($event->canDelete()): ?>
                        <?= Button::danger()
                            ->icon('fa-trash')
                            ->xs()
                            ->action('ui.modal.load', $event->content->container->createUrl('/crm/event/delete', ['id' => $event->id]))->confirm(
                                'Veranstaltung löschen',
                                'Möchtest du diese Veranstaltung wirklich unwiderruflich löschen? Alle Verknüpfungen zu ihr gehen verloren.',
                                'Löschen',
                                'Abbrechen' ) ?>
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
