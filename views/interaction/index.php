<?php

use app\modules\crm\models\Interaction;
use app\modules\crm\widgets\CrmNavigation;
use humhub\modules\space\models\Space;
use yii\helpers\Html;
use humhub\widgets\Button;
use yii\helpers\Url;

/**
 * @var $interactions Interaction[]
 * @var $space Space
 * @var $viewMode string
 * @var $pagination yii\data\Pagination
 */
?>

<?= CrmNavigation::widget([
    'contentContainer' => $space,
    'activeTab' => 'interaction',
    'createButtonLabel' => 'Neue Interaktion',
    'createUrl' => $space->createUrl('/crm/interaction/create')
]) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-comments-o"></i> <strong>Interaktionen</strong>

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
            <?= $this->render('_accordionList', ['interactions' => $interactions, 'pagination' => $pagination]) ?>
        <?php else: ?>
            <?= $this->render('_list', ['interactions' => $interactions, 'pagination' => $pagination]) ?>
        <?php endif; ?>

        <div class="clearfix" style="margin-bottom: 15px; margin-top: 15px">

            <?= Button::success('Neue Interaktion')
                ->icon('fa-plus')
                ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/interaction/create'))
                ->right()
                ->sm()
                ->loader(false)
            ?>
        </div>
    </div>
</div>
