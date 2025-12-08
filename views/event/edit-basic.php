<?php

use app\modules\crm\models\Event;
use humhub\modules\ui\form\widgets\TimePicker;
use humhub\modules\content\widgets\richtext\RichTextField;
use yii\jui\DatePicker;

/* @var $model Event */
/* @var $form humhub\modules\ui\form\widgets\ActiveForm */

?>

<div style="padding-top: 15px;">

    <div class="modal-body">

        <!-- Title -->
        <?= $form->field($model, 'title')->textInput(['placeholder' => 'Titel der Veranstaltung'])->hint(false) ?>

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

        <!-- Type -->
        <?= $form->field($model, 'type')->dropDownList([
            'it' => 'IT & Software', 'retail' => 'Handel'
        ], ['prompt' => 'Bitte auswählen...']) ?>

        <!-- Descirption (Rich Text) -->
        <?= $form->field($model, 'description')->widget(RichTextField::class) ?>
    </div>
</div>
