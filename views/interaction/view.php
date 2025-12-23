<?php
use app\modules\crm\widgets\InteractionCard;

/* @var $model app\modules\crm\models\Interaction */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?= InteractionCard::widget(['interaction' => $model]) ?>
        </div>
    </div>
</div>
