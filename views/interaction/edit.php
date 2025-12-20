<?php

use app\modules\crm\models\Interaction;
use humhub\widgets\ModalDialog;
use humhub\widgets\ModalButton;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\widgets\Tabs;

/* @var $model Interaction */

// set modal title dynamically
$header = $model->isNewRecord
    ? 'Neue <strong>Interaktion</strong> erstellen'
    : 'Interaktion <strong>bearbeiten</strong>';

$buttonText = $model->isNewRecord ? 'Erstellen' : 'Speichern';
?>

<?php ModalDialog::begin(['header' => 'Neue <strong>Interaktion</strong>', 'size' => 'large']) ?>
<?php $form = ActiveForm::begin(['id' => 'interaction-form']); ?>

<?= Tabs::widget([
    'items' => [
        [
            'label' => 'Allgemein',
            'content' => $this->render('edit-basic', ['model' => $model, 'form' => $form]),
            'active' => true, // initially the first tab is active
        ],
        [
            'label' => 'Anhänge',
            'content' => $this->render('edit-attachments', ['model' => $model, 'form' => $form]),
        ],
    ]
]); ?>

<div class="modal-footer" style="display: flex; align-items: center; justify-content: center;">
    <?= ModalButton::submitModal(null, 'Speichern') ?>
    <?= ModalButton::cancel('Abbrechen') ?>

    <div class="interaction-quality-traffic-light" style="margin-left: 20px; display: flex; align-items: center; gap: 8px; border-left: 1px solid #ddd; padding-left: 15px;">
        <span id="debug-score" class="small text-muted" style="font-weight:bold; font-family: monospace;">0%</span>
        <div id="traffic-light-red" style="width: 15px; height: 15px; border-radius: 50%; background-color: #dc3545; opacity: 1; border: 1px solid #ced4da; transition: all 0.3s;"></div>
        <div id="traffic-light-yellow" style="width: 15px; height: 15px; border-radius: 50%; background-color: #ffc107; opacity: 0.2; border: 1px solid #ced4da; transition: all 0.3s;"></div>
        <div id="traffic-light-green" style="width: 15px; height: 15px; border-radius: 50%; background-color: #28a745; opacity: 0.2; border: 1px solid #ced4da; transition: all 0.3s;"></div>
    </div>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>

<?php
// Script for traffic-light logic (CSP Safe)
$script = <<<'JS'
(function() {
    var formId = '#interaction-form';
    var timer = null;

    function hasValue(attributeName) {
        // lookup names like Interaction[title]
        var selector = '[name*="[' + attributeName + ']"]';
        var $el = $(formId).find(selector);

        if ($el.length === 0) return false;

        if ($el.is(':checkbox') || $el.is(':radio')) return $el.is(':checked');

        var val = $el.val();
        if (Array.isArray(val)) return val.length > 0;
        if (val && typeof val === 'string' && val.trim() !== '') return true;

        // check richText
        if (attributeName === 'description' || attributeName === 'result') {
            var $richText = $el.closest('.form-group').find('.humhub-ui-richtext[contenteditable]');
            if ($richText.length) {
                return $richText.text().trim().length > 0;
            }
        }
        return false;
    }

    function checkForm() {
        if ($(formId).length === 0) {
            clearInterval(timer);
            return;
        }

        var points = 0;
        var maxPoints = 12;

        // InputFields from edit-basic.php
        if (hasValue('title')) points += 3;
        if (hasValue('date')) points += 3;
        if (hasValue('time')) points += 1;
        if (hasValue('channel')) points += 1;
        if (hasValue('responsibleUserGuids')) points += 2;

        // Status Logic
        var statusIsDone = false;
        var $status = $(formId).find('[name*="[status]"]');
        if ($status.length && $status.val() === 'DONE') statusIsDone = true;

        if (statusIsDone) {
            if (hasValue('result')) points += 2;
        } else {
            if (hasValue('description')) points += 2;
        }

        var percent = Math.round((points / maxPoints) * 100);
        if (percent > 100) percent = 100;

        // traffic light coloring
        $('#debug-score').text(percent + '%');
        var $red = $('#traffic-light-red');
        var $yellow = $('#traffic-light-yellow');
        var $green = $('#traffic-light-green');

        var active = { 'opacity': '1', 'transform': 'scale(1.3)', 'box-shadow': '0 0 10px rgba(0,0,0,0.2)' };
        var inactive = { 'opacity': '0.2', 'transform': 'scale(1)', 'box-shadow': 'none' };

        if (percent >= 80) {
            $red.css(inactive); $yellow.css(inactive);
            $green.css(active).css('box-shadow', '0 0 10px #28a745');
        } else if (percent >= 40) {
            $red.css(inactive);
            $yellow.css(active).css('box-shadow', '0 0 10px #ffc107');
            $green.css(inactive);
        } else {
            $red.css(active).css('box-shadow', '0 0 10px #dc3545');
            $yellow.css(inactive); $green.css(inactive);
        }
    }

    timer = setInterval(checkForm, 500);
    checkForm();
})();
JS;
$this->registerJs($script);
?>
