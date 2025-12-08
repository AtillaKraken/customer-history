<?php

use humhub\modules\user\widgets\UserPickerField;
use humhub\modules\content\widgets\richtext\RichTextField;
use app\modules\crm\models\Interaction;
use humhub\modules\ui\form\widgets\TimePicker;
use yii\jui\DatePicker;


/* @var $form humhub\modules\ui\form\widgets\ActiveForm */
/* @var $model Interaction */
?>

<div style="padding-top: 15px;">
    <div class="modal-body">

        <!-- Name -->
        <?= $form->field($model, 'title')->textInput(['placeholder' => 'Name der Interaktion'])->hint(false) ?>

        <div class="row">
            <div class="col-md-6">
                <!-- Date -->
                <?= $form->field($model, 'date')->widget(DatePicker::class, ['dateFormat' => Yii::$app->formatter->dateInputFormat, 'clientOptions' => [], 'options' => ['class' => 'form-control']]) ?>
            </div>
            <div class="col-md-6">
                <!-- Time -->
                <?= $form->field($model, 'time')->widget(TimePicker::class); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Channel -->
                <?= $form->field($model, 'channel')->dropDownList([
                    'E-Mail' => 'E-Mail', 'Phone Call' => 'Telefonat', 'Social Media' => 'Soziale Medien'
                ], ['prompt' => 'Bitte auswählen...']) ?>
            </div>
            <div class="col-md-6">
                <!-- Status -->
                <?= $form->field($model, 'status')->dropDownList([
                    'PLANNED' => 'Geplant', 'OVERDUE' => 'Überfällig', 'CANCELLED' => 'Abgesagt', 'DONE' => 'Erledigt'
                ], ['prompt' => 'Bitte auswählen...']) ?>
            </div>
        </div>

        <!-- Description (Rich Text) -->
        <?= $form->field($model, 'description')->widget(RichTextField::class) ?>

        <!-- TODO: Toggle Logic einbauen | Wenn Status === DONE zeige result an, else description-->

        <!-- Result (Rich Text) -->
        <?= $form->field($model, 'result')->widget(RichTextField::class) ?>

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
</div>
