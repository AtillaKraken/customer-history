<?php

namespace app\modules\crm\models;

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
            [['organization_id', 'roleList'], 'required'],
            [['organization_id'], 'integer'],
            [['roles', 'note'], 'string'],
            [['name', 'email'], 'string', 'max' => 255],
            [['gender'], 'string', 'max' => 20],
            [['phone_number'], 'string', 'max' => 100],
            [['roleList'], 'safe'],
            [['organization_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::class, 'targetAttribute' => ['organization_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'organization_id' => 'Organisation',
            'name' => 'Name',
            'roles' => 'Rollen',
            'gender' => 'Geschlecht',
            'email' => 'E-Mail',
            'phone_number' => 'Telefon-Nr',
            'note' => 'Notizfeld',
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

    // Array of Roles (used in Role-Multiselect)
    public $roleList = [];

    /**
     * @return string[] Array of selectable roles
     */
    public static function getRoleOptions()
    {
        return [
            // Decision makers
            'MANAGEMENT' => 'Geschäftsführung / Vorstand',
            'HEAD_OF' => 'Abteilungs- / Bereichsleitung',

            // Key roles
            'PROJECT_LEAD' => 'Projektleitung',
            'OPERATIONS' => 'Operativ / Service / Support',
            'ASSISTANCE' => 'Assistenz / Sekretariat',

            // Commercial
            'SALES' => 'Vertrieb / Sales',
            'PURCHASING' => 'Einkauf',
            'FINANCE' => 'Finanzen / Controlling',
            'LEGAL' => 'Recht / Verträge',

            // Departments
            'HR' => 'Personal / HR',
            'MARKETING' => 'Marketing / Kommunikation / PR',
            'IT' => 'IT / Admin',
            'R_AND_D' => 'Forschung & Entwicklung / Produkt', // Ergänzt: Falls IT zu eng gefasst ist

            // Fallback
            'OTHER' => 'Sonstiges',
        ];
    }

    // After finding from db: String -> Array
    public function afterFind()
    {
        parent::afterFind();
        if (!empty($this->roles)) {
            $this->roleList = explode(',', $this->roles);
        }
    }

    // Before saving: Array -> String
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (is_array($this->roleList)) {
            $this->roles = implode(',', $this->roleList);
        } else {
            $this->roles = '';
        }

        return true;
    }
}
