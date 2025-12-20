<?php

use app\modules\crm\widgets\CrmNavigation;
use humhub\modules\space\models\Space;
use humhub\widgets\Button;

/**
 * @var $contacts app\modules\crm\models\Contact[]
 * @var $space Space
 * @var $filter app\modules\crm\models\forms\CrmFilter
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
    </div>

    <div class="panel-body" id="crm-list-content">
        <?= $this->render('_list', ['contacts' => $contacts]) ?>

        <div class="clearfix" style="margin-bottom: 15px; margin-top: 15px">
            <?= Button::success('Neue Kontaktperson')
                ->icon('fa-plus')
                ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/contact/create'))
                ->right()
                ->sm()
                ->loader(false)
            ?>
        </div>
    </div>
</div>
