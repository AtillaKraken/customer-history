<?php

use humhub\modules\crm\widgets\LinkListInput;
use humhub\modules\topic\widgets\TopicPicker;
use humhub\modules\file\widgets\UploadButton;
use humhub\modules\file\widgets\FilePreview;

/* @var $model app\modules\crm\models\Event */
/* @var $form humhub\modules\ui\form\widgets\ActiveForm */

// especially necessary here for the topic-implementation
$contentContainer = $model->content->container;
?>

<div style="padding-top: 15px;">

    <div class="modal-body">

        <!-- LinkListInput -->
        <?= LinkListInput::widget(['model' => $model]) ?>

        <hr>

        <!-- default file-attachments (HumHub Standard) -->
        <div class="form-group">
            <label class="control-label">Dateien hochladen</label>
            <br>
            <?= UploadButton::widget([
                'id' => 'event_upload_button',
                'model' => $model,
                'attribute' => 'fileList',
                'progress' => '#event_upload_progress',
                'preview' => '#event_upload_preview',
            ]) ?>

            <div id="event_upload_progress" style="display:none; margin: 10px 0;"></div>
            <div id="event_upload_preview">
                <?= FilePreview::widget([
                    'id' => 'event_upload_preview',
                    'model' => $model,
                    'edit' => true
                ]) ?>
            </div>
        </div>

        <hr>

        <!-- Topic implementation -->
        <?= $form->field($model, 'topics')->widget(TopicPicker::class, ['contentContainer' => $model->content->container]) ?>

    </div>

