<?php

namespace app\modules\crm\models;

use HttpException;
use humhub\modules\crm\models\traits\LinkableTrait;
use humhub\modules\user\models\User;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\topic\models\Topic;

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
    public $wallEntryClass = 'app\modules\crm\widgets\WallEntry';

    // define target of Notification-Links
    public function getUrl()
    {
        return $this->content->container->createUrl('/crm/interaction/view', ['id' => $this->id]);
    }

    // Statuses:
    const STATUS_PLANNED = 'PLANNED';
    const STATUS_OVERDUE = 'OVERDUE';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_DONE = 'DONE';

    // Channels:
    const CHANNEL_EMAIL = 'EMAIL';
    const CHANNEL_PHONE = 'PHONE';
    const CHANNEL_VIDEO = 'VIDEO_CONF';
    const CHANNEL_IN_PERSON = 'IN_PERSON';
    const CHANNEL_EVENT = 'EVENT';
    const CHANNEL_SOCIAL = 'SOCIAL_MEDIA';
    const CHANNEL_MESSENGER = 'MESSENGER';
    const CHANNEL_LETTER = 'LETTER';
    const CHANNEL_NEWSLETTER = 'NEWSLETTER';
    const CHANNEL_OTHER = 'OTHER';


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

    /**
     * @return string[] array of applicable statuses
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PLANNED => 'Geplant',
            self::STATUS_OVERDUE => 'Überfällig',
            self::STATUS_CANCELLED => 'Abgesagt',
            self::STATUS_DONE => 'Erledigt',
        ];
    }

    /**
     * @return string[] array of applicable channels
     */
    public static function getChannelOptions()
    {
        return [
            self::CHANNEL_EMAIL => 'E-Mail',
            self::CHANNEL_PHONE => 'Telefon',
            self::CHANNEL_VIDEO => 'Videokonferenz',
            self::CHANNEL_IN_PERSON => 'Persönliches Treffen',
            self::CHANNEL_EVENT => 'Veranstaltung',
            self::CHANNEL_SOCIAL => 'Social Media',
            self::CHANNEL_MESSENGER => 'HumHub-Messenger',
            self::CHANNEL_LETTER => 'Brief',
            self::CHANNEL_NEWSLETTER => 'Newsletter',
            self::CHANNEL_OTHER => 'Sonstiges',
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
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->viaTable('crm_interaction_responsible_user', ['interaction_id' => 'id']);
    }

    public function getEvent()
    {
        return $this->hasOne(Event::class, ['id' => 'event_id']);
    }

    public function actionView($id)
    {
        $model = Interaction::find()
            ->contentContainer($this->contentContainer)
            ->where(['crm_interaction.id' => $id])
            ->one();

        if (!$model) {
            throw new HttpException(404);
        }

        if (!$model->content->canView()) {
            throw new HttpException(403);
        }

        return $this->render('view', ['model' => $model]);
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
            }
        }
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
}
