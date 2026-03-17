<?php

namespace humhub\modules\crm\models;

use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\space\models\Space;
use humhub\modules\space\models\Membership;
use humhub\modules\crm\permissions\ManageCrmData;
use Yii;

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
 * @property-read string $contentName
 * @property-read mixed $url
 * @property-read null|string $contentDescription
 * @property Organization $organization
 */
class Contact extends ContentActiveRecord
{
    public $moduleId = 'crm';

    public $wallEntryClass = 'humhub\modules\crm\widgets\ContactWallEntry';

    // Roles:
    const ROLE_MANAGEMENT = 'Geschäftsführung / Vorstand';
    const ROLE_HEAD_OF = 'Abteilungs- / Bereichsleitung';
    const ROLE_PROJECT_LEAD = 'Projektleitung';
    const ROLE_OPERATIONS = 'Operativ / Service / Support';
    const ROLE_ASSISTANCE = 'Assistenz / Sekretariat';
    const ROLE_SALES = 'Vertrieb / Sales';
    const ROLE_PURCHASING = 'Einkauf';
    const ROLE_FINANCE = 'Finanzen / Controlling';
    const ROLE_LEGAL = 'Recht / Verträge';
    const ROLE_HR = 'Personal / HR';
    const ROLE_MARKETING = 'Marketing / Kommunikation / PR';
    const ROLE_IT = 'IT / Administration';
    const ROLE_R_AND_D = 'Forschung & Entwicklung / Produkt';
    const ROLE_OTHER = 'Sonstiges';

    private const ROLES = [
        self::ROLE_MANAGEMENT,
        self::ROLE_HEAD_OF,
        self::ROLE_PROJECT_LEAD,
        self::ROLE_OPERATIONS,
        self::ROLE_ASSISTANCE,
        self::ROLE_SALES,
        self::ROLE_PURCHASING,
        self::ROLE_FINANCE,
        self::ROLE_LEGAL,
        self::ROLE_HR,
        self::ROLE_MARKETING,
        self::ROLE_IT,
        self::ROLE_R_AND_D,
        self::ROLE_OTHER,
    ];

    // Gender:
    const GENDER_FEMALE = 'Weiblich';
    const GENDER_MALE = 'Männlich';
    const GENDER_DIVERSE = 'Divers';
    const GENDER_NOT_SPECIFIED = 'Keine Angabe';

    private const GENDERS = [
        self::GENDER_FEMALE,
        self::GENDER_MALE,
        self::GENDER_DIVERSE,
        self::GENDER_NOT_SPECIFIED,
    ];

    public function getUrl()
    {
        return $this->content->container->createUrl('/crm/contact/view', ['id' => $this->id]);
    }

    /**
     * @return string|null Name to display - usually the name from the db, but in case of an empty field (e.g. DSGVO reasons), display the ID instead
     */
    public function getDisplayName()
    {
        return !empty($this->name)
            ? $this->name
            : 'ID: ' . $this->id;
    }

    public function getContentName()
    {
        return 'Kontaktperson';
    }

    public function getContentDescription()
    {
        return $this->getDisplayName();
    }

    /**
     * @inheritdoc
     * icon next to the Contact
     */
    public function getIcon()
    {
        return 'fa-user';
    }

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
        return array_combine(self::ROLES, self::ROLES);
    }

    public static function getGenderOptions()
    {
        return array_combine(self::GENDERS, self::GENDERS);
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

    /**
     * return Events where a certain Contact participated in
     * */
    public function getParticipations()
    {
        return $this->hasMany(Event::class, ['id' => 'event_id'])
            ->viaTable('crm_event_contact', ['contact_id' => 'id'])
            ->orderBy(['date' => SORT_DESC]);
    }

    public function canEdit()
    {
        $user = Yii::$app->user->getIdentity();
        if (!$user) {
            return false;
        }

        // admins / owner (via custom Permission)
        if ($this->content->container->permissionManager->can(new ManageCrmData())) {
            return true;
        }

        // content creator
        if ($this->content->created_by == $user->id) {
            return true;
        }

        // responsible Users
        if ($this->isResponsible($user->id)) {
            return true;
        }

        return false;
    }

    public function canDelete()
    {
        $user = Yii::$app->user->getIdentity();
        if (!$user) {
            return false;
        }

        // admins / owner (via custom Permission)
        if ($this->content->container->permissionManager->can(new \humhub\modules\crm\permissions\ManageCrmData())) {
            return true;
        }

        // moderators
        if ($this->content->container instanceof Space) {
            $membership = Membership::findOne(['space_id' => $this->content->container->id, 'user_id' => $user->id]);
            if ($membership && $membership->group_id === Space::USERGROUP_MODERATOR) {
                return true;
            }
        }

        // content creator
        if ($this->content->created_by == $user->id) {
            return true;
        }

        return false;
    }

    /**
     * override standard soft-deletion with hard delete - includes unlinking of pivots
     */
    public function delete()
    {
        foreach ($this->interactions as $interaction) {
            $this->unlink('interactions', $interaction, true);
        }
        foreach ($this->events as $event) {
            $this->unlink('events', $event, true);
        }

        return $this->hardDelete();
    }

    /**
     * helper: is the user responsible for this contact's organization?
     */
    public function isResponsible($userId)
    {
        if ($this->organization) {
            return $this->organization->isResponsible($userId);
        }

        return false;
    }

}
