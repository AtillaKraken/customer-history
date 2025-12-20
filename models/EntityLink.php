<?php

namespace app\modules\crm\models;

use humhub\components\ActiveRecord;

/**
 * corresponding with 'crm_entity_link' from
 * m231207_120000_create_crm_entity_link_table.php
 */
class EntityLink extends ActiveRecord
{
    public static function tableName()
    {
        return 'crm_entity_link';
    }

    public function rules()
    {
        return [
            [['object_model', 'object_id', 'url'], 'required'],
            [['object_id'], 'integer'],
            [['url'], 'string', 'max' => 255],
            [['title'], 'string', 'max' => 255],
        ];
    }
}
