<?php

namespace app\modules\crm\models;

use Yii;
use humhub\modules\content\components\ContentActiveRecord;

/**
 * This is the model class for table "crm_contact".
 *
 * @property int $id
 * @property int $organization_id
 * @property string|null $name
 * @property string $roles
 * @property string|null $gender
 * @property string|null $email
 * @property string|null $phone_number
 * @property string|null $note
 *
 * @property EventContact[] $crmEventContacts
 * @property InteractionContact[] $crmInteractionContacts
 * @property Event[] $events
 * @property Interaction[] $interactions
 * @property Organization $organization
 */
class Contact extends ContentActiveRecord
{
    public static function tableName()
    {
        return 'crm_contact';
    }

    public function rules()
    {
        return [
            [['name', 'gender', 'email', 'phone_number', 'note'], 'default', 'value' => null],
            [['organization_id', 'roles'], 'required'],
            [['organization_id'], 'integer'],
            [['roles', 'note'], 'string'],
            [['name', 'email'], 'string', 'max' => 255],
            [['gender'], 'string', 'max' => 20],
            [['phone_number'], 'string', 'max' => 100],
            [['organization_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::class, 'targetAttribute' => ['organization_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'organization_id' => 'Organization',
            'name' => 'Name',
            'roles' => 'Roles',
            'gender' => 'Gender',
            'email' => 'Email',
            'phone_number' => 'Phone',
            'note' => 'Note',
        ];
    }

    public function getEvents()
    {
        return $this->hasMany(Event::class, ['id' => 'event_id'])
            ->viaTable('crm_event_contact', ['contact_id' => 'id']);
    }

    public function getInteractions()
    {
        return $this->hasMany(Interaction::class, ['id' => 'interaction_id'])
            ->viaTable('crm_interaction_contact', ['contact_id' => 'id']);
    }

    public function getOrganization()
    {
        return $this->hasOne(Organization::class, ['id' => 'organization_id']);
    }
}
