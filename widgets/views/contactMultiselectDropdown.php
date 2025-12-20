<?php

use humhub\modules\ui\form\widgets\MultiSelect;
use yii\base\Model;

/* @var $model Model */
/* @var $attribute string */
/* @var $items array */
/* @var $options array */

echo MultiSelect::widget([
    'model' => $model,
    'attribute' => $attribute,
    'items' => $items,
    'options' => $options,
]);
// TODO: Nicht existirerende Strings nciht abschickbar machen



