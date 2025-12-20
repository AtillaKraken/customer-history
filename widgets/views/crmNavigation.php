<?php

use humhub\widgets\Button;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $contentContainer humhub\modules\content\components\ContentContainerActiveRecord */
/* @var $activeTab string */
/* @var $createButtonLabel string */
/* @var $createUrl string */
/* @var $filter app\modules\crm\models\forms\CrmFilter */

// helper function to check active state
$isActive = function ($tab) use ($activeTab) {
    return $activeTab === $tab ? 'active' : '';
};

// unique ID for Filter-Collapse
$filterId = 'crm-filter-settings-' . $activeTab;

// helper to check if a value is part of the filter array
$isChecked = function ($value) use ($filter) {
    return in_array($value, $filter->filters);
};
?>

<!-- Navigation Tabs -->
<div class="panel panel-default">
    <div class="panel-body" style="padding: 10px;">
        <ul class="nav nav-pills">
            <li class="<?= $isActive('overview') ?>"><a
                    href="<?= $contentContainer->createUrl('/crm/overview/index') ?>">Übersicht</a></li>
            <li class="<?= $isActive('organization') ?>"><a
                    href="<?= $contentContainer->createUrl('/crm/organization/index') ?>">Organisationen</a></li>
            <li class="<?= $isActive('contact') ?>"><a href="<?= $contentContainer->createUrl('/crm/contact/index') ?>">Kontaktpersonen</a>
            </li>
            <li class="<?= $isActive('interaction') ?>"><a
                    href="<?= $contentContainer->createUrl('/crm/interaction/index') ?>">Interaktionen</a></li>
            <li class="<?= $isActive('event') ?>"><a href="<?= $contentContainer->createUrl('/crm/event/index') ?>">Veranstaltungen</a>
            </li>
        </ul>
    </div>
</div>

<!-- filter and action Panel -->
<div class="panel panel-default">
    <div class="panel-body">

        <?= Html::beginForm(Url::current(), 'GET', ['id' => 'crm-filter-form', 'data-target' => '#crm-list-content']) ?>

        <div class="row">
            <div class="col-md-2" style="padding-top: 6px;">
                <a href="#<?= $filterId ?>" data-toggle="collapse" aria-expanded="false"
                   style="color: #333; text-decoration: none;" id="crm-filter-toggle-btn">
                    <strong><i class="fa fa-filter"></i> Filter</strong> <span class="caret"></span>
                </a>
            </div>

            <div class="col-md-6">
                <div class="input-group">
                    <?= Html::textInput('CrmFilter[term]', $filter->term, [
                        'class' => 'form-control',
                        'placeholder' => 'Suchen...',
                        'id' => 'crm-search-input'
                    ]) ?>
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                    </span>
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

        <div id="<?= $filterId ?>" class="collapse <?= !empty($filter->filters) ? 'in' : '' ?>"
             style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">

            <div class="row">
                <div class="col-md-3">
                    <label>
                        <?= Html::checkbox('CrmFilter[filters][]', $isChecked('mine'), ['value' => 'mine', 'class' => 'crm-filter-input']) ?>
                        Nur meine Einträge
                    </label>
                </div>

                <?php if ($activeTab === 'interaction'): ?>
                    <div class="col-md-3">
                        <label>
                            <?= Html::checkbox('CrmFilter[filters][]', $isChecked('open'), ['value' => 'open', 'class' => 'crm-filter-input']) ?>
                            Nur offene
                        </label>
                    </div>
                    <div class="col-md-3">
                        <label>
                            <?= Html::checkbox('CrmFilter[filters][]', $isChecked('overdue'), ['value' => 'overdue', 'class' => 'crm-filter-input']) ?>
                            <span class="text-danger">Überfällig</span>
                        </label>
                    </div>
                <?php endif; ?>

                <?php if ($activeTab === 'event'): ?>
                    <div class="col-md-3">
                        <label>
                            <?= Html::checkbox('CrmFilter[filters][]', $isChecked('upcoming'), ['value' => 'upcoming', 'class' => 'crm-filter-input']) ?>
                            Nur Zukünftige
                        </label>
                    </div>
                <?php endif; ?>

                <?php if ($activeTab === 'organization'): ?>
                    <div class="col-md-3">
                        <label>
                            <?= Html::checkbox('CrmFilter[filters][]', $isChecked('category_customer'), ['value' => 'category_customer', 'class' => 'crm-filter-input']) ?>
                            Kunden
                        </label>
                    </div>
                <?php endif; ?>

                <div class="col-md-12 text-right" style="margin-top: 10px;">
                    <a href="#" id="crm-filter-reset" class="btn btn-default btn-xs">
                        <i class="fa fa-times"></i> Filter zurücksetzen
                    </a>
                </div>
            </div>
        </div>

        <?= Html::endForm() ?>
    </div>
</div>

<script>
    $(document).ready(function () {

        // load
        function reloadCrmList() {
            var form = $('#crm-filter-form');
            var url = form.attr('action');
            var target = form.data('target');

            // get all inputs (Text + checked Checkboxes)
            var data = form.serialize();

            $(target).css('opacity', 0.5);

            $.get(url, data, function (response) {
                $(target).html(response).css('opacity', 1);
            }).fail(function() {
                $(target).css('opacity', 1);
                console.error("Fehler beim Laden der CRM Liste");
            });
        }

        // reactivity: when checkbox changes -> reload
        $('.crm-filter-input').on('change', function () {
            reloadCrmList();
        });
        // TODO: actually fix reset button (currently not really reactive)


        // searchbar: prevents normal submit (=> reload entire side), use Ajax instead
        $('#crm-filter-form').on('submit', function (e) {
            e.preventDefault();
            reloadCrmList();
        });

        $('#crm-filter-reset').on('click', function(e) {
            e.preventDefault();

            // empty searhbar
            $('#crm-search-input').val('');

            // uncheck all boxes
            $('#crm-filter-form').find('input[type=checkbox]').prop('checked', false);

            reloadCrmList();
        });
    });
</script>
