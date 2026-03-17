<?php

use humhub\modules\crm\models\Interaction;
use humhub\modules\crm\widgets\CrmNavigation;
use humhub\modules\crm\permissions\CreateCrmEntry;
use humhub\modules\space\models\Space;
use yii\helpers\Html;
use humhub\widgets\Button;
use yii\helpers\Url;

/**
 * @var $interactions Interaction[]
 * @var $space Space
 * @var $viewMode string
 * @var $pagination yii\data\Pagination
 * @var $filter
 */
?>

<?= CrmNavigation::widget([
    'contentContainer' => $space,
    'activeTab' => 'interaction',
    'createButtonLabel' => 'Neue Interaktion',
    'createUrl' => $space->createUrl('/crm/interaction/create'),
    'filter' => $filter
]) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-comments-o"></i> <strong>Interaktionen</strong>

        <div class="pull-right" style="margin-left: 10px;">
            <div class="btn-group btn-group-xs">
                <a href="<?= Url::current(['view' => 'list']) ?>"
                   class="btn btn-default d-flex align-items-center  <?= ($viewMode === 'list') ? 'active' : '' ?>" title="Liste">
                    <i class="fa fa-list m-0"></i>
                </a>
                <a href="<?= Url::current(['view' => 'cards']) ?>"
                   class="btn btn-default d-flex align-items-center  <?= ($viewMode === 'cards') ? 'active' : '' ?>" title="Details">
                    <i class="fa fa-th-list m-0"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="panel-body" id="crm-list-content">

        <?php if ($viewMode === 'cards'): ?>
            <?= $this->render('_accordionList', ['interactions' => $interactions, 'pagination' => $pagination]) ?>
        <?php else: ?>
            <?= $this->render('_list', ['interactions' => $interactions, 'pagination' => $pagination]) ?>
        <?php endif; ?>

        <?php if ($space->permissionManager->can(new CreateCrmEntry())): ?>
        <div class="clearfix" style="margin-bottom: 15px; margin-top: 15px">

            <?= Button::success('Neue Interaktion')
                ->icon('fa-plus')
                ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/interaction/create'))
                ->right()
                ->sm()
                ->loader(false)
            ?>
        </div>
        <?php endif; ?>
    </div>
</div>
