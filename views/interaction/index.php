<?php

use app\modules\crm\models\Interaction;
use app\modules\crm\widgets\CrmNavigation;
use app\modules\crm\widgets\InteractionCard;
use humhub\modules\space\models\Space;
use yii\helpers\Html;
use humhub\widgets\Button;

/**
 * @var $interactions Interaction[]
 * @var $space Space
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
    </div>
    <div class="panel-body" id="crm-list-content">
        <?= $this->render('_list', ['interactions' => $interactions]) ?>
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
