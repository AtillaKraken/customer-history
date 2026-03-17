<?php

use humhub\modules\crm\permissions\CreateCrmEntry;
use humhub\widgets\Button;
use yii\helpers\Html;
use yii\helpers\Url;
use humhub\modules\ui\form\widgets\MultiSelect;
use yii\jui\DatePicker;
use humhub\modules\crm\models\Organization;
use humhub\modules\crm\models\Interaction;
use humhub\modules\crm\models\Contact;
use humhub\modules\crm\models\Event;

/* @var $contentContainer humhub\modules\content\components\ContentContainerActiveRecord */
/* @var $activeTab string */
/* @var $createButtonLabel string */
/* @var $createUrl string */
/* @var $filter humhub\modules\crm\models\forms\CrmFilter */
/* @var $orgOptions array */
/* @var $userOptions array */

// helper function to check active state
$isActive = function ($tab) use ($activeTab) {
    return $activeTab === $tab ? 'active' : '';
};

// unique ID for Filter-Collapse
$filterId = 'crm-filter-settings-' . $activeTab;

// collapse filter dropdown if filters're applied
$hasActiveFilters = !empty($filter->filters) || !empty($filter->orgCategories) || !empty($filter->interactionStatus) || !empty($filter->contactOrg);

// examine whether or not we are i nthe module's overview/dashboard (overview/index.php)
$isOverview = ($activeTab === 'overview');


?>

<style>
    /* rotate animation to un-/collapse the filter dropdown*/
    #crm-filter-toggle-btn .fa-caret-down {
        transition: transform 0.3s ease;
    }

    #crm-filter-toggle-btn[aria-expanded="true"] .fa-caret-down {
        transform: rotate(180deg);
    }
</style>

<!-- Navigation Tabs -->
<div class="card" style="margin-bottom: 15px; background-color: white">
    <div class="card-body" style="padding: 10px">
        <ul class="nav nav-pills">

            <li class="nav-item">
                <a class="nav-link <?= $isActive('overview') ?>"
                   href="<?= $contentContainer->createUrl('/crm/overview/index') ?>">
                    Übersicht
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $isActive('organization') ?>"
                   href="<?= $contentContainer->createUrl('/crm/organization/index') ?>">
                    Organisationen
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $isActive('contact') ?>"
                   href="<?= $contentContainer->createUrl('/crm/contact/index') ?>">
                    Kontaktpersonen
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $isActive('interaction') ?>"
                   href="<?= $contentContainer->createUrl('/crm/interaction/index') ?>">
                    Interaktionen
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $isActive('event') ?>"
                   href="<?= $contentContainer->createUrl('/crm/event/index') ?>">
                    Veranstaltungen
                </a>
            </li>

            <?php if ($isOverview && $createUrl): ?>
                <li class="nav-item ms-auto">
                    <a href="#"
                       data-action-click="ui.modal.load"
                       data-action-url="<?= $createUrl ?>"
                       class="btn btn-success text-white fw-bold">
                        <i class="fa fa-plus"></i> <?= $createButtonLabel ?>
                    </a>
                </li>
            <?php endif; ?>

        </ul>
    </div>
</div>

<!-- filter and action Panel -->
<?php if (!$isOverview): ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <?= Html::beginForm(Url::current(), 'GET', ['id' => 'crm-filter-form', 'data-target' => '#crm-list-content']) ?>

            <div class="row">
                <div class="col-md-2" style="padding-top: 6px;">
                    <a href="#<?= $filterId ?>" data-bs-toggle="collapse"
                       aria-expanded="<?= $hasActiveFilters ? 'true' : 'false' ?>"
                       style="color: #333; text-decoration: none;" id="crm-filter-toggle-btn">
                        <strong><i class="fa fa-filter"></i> Filter</strong> <i class="fa fa-caret-down"></i>
                    </a>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <?= Html::activeTextInput($filter, 'term', ['class' => 'form-control', 'placeholder' => 'Suchen...', 'id' => 'crm-search-input']) ?>
                        <span class="input-group-btn"><button class="btn btn-default" type="submit"><i
                                    class="fa fa-search"></i></button></span>
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    <?php if ($contentContainer->permissionManager->can(new CreateCrmEntry())): ?>
                        <?= Button::success($createButtonLabel)
                            ->icon('fa-plus')
                            ->action('ui.modal.load', $createUrl)
                            ->right()
                            ->sm()
                            ->loader(false)
                        ?>
                    <?php endif; ?>
                </div>
            </div>

            <div id="<?= $filterId ?>" class="collapse <?= $hasActiveFilters ? 'in' : '' ?>"
                 style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">

                <?php if ($activeTab === 'organization'): ?>
                    <div class="row">
                        <div class="col-md-3">
                            <label>Kategorie</label>
                            <?= MultiSelect::widget(['model' => $filter, 'attribute' => 'orgCategories', 'items' => Organization::getCategoryOptions(), 'options' => ['class' => 'crm-filter-input']]) ?>
                        </div>
                        <div class="col-md-3">
                            <label>Branche</label>
                            <?= MultiSelect::widget(['model' => $filter, 'attribute' => 'orgIndustries', 'items' => Organization::getIndustryOptions(), 'options' => ['class' => 'crm-filter-input']]) ?>
                        </div>
                        <div class="col-md-3">
                            <label>Größe</label>
                            <?= Html::activeDropDownList($filter, 'orgSize', Organization::getSizeOptions(), ['class' => 'form-control crm-filter-input', 'prompt' => 'Alle']) ?>
                        </div>
                        <div class="col-md-3">
                            <label>Verantwortlich</label>
                            <?= MultiSelect::widget(['model' => $filter, 'attribute' => 'orgRespUsers', 'items' => $userOptions, 'options' => ['class' => 'crm-filter-input']]) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($activeTab === 'contact'): ?>
                    <div class="row">
                        <div class="col-md-3">
                            <label>Geschlecht</label>
                            <?= MultiSelect::widget(['model' => $filter, 'attribute' => 'contactGender', 'items' => Contact::getGenderOptions(), 'options' => ['class' => 'crm-filter-input']]) ?>
                        </div>
                        <div class="col-md-3">
                            <label>Organisation</label>
                            <?= MultiSelect::widget(['model' => $filter, 'attribute' => 'contactOrg', 'items' => $orgOptions, 'options' => ['class' => 'crm-filter-input']]) ?>
                        </div>
                        <div class="col-md-3">
                            <label>Rolle</label>
                            <?= MultiSelect::widget(['model' => $filter, 'attribute' => 'contactRoles', 'items' => Contact::getRoleOptions(), 'options' => ['class' => 'crm-filter-input']]) ?>
                        </div>
                        <div class="col-md-3" style="padding-top: 25px;">
                            <label>
                                <?= Html::activeCheckbox($filter, 'contactOwnOrg', ['label' => 'Von mir betreute Organisationen', 'class' => 'crm-filter-input']) ?>
                            </label>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($activeTab === 'interaction'): ?>
                    <div class="row">
                        <div class="col-md-3">
                            <label>Datum</label>
                            <?= DatePicker::widget(['model' => $filter, 'attribute' => 'interactionDate', 'dateFormat' => 'yyyy-MM-dd', 'options' => ['class' => 'form-control crm-filter-input', 'placeholder' => 'Datum']]) ?>
                        </div>
                        <div class="col-md-3">
                            <label>Status</label>
                            <?= MultiSelect::widget(['model' => $filter, 'attribute' => 'interactionStatus', 'items' => Interaction::getStatusOptions(), 'options' => ['class' => 'crm-filter-input']]) ?>
                        </div>
                        <div class="col-md-3">
                            <label>Kanal</label>
                            <?= MultiSelect::widget(['model' => $filter, 'attribute' => 'interactionChannel', 'items' => Interaction::getChannelOptions(), 'options' => ['class' => 'crm-filter-input']]) ?>
                        </div>
                        <div class="col-md-3">
                            <label>Verantwortlich</label>
                            <?= MultiSelect::widget(['model' => $filter, 'attribute' => 'interactionRespUsers', 'items' => $userOptions, 'options' => ['class' => 'crm-filter-input']]) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($activeTab === 'event'): ?>
                    <div class="row">
                        <div class="col-md-3">
                            <label>Datum</label>
                            <?= DatePicker::widget(['model' => $filter, 'attribute' => 'eventDate', 'dateFormat' => 'yyyy-MM-dd', 'options' => ['class' => 'form-control crm-filter-input', 'placeholder' => 'Datum']]) ?>
                        </div>
                        <div class="col-md-3">
                            <label>Typ</label>
                            <?= MultiSelect::widget(['model' => $filter, 'attribute' => 'eventType', 'items' => Event::getTypeOptions(), 'options' => ['class' => 'crm-filter-input']]) ?>
                        </div>
                        <div class="col-md-3">
                            <label>Organisation</label>
                            <?= MultiSelect::widget(['model' => $filter, 'attribute' => 'eventOrg', 'items' => $orgOptions, 'options' => ['class' => 'crm-filter-input']]) ?>
                        </div>
                        <div class="col-md-3" style="padding-top: 25px;">
                            <label><?= Html::activeCheckbox($filter, 'eventOwnOrg', ['label' => 'Von mir betreute Organisationen', 'class' => 'crm-filter-input']) ?></label>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row" style="margin-top: 15px; border-top: 1px dashed #ddd; padding-top: 10px;">
                    <div class="col-md-12 text-right">
                        <a href="#" id="crm-filter-reset" class="btn btn-default btn-sm"><i class="fa fa-times"></i>
                            Filter
                            zurücksetzen</a>
                    </div>
                </div>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
<?php endif; ?>

<?php
$script = <<<JS
(function() {
    var formSelector = '#crm-filter-form';

    // load
    function reloadCrmList() {
        var \$form = $(formSelector);
        var url = \$form.attr('action');
        var data = \$form.serialize();
        var target = \$form.data('target');
        var \$target = $(target);

        \$target.css('opacity', 0.5);

        $.get(url, data, function (response) {
            \$target.html(response).css('opacity', 1);
        }).fail(function() {
            \$target.css('opacity', 1);
        });
    }

    // listen to .crm-filter-input's information [checkboxes, selections]
    $(document).off('change.crm', '.crm-filter-input').on('change.crm', '.crm-filter-input', function () {
        reloadCrmList();
    });
    // reactivity: when searched -> reload
    $(document).off('submit.crm', formSelector).on('submit.crm', formSelector, function (e) {
        e.preventDefault();
        reloadCrmList();
    });

    // reactivity: when reset button is pressed -> empty/uncheck all and reload
    $(document).off('click.crmReset', '#crm-filter-reset').on('click.crmReset', '#crm-filter-reset', function(e) {
        e.preventDefault();

        var \$form = $(formSelector);

        // empty texts and datepickers
        \$form.find('input[type="text"]').val('');

        // unmark checkboxen
        \$form.find('input[type="checkbox"]').prop('checked', false);

        // reset all selections
        // => .val(null) deletes value
        // => .trigger('change') is necesarry to visually update the state
        \$form.find('select').each(function() {
             $(this).val(null).trigger('change');
        });

        reloadCrmList();
    });
})();
JS;
$this->registerJs($script);
?>
