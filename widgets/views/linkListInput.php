<?php

use humhub\modules\crm\models\EntityLink;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/* @var $model ActiveRecord */
/* @var $links EntityLink[] */
/* @var $formName string */
/* @var $attributeNew string */
/* @var $attributeEdit string */
?>

    <div class="link-list-wrapper">
        <label class="control-label">
            <i class="fa fa-link"></i> Verknüpfte Links (z.B. Nextcloud)
        </label>

        <div class="crm-links-container">

            <?php foreach ($links as $link) : ?>
                <div class="form-group link-row" style="margin-bottom: 8px;">
                    <div class="input-group">
                    <span class="input-group-addon" style="background-color: #f7f7f7;">
                        <i class="fa fa-external-link"></i>
                    </span>
                        <?= Html::textInput($formName . '['.$attributeEdit.'][' . $link->id . ']', $link->url, [
                            'class' => 'form-control',
                            'placeholder' => 'https://...',
                        ]) ?>
                        <span class="input-group-btn">
                        <button class="btn btn-default crm-remove-link-btn" type="button" title="Link entfernen">
                            <i class="fa fa-trash text-danger"></i>
                        </button>
                    </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="crm-add-link-wrapper" style="margin-top: 10px;">
            <button type="button" class="btn btn-default btn-sm btn-block crm-add-link-btn" style="border-style: dashed; color: #777;">
                <i class="fa fa-plus"></i> Weiteren Link hinzufügen
            </button>
        </div>

        <div class="crm-new-link-template" style="display:none;">
            <div class="form-group link-row" style="margin-bottom: 8px;">
                <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-link"></i>
                </span>
                    <?= Html::textInput($formName . '['.$attributeNew.'][]', '', [
                        'class' => 'form-control',
                        'placeholder' => 'https://...',
                    ]) ?>
                    <span class="input-group-btn">
                    <button class="btn btn-default crm-remove-link-btn" type="button" title="Link entfernen">
                        <i class="fa fa-trash"></i>
                    </button>
                </span>
                </div>
            </div>
        </div>
    </div>

<?php
$this->registerJs(<<<'JS'
(function() {
    // Event: "Link hinzufügen" was clicked
    $(document).off('click.crmLinkAdd').on('click.crmLinkAdd', '.crm-add-link-btn', function() {
        var $wrapper = $(this).closest('.link-list-wrapper');
        var $container = $wrapper.find('.crm-links-container');
        var $template = $wrapper.find('.crm-new-link-template');

        // clone template
        var $newItem = $template.children().first().clone();

        // empty input
        $newItem.find('input').val('');

        // insert
        $container.append($newItem);

        // focus new input-field (next Link)
        $newItem.find('input').focus();
    });

    // Event: Bin icon was clicked
    $(document).off('click.crmLinkRemove').on('click.crmLinkRemove', '.crm-remove-link-btn', function() {
        // delete link/row
        $(this).closest('.link-row').fadeOut(200, function() {
            $(this).remove();
        });
    });
})();
JS
);
?>
