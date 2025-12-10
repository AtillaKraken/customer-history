<?php

namespace app\modules\crm\models;

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
 * @property User[] $users
 */
class Interaction extends ContentActiveRecord
{
    use LinkableTrait;

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
            [['status'], 'default', 'value' => 'PLANNED'],
            [['date', 'time'], 'safe'],
            [['description', 'result', 'links'], 'string'],
            [['channel', 'status'], 'string', 'max' => 50],
            [['topics', 'responsibleUserGuids', 'newLinks', 'editLinks'], 'safe'],
            [['event_id'], 'integer'],
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
            'event_id' => 'EventID',
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

    public function afterFind()
    {
        parent::afterFind();
        // Lade die GUIDs für das Formular
        $this->responsibleUserGuids = array_map(function ($user) {
            return $user->guid;
        }, $this->responsibleUsers);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $this->saveLinks();
        Topic::attach($this->content, $this->topics);

        // save contacts
        // TODO: contactIds vom Formular übermitteln lassen
        if (is_array($this->contactIds)) {
            $this->unlinkAll('contacts', true);
            foreach ($this->contactIds as $cId) {
                $contact = Contact::findOne($cId);
                if ($contact) {
                    $this->link('contacts', $contact);
                }
            }
        }

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
