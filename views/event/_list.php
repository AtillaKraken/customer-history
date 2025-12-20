<?php

use humhub\widgets\Button;
use yii\helpers\Html;

/* @var $events app\modules\crm\models\Event[] */
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
                <td><strong><?= Html::encode($event->title) ?></strong></td>
                <td>
                    <?= Yii::$app->formatter->asDate($event->date) ?>
                    <?php if($event->time): ?>
                        <small class="text-muted"><?= Html::encode($event->time) ?> Uhr</small>
                    <?php endif; ?>
                </td>
                <td><span class="label label-default"><?= Html::encode($event->type) ?></span></td>
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
<?php endif; ?>
