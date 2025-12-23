<?php

namespace app\modules\crm\models;

use humhub\modules\crm\models\traits\LinkableTrait;
use humhub\modules\topic\models\Topic;
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
    use LinkableTrait;

    public static function tableName()
    {
        return 'crm_event';
    }

    // Type Constants
    const TYPE_NETWORKING = 'networking';
    const TYPE_WORKSHOP = 'workshop';
    const TYPE_PRESENTATION = 'presentation';
    const TYPE_CAREER = 'career';
    const TYPE_CAMPUSTOUR = 'campustour';
    const TYPE_PROJECT = 'project';
    const TYPE_CONFERENCE = 'conference';
    const TYPE_MEDIA = 'media';
    const TYPE_MARKETING = 'marketing';
    const TYPE_OTHER = 'other';

    public $topics = []; // helper array for the form/modal
    public array $contactIds = []; // helper for contact assignment

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
            [['topics', 'contactIds', 'newLinks', 'editLinks'], 'safe'],
        ];
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
        return [
            self::TYPE_NETWORKING => 'Netzwerktreffen',
            self::TYPE_WORKSHOP => 'Workshop',
            self::TYPE_PRESENTATION => 'Präsentation',
            self::TYPE_CAREER => 'Karriere-Event',
            self::TYPE_CAMPUSTOUR => 'Campustour',
            self::TYPE_PROJECT => 'Projektmeeting',
            self::TYPE_CONFERENCE => 'Fachtagung/Messe',
            self::TYPE_MEDIA => 'Presse- & Medienformat',
            self::TYPE_MARKETING => 'Marketing-/PR-Aktion',
            self::TYPE_OTHER => 'Sonstige',
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

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) return false;

        // format date for database (dd.mm.YYYY -> YYYY-mm-dd)
        if ($this->date) {
            $dt = \DateTime::createFromFormat('d.m.Y', $this->date);
            if ($dt) {
                $this->date = $dt->format('Y-m-d');
            }
        }
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
}
