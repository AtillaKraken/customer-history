<?php
use humhub\modules\crm\widgets\InteractionCard;
use humhub\widgets\LinkPager;

/* @var $interactions humhub\modules\crm\models\Interaction[] */
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

    <?php if (isset($pagination)): ?>
        <div class="text-center">
            <?= LinkPager::widget(['pagination' => $pagination]) ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
