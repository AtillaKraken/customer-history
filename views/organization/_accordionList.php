<?php

use humhub\modules\crm\widgets\OrganizationCard;
use humhub\widgets\LinkPager;

/* @var $organizations humhub\modules\crm\models\Organization[] */
/* @var $pagination yii\data\Pagination */
?>

<?php if (empty($organizations)): ?>
    <div class="alert alert-info">Keine Organisationen gefunden.</div>
<?php else: ?>
    <div class="crm-accordion-list">
        <?php foreach ($organizations as $org): ?>
            <?= OrganizationCard::widget(['organization' => $org]) ?>
        <?php endforeach; ?>
    </div>
    <?php if (isset($pagination)): ?>
        <div class="text-center"><?= LinkPager::widget(['pagination' => $pagination]) ?></div>
    <?php endif; ?>
<?php endif; ?>
