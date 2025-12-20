<?php

use app\modules\crm\models\Event;
use app\modules\crm\widgets\CrmNavigation;
use humhub\modules\space\models\Space;
use yii\helpers\Html;
use humhub\widgets\Button;

/**
 * @var $events Event[]
 * @var $space Space
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
    </div>
    <div class="panel-body" id="crm-list-content">
        <?= $this->render('_list', ['events' => $events]) ?>

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
