<?php

namespace app\modules\crm\models;

use humhub\modules\user\models\User;
use Yii;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\search\interfaces\Searchable; // required for  global search function

/**
 * This is the model class for table "crm_organization".
 *
 * @property int $id
 * @property string $name
 * @property string $category
 * @property string|null $industry
 * @property string|null $size
 * @property string|null $city
 * @property string|null $notes
 *
 * @property Contact[] $contacts
 * @property Event[] $events
 * @property-read string $contentName
 * @property-read array $searchAttributes
 * @property-read string $icon
 * @property-read string $description
 * @property-read mixed $responsibleUsers
 * @property Interaction[] $interactions
 */
class Organization extends ContentActiveRecord implements Searchable
{
    public $moduleId = 'crm';

    /**
     * @inheritdoc
     * TODO: Hier definieren wir später, wie der Eintrag auf der Wall (Stream) aussieht.
     * Aktuell nutzen wir den Standard, später bauen wir ein eigenes Widget.
     */
    // public $wallEntryClass = 'humhub\modules\crm\widgets\WallEntry';

    public static function tableName()
    {
        return 'crm_organization';
    }

    /**
     * @var array helper attribute for the form's UserPicker to save GUIDs.
     */
    public array $responsibleUserGuids = [];

    public function rules()
    {
        return [
            [['industry', 'size', 'city', 'notes'], 'default', 'value' => null],
            [['name', 'category'], 'required'],
            [['notes'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['category', 'industry', 'city'], 'string', 'max' => 100],
            [['size'], 'string', 'max' => 50],
            [['responsibleUserGuids'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Firmenname',
            'category' => 'Kategorie',
            'industry' => 'Branche',
            'size' => 'Größe',
            'city' => 'Stadt',
            'notes' => 'Notizen',
            'responsibleUserGuids' => 'Verantwortliche Nutzer',
        ];
    }

    /**
     * @inheritdoc
     * name of object in UI (eg entry in search or Activity Stream)
     */
    public function getContentName()
    {
        return 'Organisation';
    }

    /**
     *
     * desc for search results
     */
    public function getDescription()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     * icon next to the Organization
     */
    public function getIcon()
    {
        return 'fa-building';
    }

    /**
     * @inheritdoc
     * config for global search (Searchable Interface)
     */
    public function getSearchAttributes()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'city' => $this->city,
            'industry' => $this->industry,
            'notes' => $this->notes,
        ];
    }

    public function getContacts()
    {
        return $this->hasMany(Contact::class, ['organization_id' => 'id']);
    }

    public function getInteractions()
    {
        return $this->hasMany(Interaction::class, ['id' => 'interaction_id'])
            ->viaTable('crm_interaction_organization', ['organization_id' => 'id']);
    }

    public function getEvents()
    {
        return $this->hasMany(Event::class, ['id' => 'event_id'])
            ->viaTable('crm_event_organization', ['organization_id' => 'id']);
    }

    // Relation to HumHub Users (ResponsibleUser)
    public function getResponsibleUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->viaTable('crm_organization_user', ['organization_id' => 'id']);
    }

    /**
     * load GUIDs after finding them in  responsibleUserGuids
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->responsibleUserGuids = array_map(function($user) {
            return $user->guid;
        }, $this->responsibleUsers);
    }

    /**
     * save links after saving
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // delete old links
        $this->unlinkAll('responsibleUsers', true);

        // set new links
        if (!empty($this->responsibleUserGuids)) {
            $guids = is_array($this->responsibleUserGuids) ? $this->responsibleUserGuids : explode(',', $this->responsibleUserGuids);

            foreach ($guids as $guid) {
                $user = User::findOne(['guid' => trim($guid)]);
                if ($user) {
                    $this->link('responsibleUsers', $user);
                }
            }
        }
    }

}
