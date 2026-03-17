<?php

namespace humhub\modules\crm\models;

use humhub\modules\crm\models\traits\LinkableTrait;
use humhub\modules\topic\models\Topic;
use Yii;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\space\models\Space;
use humhub\modules\space\models\Membership;
use humhub\modules\crm\permissions\ManageCrmData;
use yii\base\InvalidConfigException;

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
 * @property-read string $contentName
 * @property-read mixed $url
 * @property-read string $contentDescription
 * @property Organization[] $organizations
 */
class Event extends ContentActiveRecord
{
    public $moduleId = 'crm';

    use LinkableTrait;

    // define Widget for Stream
    public $wallEntryClass = 'humhub\modules\crm\widgets\EventWallEntry';

    // define target of Notification-Links
    public function getUrl()
    {
        return $this->content->container->createUrl('/crm/event/view', ['id' => $this->id]);
    }

    public static function tableName()
    {
        return 'crm_event';
    }

    // Type Constants
    public const TYPE_NETWORKING = 'Netzwerktreffen';
    public const TYPE_WORKSHOP = 'Workshop';
    public const TYPE_PRESENTATION = 'Präsentation';
    public const TYPE_CAREER = 'Karriere-Event';
    public const TYPE_CAMPUSTOUR = 'Campustour';
    public const TYPE_PROJECT = 'Projektmeeting';
    public const TYPE_CONFERENCE = 'Fachtagung / Messe';
    public const TYPE_MEDIA = 'Presse- & Medienformat';
    public const TYPE_MARKETING = 'Marketing- / PR-Aktion';
    public const TYPE_OTHER = 'Sonstiges';
    private const TYPES = [
        self::TYPE_NETWORKING,
        self::TYPE_WORKSHOP,
        self::TYPE_PRESENTATION,
        self::TYPE_CAREER,
        self::TYPE_CAMPUSTOUR,
        self::TYPE_PROJECT,
        self::TYPE_CONFERENCE,
        self::TYPE_MEDIA,
        self::TYPE_MARKETING,
        self::TYPE_OTHER,
    ];

    public $topics = []; // helper array for the form/modal
    public $contactIds = []; // helper for contact assignment

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
            ['type', 'in', 'range' => array_keys(self::getTypeOptions())],
            [['contactIds'], 'default', 'value' => []],
            [['topics', 'contactIds', 'newLinks', 'editLinks'], 'safe'],
        ];
    }

    // necessary to display correct texts in streams and activity widgets
    public function getContentName()
    {
        return 'CRM-Veranstaltung';
    }

    // necessary to display correct name of an entry in streams and activity widgets
    public function getContentDescription()
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     * icon next to the Event
     */
    public function getIcon()
    {
        return 'fa-calendar';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Titel',
            'date' => 'Datum',
            'time' => 'Zeit',
            'type' => 'Format',
            'description' => 'Beschreibung',
            'links' => 'Links',
            'calendar_entry_id' => 'Zug. Kalender-Eintrag',
            'topics' => 'Themen',
        ];
    }

    /**
     * @return array
     */
    public static function getTypeOptions()
    {
        return array_combine(self::TYPES, self::TYPES);
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

    public function afterFind()
    {
        parent::afterFind();
        // load topics
        $this->topics = Topic::findByContent($this->content)->all();

        // load contact IDs
        $this->contactIds = array_map(function ($contact) {
            return $contact->id;
        }, $this->contacts);

        // format date (for readability)
        if ($this->date) {
            $this->date = \Yii::$app->formatter->asDate($this->date, 'php:d.m.Y');
        }
    }

    public function beforeValidate()
    {
        // ensure contactIds is always an array (handle empty strings from form)
        if (is_string($this->contactIds)) {
            if (empty($this->contactIds)) {
                $this->contactIds = [];
            } else {
                $this->contactIds = explode(',', $this->contactIds);
            }
        } elseif (!is_array($this->contactIds)) {
            $this->contactIds = [];
        }

        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // format date for database (dd.mm.YYYY -> YYYY-mm-dd)
        if ($this->date) {
            $dt = \DateTime::createFromFormat('d.m.Y', $this->date);
            if ($dt) {
                $this->date = $dt->format('Y-m-d');
            }
        }

        // force private-visibility
        $this->content->visibility = \humhub\modules\content\models\Content::VISIBILITY_PRIVATE;

        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->saveLinks(); // save links
        \humhub\modules\topic\models\Topic::attach($this->content, $this->topics); // save topcis

        // save contacts
        if (is_array($this->contactIds)) {
            $this->unlinkAll('contacts', true);
            foreach ($this->contactIds as $cId) {
                $contact = Contact::findOne($cId);
                if ($contact) {
                    $this->link('contacts', $contact);
                }
            }
        }

        // save organizations (determined by selected contacts)
        // => ensures orga links persist even if contact is deleted later from the DB (due to e.g. DSGVO)
        $this->unlinkAll('organizations', true);
        foreach ($this->contacts as $contact) {
            if ($contact->organization) {
                // avoid duplicates
                $exists = $this->getOrganizations()->where(['id' => $contact->organization->id])->exists();
                if (!$exists) {
                    $this->link('organizations', $contact->organization);
                }
            }
        }
    }

    /**
     * @throws \Throwable
     * @throws InvalidConfigException
     */
    public function canEdit()
    {
        $user = Yii::$app->user->getIdentity();
        if (!$user) {
            return false;
        }

        // space admins and owner
        if ($this->content->container->permissionManager->can(new ManageCrmData())) {
            return true;
        }

        // contnet creator
        if ($this->content->created_by == $user->id) {
            return true;
        }

        // responsibleUser for participating Organizations
        // if users at least responsible for one organizaion of the participating contacts,
        // he/she can edit the event
        foreach ($this->organizations as $org) {
            if ($org->isResponsible($user->id)) {
                return true;
            }
        }

        return false;
    }
    public function canDelete()
    {
        $user = Yii::$app->user->getIdentity();
        if (!$user) {
            return false;
        }

        // admins/owner
        if ($this->content->container->permissionManager->can(new ManageCrmData())) {
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
        foreach ($this->contacts as $contact) {
            $this->unlink('contacts', $contact, true);
        }
        foreach ($this->organizations as $org) {
            $this->unlink('organizations', $org, true);
        }

        return $this->hardDelete();
    }

}
