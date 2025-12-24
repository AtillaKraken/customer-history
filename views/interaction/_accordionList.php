<?php
use app\modules\crm\widgets\InteractionCard;

/* @var $interactions app\modules\crm\models\Interaction[] */
?>

<?php if (empty($interactions)): ?>
    <div class="alert alert-info">Keine Interaktionen gefunden.</div>
<?php else: ?>
    <div class="crm-accordion-list">
        <?php foreach ($interactions as $interaction): ?>
            <?= InteractionCard::widget(['interaction' => $interaction]) ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
