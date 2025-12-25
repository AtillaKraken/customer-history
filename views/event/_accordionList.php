<?php
use app\modules\crm\widgets\EventCard;
use humhub\widgets\LinkPager;

/* @var $events app\modules\crm\models\Event[] */
/* @var $pagination yii\data\Pagination */
?>

<?php if (empty($events)): ?>
    <div class="alert alert-info">Keine Veranstaltungen gefunden.</div>
<?php else: ?>
    <div class="crm-accordion-list">
        <?php foreach ($events as $event): ?>
            <?= EventCard::widget(['event' => $event]) ?>
        <?php endforeach; ?>
    </div>

    <div class="text-center">
        <?= LinkPager::widget(['pagination' => $pagination]) ?>
    </div>
<?php endif; ?>
