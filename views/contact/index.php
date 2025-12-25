<?php

use app\modules\crm\widgets\CrmNavigation;
use humhub\modules\space\models\Space;
use yii\helpers\Url;
use humhub\widgets\Button;

/**
 * @var $contacts app\modules\crm\models\Contact[]
 * @var $space Space
 * @var $filter app\modules\crm\models\forms\CrmFilter
 * @var $space humhub\modules\space\models\Space
 * @var $viewMode string
 * @var $pagination yii\data\Pagination
 */
?>


<?= CrmNavigation::widget([
    'contentContainer' => $space,
    'activeTab' => 'contact',
    'createButtonLabel' => 'Neue Kontaktperson',
    'createUrl' => $space->createUrl('/crm/contact/create'),
    'filter' => $filter
]) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-address-card-o"></i> <strong>Kontaktpersonen</strong> im Space
        <div class="pull-right" style="margin-left: 10px;">
            <div class="btn-group btn-group-xs">
                <a href="<?= Url::current(['view' => 'list']) ?>"
                   class="btn btn-default <?= ($viewMode === 'list') ? 'active' : '' ?>" title="Liste">
                    <i class="fa fa-list"></i>
                </a>
                <a href="<?= Url::current(['view' => 'cards']) ?>"
                   class="btn btn-default <?= ($viewMode === 'cards') ? 'active' : '' ?>" title="Details">
                    <i class="fa fa-th-list"></i>
                </a>
            </div>
        </div>
    </div>


    <div class="panel-body" id="crm-list-content">
        <?php if ($viewMode === 'cards'): ?>
            <?= $this->render('_accordionList', ['contacts' => $contacts, 'pagination' => $pagination]) ?>
        <?php else: ?>
            <?= $this->render('_list', ['contacts' => $contacts, 'pagination' => $pagination]) ?>
        <?php endif; ?>
        <div class="clearfix" style="margin-bottom: 15px; margin-top: 15px">
            <?= Button::success('Neue Kontaktperson')
                ->icon('fa-plus')
                ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/contact/create'))
                ->right()->sm()->loader(false) ?>
        </div>
    </div>
</div>
