<?php

use humhub\modules\user\widgets\UserPickerField;
use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\crm\models\Organization;
use humhub\widgets\Link;

/* @var $form humhub\modules\ui\form\widgets\ActiveForm */
/* @var $model Organization */
?>

<div style="padding-top: 15px;">

    <!-- Name -->
    <?= $form->field($model, 'name')->textInput(['placeholder' => 'Name der Organisation'])->hint(false) ?>

    <div class="row">
        <div class="col-md-6">
            <!-- Category -->
            <?= $form->field($model, 'category')->dropDownList(Organization::getCategoryOptions(), [
                'prompt' => 'Bitte auswählen...',
            ]) ?>
        </div>
        <div class="col-md-6">
            <!-- Industry -->
            <?= $form->field($model, 'industry')->dropDownList(Organization::getIndustryOptions(), [
                'prompt' => 'Bitte auswählen...',
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <!-- Size -->
            <?= $form->field($model, 'size')->dropDownList(Organization::getSizeOptions(), [
                'prompt' => 'Bitte auswählen...',
            ]) ?>
        </div>
        <div class="col-md-6">
            <!-- City -->
            <?= $form->field($model, 'city')->textInput(['placeholder' => 'Sitz der Organisation']) ?>
        </div>
    </div>

    <!-- Notes (Rich Text) -->
    <?= $form->field($model, 'notes')->widget(RichTextField::class) ?>

    <!-- Responsible Users -->
    <?= $form->field($model, 'responsibleUserGuids')->widget(UserPickerField::class, [
        'id' => 'crm-responsible-user-picker',
        'selection' => $model->responsibleUsers,
        'placeholder' => 'Benutzer zuweisen...'])?>

    <!-- "Assign me" Button -->
    <div class="text-right">
        <?= Link::userPickerSelfSelect('#crm-responsible-user-picker', 'Mich zuweisen')
            ->icon('fa-check-circle')
            ->options(['class' => 'small']) ?>
    </div>

    <div class="alert alert-info" style="font-size: 12px; padding: 8px; margin-right: 115px">
        <i class="fa fa-shield"></i> <strong>Berechtigungen:</strong>
        Auswahl darf automatisch zugehörige <strong>Kontaktpersonen, Interaktionen und Veranstaltungen</strong> bearbeiten.
    </div>
</div>
