<?php

use app\modules\crm\models\Event;
use app\modules\crm\widgets\CrmNavigation;
use humhub\modules\space\models\Space;
use yii\helpers\Html;
use humhub\widgets\Button;
use yii\helpers\Url;

/**
 * @var $events Event[]
 * @var $space Space
 * @var $viewMode string
 * @var $pagination yii\data\Pagination
 */
?>

<?= CrmNavigation::widget([
    'contentContainer' => $space,
    'activeTab' => 'event',
    'createButtonLabel' => 'Neue Veranstaltung',
    'createUrl' => $space->createUrl('/crm/event/create')
]) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-calendar"></i> <strong>Veranstaltungen</strong>

        <div class="pull-right" style="margin-left: 10px;">
            <div class="btn-group btn-group-xs">
                <a href="<?= Url::current(['view' => 'list']) ?>" class="btn btn-default <?= ($viewMode === 'list') ? 'active' : '' ?>" title="Liste">
                    <i class="fa fa-list"></i>
                </a>
                <a href="<?= Url::current(['view' => 'cards']) ?>" class="btn btn-default <?= ($viewMode === 'cards') ? 'active' : '' ?>" title="Details">
                    <i class="fa fa-th-list"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="panel-body" id="crm-list-content">

        <?php if ($viewMode === 'cards'): ?>
            <?= $this->render('_accordionList', ['events' => $events, 'pagination' => $pagination]) ?>
        <?php else: ?>
            <?= $this->render('_list', ['events' => $events, 'pagination' => $pagination]) ?>
        <?php endif; ?>

        <div class="clearfix" style="margin-bottom: 15px; margin-top: 15px">
            <?= Button::success('Neue Veranstaltung')
                ->icon('fa-plus')
                ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/event/create'))
                ->right()
                ->sm()
                ->loader(false)
            ?>
        </div>
    </div>

</div>
