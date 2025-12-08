<?php

use humhub\widgets\Button;

/* @var $contentContainer humhub\modules\content\components\ContentContainerActiveRecord */
/* @var $activeTab string */
/* @var $createButtonLabel string */
/* @var $createUrl string */

// Helper function to check active state
$isActive = function($tab) use ($activeTab) {
    return $activeTab === $tab ? 'active' : '';
};
?>

<!-- Navigation Tabs -->
<div class="panel panel-default">
    <div class="panel-body" style="padding: 10px;">
        <ul class="nav nav-pills">
            <li class="<?= $isActive('overview') ?>">
                <a href="<?= $contentContainer->createUrl('/crm/overview/index') ?>">Übersicht</a>
            </li>
            <li class="<?= $isActive('organization') ?>">
                <a href="<?= $contentContainer->createUrl('/crm/organization/index') ?>">Organisationen</a>
            </li>
            <li class="<?= $isActive('contact') ?>">
                <a href="<?= $contentContainer->createUrl('/crm/contact/index') ?>">Kontaktpersonen</a>
            </li>
            <li class="<?= $isActive('interaction') ?>">
                <a href="<?= $contentContainer->createUrl('/crm/interaction/index') ?>">Interaktionen</a>
            </li>
            <li class="<?= $isActive('event') ?>">
                <a href="<?= $contentContainer->createUrl('/crm/event/index') ?>">Veranstaltungen</a>
            </li>
        </ul>
    </div>
</div>

<!-- filter and action Panel -->
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-2" style="padding-top: 6px;">
                <strong><i class="fa fa-filter"></i> Filter</strong> <span class="caret"></span>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Suchbegriff eingeben...">
                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                </div>
            </div>
            <div class="col-md-4 text-right">
                <?= Button::success($createButtonLabel)
                    ->icon('fa-plus')
                    ->action('ui.modal.load', $createUrl)
                    ->right()
                    ->sm()
                    ->loader(false)
                ?>
            </div>
        </div>
    </div>
</div>
