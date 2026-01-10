<?php

use humhub\modules\crm\widgets\ContactCard;
use humhub\widgets\LinkPager;


/* @var $contacts humhub\modules\crm\models\Contact[] */
/* @var $pagination yii\data\Pagination */
if (empty($contacts)): ?>
    <div class="alert alert-info">Keine Kontakte gefunden.</div>
<?php else: ?>
    <div class="crm-accordion-list">
        <?php foreach ($contacts as $contact): ?>
            <?= ContactCard::widget(['contact' => $contact]) ?>
        <?php endforeach; ?>
    </div>
    <?php if (isset($pagination)): ?>
        <div class="text-center"><?= LinkPager::widget(['pagination' => $pagination]) ?></div>
    <?php endif; ?>
<?php endif; ?>
