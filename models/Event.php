<?php

namespace app\modules\crm\models;

use Yii;
use humhub\modules\content\components\ContentActiveRecord;

/**
 * This is the model class for table "crm_event".
 *
 * @property int $id
 * @property string $title
 * @property string $date
 * @property string|null $time
 * @property string|null $type
 * @property string|null $description
 * @property string|null $links
 * @property int|null $calendar_entry_id
 *
 * @property Contact[] $contacts
 * @property EventContact[] $crmEventContacts
 * @property EventOrganization[] $crmEventOrganizations
 * @property Organization[] $organizations
 */
class Event extends ContentActiveRecord
{
    public static function tableName()
    {
        return 'crm_event';
    }

    public function rules()
    {
        return [
            [['time', 'type', 'description', 'links', 'calendar_entry_id'], 'default', 'value' => null],
            [['title', 'date'], 'required'],
            [['date', 'time'], 'safe'],
            [['description', 'links'], 'string'],
            [['calendar_entry_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'date' => 'Date',
            'time' => 'Time',
            'type' => 'Type',
            'description' => 'Description',
            'links' => 'Links',
            'calendar_entry_id' => 'Calendar Entry',
        ];
    }

    public function getContacts()
    {
        return $this->hasMany(Contact::class, ['id' => 'contact_id'])
            ->viaTable('crm_event_contact', ['event_id' => 'id']);
    }

    public function getOrganizations()
    {
        return $this->hasMany(Organization::class, ['id' => 'organization_id'])
            ->viaTable('crm_event_organization', ['event_id' => 'id']);
    }
}
