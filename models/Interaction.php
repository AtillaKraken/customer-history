<?php

namespace app\modules\crm\models;

use HttpException;
use humhub\modules\crm\models\traits\LinkableTrait;
use humhub\modules\user\models\User;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\topic\models\Topic;
use yii\db\Exception;

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
 * @property-read mixed $event
 * @property-read mixed $responsibleUsers
 * @property User[] $users
 */
class Interaction extends ContentActiveRecord
{
    use LinkableTrait;

    // define Widget for Stream
    public $wallEntryClass = 'app\modules\crm\widgets\InteractionWallEntry';

    // Status:
    const STATUS_PLANNED = 'Geplant';
    const STATUS_OVERDUE = 'Überfällig';
    const STATUS_CANCELLED = 'Abgesagt';
    const STATUS_DONE = 'Erledigt';

    private const STATUSES = [
        self::STATUS_PLANNED,
        self::STATUS_OVERDUE,
        self::STATUS_CANCELLED,
        self::STATUS_DONE,
    ];

    // Channels:
    const CHANNEL_EMAIL = 'E-Mail';
    const CHANNEL_PHONE = 'Telefon';
    const CHANNEL_VIDEO = 'Videokonferenz';
    const CHANNEL_IN_PERSON = 'Persönliches Treffen';
    const CHANNEL_EVENT = 'Veranstaltung';
    const CHANNEL_SOCIAL = 'Social Media';
    const CHANNEL_MESSENGER = 'Messenger';
    const CHANNEL_LETTER = 'Brief';
    const CHANNEL_NEWSLETTER = 'Newsletter';
    const CHANNEL_OTHER = 'Sonstiges';

    private const CHANNELS = [
        self::CHANNEL_EMAIL,
        self::CHANNEL_PHONE,
        self::CHANNEL_VIDEO,
        self::CHANNEL_IN_PERSON,
        self::CHANNEL_EVENT,
        self::CHANNEL_SOCIAL,
        self::CHANNEL_MESSENGER,
        self::CHANNEL_LETTER,
        self::CHANNEL_NEWSLETTER,
        self::CHANNEL_OTHER,
    ];

    public static function getChannelOptions()
    {
        return array_combine(self::CHANNELS, self::CHANNELS);
    }


    public static function getStatusOptions()
    {
        return array_combine(self::STATUSES, self::STATUSES);
    }


    // define target of Notification-Links
    public function getUrl()
    {
        return $this->content->container->createUrl('/crm/interaction/view', ['id' => $this->id]);
    }


    public static function tableName()
    {
        return 'crm_interaction';
    }

    /**
     * @var array helper attribute for the form's UserPicker to save GUIDs.
     */
    public array $responsibleUserGuids = [];
    public array $contactIds = [];
    public $topics = [];

    public function rules()
    {
        return [
            [['date', 'title'], 'required'],
            [['title'], 'string', 'max' => 255],
            [['time', 'channel', 'description', 'result', 'links'], 'default', 'value' => null],
            ['status', 'in', 'range' => array_keys(self::getStatusOptions())],
            ['status', 'default', 'value' => self::STATUS_PLANNED],
            [['date', 'time'], 'safe'],
            [['description', 'result', 'links'], 'string'],
            [['channel', 'status'], 'string', 'max' => 50],
            ['channel', 'in', 'range' => array_keys(self::getChannelOptions())],
            [['topics', 'responsibleUserGuids', 'contactIds', 'newLinks', 'editLinks'], 'safe'],
            [['event_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Datum',
            'time' => 'Zeit',
            'title' => 'Titel',
            'channel' => 'Kanal',
            'status' => 'Status',
            'description' => 'Beschreibung',
            'result' => 'Ergebnis',
            'links' => 'Links',
            'event_id' => 'EventID',
        ];
    }

    // necessary to display correct texts in streams and activity widgets
    public function getContentName()
    {
        return 'Interaktion';
    }
    // necessary to display correct name of an entry in streams and activity widgets
    public function getContentDescription()
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     * icon next to the Interaction
     */
    public function getIcon()
    {
        return 'fa-comments-o';
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
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->viaTable('crm_interaction_responsible_user', ['interaction_id' => 'id']);
    }

    public function getEvent()
    {
        return $this->hasOne(Event::class, ['id' => 'event_id']);
    }

    public function afterFind()
    {
        parent::afterFind();
        // load GUIDs/responsibleUsers for the form
        $this->responsibleUserGuids = array_map(function ($user) {
            return $user->guid;
        }, $this->responsibleUsers);

        // load contacts
        $this->contactIds = array_map(function ($contact) {
            return $contact->id;
        }, $this->contacts);

        // load topics
        $this->topics = Topic::findByContent($this->content)->all();

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

                // auto-overdue logic: if date is in the past AND status is currently PLANNED, set it to overdue
                // => to prevent interactions labeled as "planned" even though there are set in the past
                $today = new \DateTime('today');
                if ($dt < $today && $this->status === self::STATUS_PLANNED) {
                    $this->status = self::STATUS_OVERDUE;
                }
            }
        }

        // force private-visibility (=> only space-members allowed!)
        $this->content->visibility = \humhub\modules\content\models\Content::VISIBILITY_PRIVATE;
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $this->saveLinks();
        Topic::attach($this->content, $this->topics);

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
        // TODO: Kontaktzuweisung fixen!

        // save organizations of applied contacts in interaction
        $this->unlinkAll('organizations', true);
        foreach ($this->contacts as $contact) {
            if ($contact->organization) {
                // check if organizatonId/entry already exists to avoid duplicates:
                $exists = $this->getOrganizations()->where(['id' => $contact->organization->id])->exists();

                if (!$exists) {
                    $this->link('organizations', $contact->organization);
                }
            }
        }

        // save responsibleUsers
        $this->unlinkAll('responsibleUsers', true);
        if (!empty($this->responsibleUserGuids)) {
            $guids = is_array($this->responsibleUserGuids) ? $this->responsibleUserGuids : explode(',', $this->responsibleUserGuids);
            foreach ($guids as $guid) {
                $user = User::findOne(['guid' => trim($guid)]);
                if ($user) $this->link('responsibleUsers', $user);
            }
        }
    }

    /**
     * @return int amount of affected rows / interactions updated
     */
    public static function updateOverdueStatuses()
    {
        $count = 0;
        $today = new \DateTime('today');
        $todayStr = $today->format('Y-m-d');

        // find all planned, yet past interactions
        $overdueInteractions = self::find()
            ->where(['status' => self::STATUS_PLANNED])
            ->andWhere(['<', 'date', $todayStr])
            ->all();


        foreach ($overdueInteractions as $interaction) {
            $interaction->status = self::STATUS_OVERDUE;
            if ($interaction->save(false, ['status'])) {
                $count++;
            }
        }

        return $count;
    }
}
