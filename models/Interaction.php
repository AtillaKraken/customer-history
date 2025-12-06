<?php

namespace app\modules\crm\models;

use Yii;
use humhub\modules\content\components\ContentActiveRecord;

/**
 * This is the model class for table "crm_interaction".
 *
 * @property int $id
 * @property string $date
 * @property string|null $time
 * @property string $title
 * @property string|null $channel
 * @property string|null $status
 * @property string|null $description
 * @property string|null $result
 * @property string|null $links
 *
 * @property Contact[] $contacts
 * @property Organization[] $organizations
 * @property User[] $users
 */
class Interaction extends ContentActiveRecord
{
    public static function tableName()
    {
        return 'crm_interaction';
    }

    public function rules()
    {
        return [
            [['time', 'channel', 'description', 'result', 'links'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'PLANNED'],
            [['date', 'title'], 'required'],
            [['date', 'time'], 'safe'],
            [['description', 'result', 'links'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['channel', 'status'], 'string', 'max' => 50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
            'time' => 'Time',
            'title' => 'Title',
            'channel' => 'Channel',
            'status' => 'Status',
            'description' => 'Description',
            'result' => 'Result',
            'links' => 'Links',
        ];
    }

    public function getContacts()
    {
        return $this->hasMany(Contact::class, ['id' => 'contact_id'])
            ->viaTable('crm_interaction_contact', ['interaction_id' => 'id']);
    }

    public function getOrganizations()
    {
        return $this->hasMany(Organization::class, ['id' => 'organization_id'])
            ->viaTable('crm_interaction_organization', ['interaction_id' => 'id']);
    }

    public function getResponsibleUsers()
    {
        // HumHub User Model
        return $this->hasMany(\humhub\modules\user\models\User::class, ['id' => 'user_id'])
            ->viaTable('crm_interaction_responsible_user', ['interaction_id' => 'id']);
    }
}
