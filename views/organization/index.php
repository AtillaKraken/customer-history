<?php

use humhub\modules\crm\models\Organization;
use humhub\modules\crm\widgets\CrmNavigation;
use humhub\modules\crm\permissions\CreateCrmEntry;
use humhub\widgets\Button;
use yii\helpers\Url;

/**
 * @var $organizations Organization[]
 * @var $space humhub\modules\space\models\Space
 * @var $viewMode string
 * @var $pagination yii\data\Pagination
 * @var $filter
 */
?>

<?= CrmNavigation::widget([
    'contentContainer' => $space,
    'activeTab' => 'organization',
    'createButtonLabel' => 'Neue Organisation',
    'createUrl' => $space->createUrl('/crm/organization/create'),
    'filter' => $filter
]) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-building"></i> <strong>Organisationen</strong>

        <div class="pull-right" style="margin-left: 10px;">
            <div class="btn-group btn-group-xs">
                <a href="<?= Url::current(['view' => 'list']) ?>" class="btn btn-default d-flex align-items-center <?= ($viewMode === 'list') ? 'active' : '' ?>" title="Liste">
                    <i class="fa fa-list m-0 m-0"></i>
                </a>
                <a href="<?= Url::current(['view' => 'cards']) ?>" class="btn btn-default d-flex align-items-center <?= ($viewMode === 'cards') ? 'active' : '' ?>" title="Details">
                    <i class="fa fa-th-list m-0"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="panel-body" id="crm-list-content">
        <?php if ($viewMode === 'cards'): ?>
            <?= $this->render('_accordionList', ['organizations' => $organizations, 'pagination' => $pagination]) ?>
        <?php else: ?>
            <?= $this->render('_list', ['organizations' => $organizations, 'pagination' => $pagination]) ?>
        <?php endif; ?>

        <?php if ($space->permissionManager->can(new CreateCrmEntry())): ?>
        <div class="clearfix" style="margin-bottom: 15px; margin-top: 15px">
            <?= Button::success('Neue Organisation')
                ->icon('fa-plus')
                ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/organization/create'))
                ->right()->sm()->loader(false) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
