<?php

use app\modules\crm\models\Organization;
use app\modules\crm\widgets\CrmNavigation;
use humhub\modules\space\models\Space;
use humhub\widgets\Button;

/**
 * @var $organizations Organization[]
 * @var $space Space
 */
?>

<!-- WICHTIG: Alles in einem Wrapper, damit PJAX nicht das Layout zerreißt -->
<div class="crm-module-container">

    <!-- Navigation Widget -->
    <?= CrmNavigation::widget([
        'contentContainer' => $space,
        'activeTab' => 'organization',
        'createButtonLabel' => 'Neue Organisation',
        'createUrl' => $space->createUrl('/crm/organization/create'),
    ]) ?>

    <!-- Content Panel -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-building"></i> <strong>Organisationen</strong> im Space
        </div>

        <div class="panel-body">
            <!-- AJAX Target für Listen-Updates -->
            <div id="crm-list-content">
                <?= $this->render('_list', ['organizations' => $organizations]) ?>
            </div>
            <div class="clearfix" style="margin-bottom: 15px; margin-top: 15px">
                <?= Button::success('Neue Organisation')
                    ->icon('fa-plus')
                    ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/organization/create'))
                    ->right()
                    ->sm()
                    ->loader(false)
                ?>
            </div>
        </div>

    </div>

</div>
