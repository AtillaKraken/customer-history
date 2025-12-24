<?php
use app\modules\crm\widgets\InteractionCard;
use humhub\widgets\LinkPager;

/* @var $interactions app\modules\crm\models\Interaction[] */
/* @var $pagination yii\data\Pagination */
?>

<?php if (empty($interactions)): ?>
    <div class="alert alert-info">Keine Interaktionen gefunden.</div>
<?php else: ?>
    <div class="crm-accordion-list">
        <?php foreach ($interactions as $interaction): ?>
            <?= InteractionCard::widget(['interaction' => $interaction]) ?>
        <?php endforeach; ?>
    </div>

    <div class="text-center">
        <?= LinkPager::widget(['pagination' => $pagination]) ?>
    </div>
<?php endif; ?>
