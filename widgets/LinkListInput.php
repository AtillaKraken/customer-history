<?php

namespace humhub\modules\crm\widgets;

use humhub\components\Widget;

class LinkListInput extends Widget
{
    /**
     * @var \yii\db\ActiveRecord Model of given Entity (Event, Interaction...)
     */
    public $model;

    /**
     * @var string Attribute name for new Links (Standard: newLinks)
     */
    public $attributeNew = 'newLinks';

    /**
     * @var string Attribut name for edited Links (Standard: editLinks)
     */
    public $attributeEdit = 'editLinks';

    public function run()
    {
        return $this->render('linkListInput', [
            'model' => $this->model,
            'formName' => $this->model->formName(),
            'attributeNew' => $this->attributeNew,
            'attributeEdit' => $this->attributeEdit,
            'links' => $this->model->externalLinks // use relation from the trait
        ]);
    }
}
