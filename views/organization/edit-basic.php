<?php

use humhub\modules\user\widgets\UserPickerField;
use humhub\modules\content\widgets\richtext\RichTextField;
use app\modules\crm\models\Organization;


/* @var $form humhub\modules\ui\form\widgets\ActiveForm */
/* @var $model Organization */
?>

<div style="padding-top: 15px;">

    <!-- Name -->
    <?= $form->field($model, 'name')->textInput(['placeholder' => 'Name der Organisation'])->hint(false) ?>

    <div class="row">
        <div class="col-md-6">
            <!-- Category -->
            <?= $form->field($model, 'category')->dropDownList([
                'partner' => 'Partner', 'customer' => 'Kunde', 'lead' => 'Interessent'
            ], ['prompt' => 'Bitte auswählen...']) ?>
        </div>
        <div class="col-md-6">
            <!-- Industry -->
            <?= $form->field($model, 'industry')->dropDownList([
                'it' => 'IT & Software', 'retail' => 'Handel'
            ], ['prompt' => 'Bitte auswählen...']) ?>
        </div>
    </div>

    <!-- City -->
    <?= $form->field($model, 'city')->textInput(['placeholder' => 'Ort']) ?>

    <!-- Notes (Rich Text) -->
    <?= $form->field($model, 'description')->widget(RichTextField::class) ?>

    <!-- Responsible Users -->
    <?= $form->field($model, 'responsibleUserGuids')->widget(UserPickerField::class, [
        'selection' => $model->responsibleUsers,
        'placeholder' => 'Benutzer zuweisen...'
    ]) ?>

    <!-- "Mich zuweisen" Link -->
    <div class="text-right">
        <a href="#" class="small"><i class="fa fa-check-circle"></i> Mich zuweisen</a>
    </div>
    <!-- TODO: Mich Hinzufügen fixne-->
</div>
