<?php

use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\ui\form\widgets\MultiSelect;
use app\modules\crm\models\Contact;

/* @var $form humhub\modules\ui\form\widgets\ActiveForm */
/* @var $model Contact */
/* @var $organizations array */
?>

<div style="padding-top: 15px;">

    <div class="row">
        <div class="col-md-8">
            <!-- Name -->
            <?= $form->field($model, 'name')->textInput(['placeholder' => 'Vor- und Nachname'])->hint(false) ?>
        </div>
        <div class="col-md-4">
            <!-- Gender -->
            <?= $form->field($model, 'gender')->dropDownList([
                'male' => 'Männlich',
                'female' => 'Weiblich',
                'diverse' => 'Divers',
            ], ['prompt' => 'Keine Angabe']) ?>
        </div>
    </div>

    <!-- Link to Organization -->
    <?= $form->field($model, 'organization_id')->dropDownList($organizations, ['prompt' => 'Organisation auswählen...'])
        ->label('Zugehörigkeit') ?>

    <!-- Roles -->
    <?= $form->field($model, 'roleList')->widget(MultiSelect::class, [
        'items' => Contact::getRoleOptions(),
        'options' => [
            'multiple' => true, // allow multiselection
        ],
    ])->label('Rollen / Funktionen') ?>

    <hr>

    <div class="row">
        <div class="col-md-6">
            <!-- Email -->
            <?= $form->field($model, 'email')->input('email', ['placeholder' => 'mail@example.com']) ?>
        </div>
        <div class="col-md-6">
            <!-- Phone Number -->
            <?= $form->field($model, 'phone_number')->textInput(['placeholder' => '+49 ...']) ?>
        </div>
    </div>

    <!-- Notes (Rich Text) -->
    <?= $form->field($model, 'note')->widget(RichTextField::class) ?>

</div>
