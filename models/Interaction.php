<?php

namespace humhub\modules\crm\models;

use HttpException;
use humhub\modules\crm\models\traits\LinkableTrait;
use humhub\modules\crm\permissions\ManageCrmData;
use humhub\modules\space\models\Membership;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\topic\models\Topic;
use Yii;
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
    public $wallEntryClass = 'humhub\modules\crm\widgets\InteractionWallEntry';

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
    public $responsibleUserGuids = [];
    public $contactIds = [];
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

            // ensure fields are treated as arrays or safe even if empty
            [['responsibleUserGuids', 'contactIds'], 'default', 'value' => []],
            [['topics', 'responsibleUserGuids', 'contactIds', 'newLinks', 'editLinks'], 'safe'],            [['event_id'], 'integer'],
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
            'responsibleUserGuids' => 'Verantwortliche Nutzer',
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
        // unlink first to handle removals
        $this->unlinkAll('contacts', true);

        // ensure handling of arrays and comma-separated strings
        if (!empty($this->contactIds)) {
            $cIds = is_array($this->contactIds) ? $this->contactIds : explode(',', $this->contactIds);
            foreach ($cIds as $cId) {
                $contact = Contact::findOne(trim($cId));
                if ($contact) {
                    $this->link('contacts', $contact);
                }
            }
        }

        // save organizations
        // (automatically derived from selected contacts)
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

    public function canEdit()
    {
        $user = Yii::$app->user->getIdentity();
        if (!$user) {
            return false;
        }

        // space-admins / -owner
        if ($this->content->container->permissionManager->can(new ManageCrmData())) {
            return true;
        }

        // content creator
        if ($this->content->created_by == $user->id) {
            return true;
        }

        // respnsibleUser for this Interaction
        if ($this->getResponsibleUsers()->where(['user.id' => $user->id])->exists()) {
            return true;
        }

        // OR responsibleUser for affected organizations
        // if users at least responsible for one organization of the addressed contacts,
        // he/she can edit the interaction
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

        // admin / owner
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
        foreach ($this->responsibleUsers as $user) {
            $this->unlink('responsibleUsers', $user, true);
        }

        return $this->hardDelete();
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

    /**
     * calculates the quality score in percent
     * Mandatory fields give fewer points to ensure initial state is red.
     */
    public function getQualityScore()
    {
        $points = 0;
        $maxPoints = 7;

        // mandatory fields give 0
        //if (!empty($this->title)) $points += 0;
        //if (!empty($this->date)) $points += 0;

        // basic fields
        // if (!empty($this->time)) $points += 0;
        if (!empty($this->channel)) $points += 1;
        if (!empty($this->status)) $points += 1;

        // relations / arrays
        // check both relation (for list view) and attribute (for form save)
        if (!empty($this->contacts) || !empty($this->contactIds)) $points += 3;
        if (!empty($this->responsibleUsers) || !empty($this->responsibleUserGuids)) $points += 1;

        // content
        if ($this->status === self::STATUS_DONE) {
            if (!empty($this->result) && trim(strip_tags($this->result)) !== '') $points += 1;
        } else {
            if (!empty($this->description) && trim(strip_tags($this->description)) !== '') $points += 1;
        }

        $percent = round(($points / $maxPoints) * 100);
        return min(100, $percent);
    }

    /**
     * returns the hex color code based on the score
     */
    public function getQualityColor()
    {
        $score = $this->getQualityScore();
        if ($score >= 80) return '#28a745';
        if ($score >= 40) return '#ffc107';
        return '#dc3545';
    }
}
