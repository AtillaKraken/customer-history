<?php

namespace app\modules\crm\models;

use humhub\modules\user\models\User;
use Yii;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\search\interfaces\Searchable;

// required for  global search function

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
    // Category:
    const CATEGORY_COMPANY = 'Unternehmen';
    const CATEGORY_ASSOCIATION = 'Verband';
    const CATEGORY_SCHOOL = 'Schule';
    const CATEGORY_COLLEGE = 'Hochschule';
    const CATEGORY_AUTHORITY = 'Behörde';
    const CATEGORY_PROJECT = 'Projekt';
    const CATEGORY_RESEARCH_PROJECT = 'Forschungsprojekt';
    private const CATEGORIES = [
        self::CATEGORY_COMPANY,
        self::CATEGORY_ASSOCIATION,
        self::CATEGORY_SCHOOL,
        self::CATEGORY_COLLEGE,
        self::CATEGORY_AUTHORITY,
        self::CATEGORY_PROJECT,
        self::CATEGORY_RESEARCH_PROJECT,
    ];


    // Industry:
    const INDUSTRY_IT_AND_SOFTWARE_DEVELOPMENT = 'IT & Softwareentwicklung';
    const INDUSTRY_MECHANICAL_ENGINEERING_AND_INDUSTRY = 'Maschinenbau & Industrie';
    const INDUSTRY_AUTOMOTIVE_AND_MOBILITY = 'Automobil & Mobilität';
    const INDUSTRY_ENERGY_AND_ENVIRONMENT = 'Energie & Umwelt';
    const INDUSTRY_CONSTRUCTION_AND_ARCHITECTURE = 'Bau & Architektur';
    const INDUSTRY_CRAFTS = 'Handwerk';
    const INDUSTRY_HEALTH_AND_CARE = 'Gesundheit & Pflege';
    const INDUSTRY_EDUCATION_AND_RESEARCH = 'Bildung & Forschung';
    const INDUSTRY_PUBLIC_SERVICE_AND_ADMINISTRATION = 'Öffentlicher Dienst & Verwaltung';
    const INDUSTRY_FINANCE_AND_INSURANCE = 'Finanzen & Versicherung';
    const INDUSTRY_RETAIL_AND_ECOMMERCE = 'Handel & E-Commerce';
    const INDUSTRY_LOGISTICS_AND_TRANSPORT = 'Logistik & Transport';
    const INDUSTRY_MARKETING_MEDIA_AND_COMMUNICATION = 'Marketing, Medien & Kommunikation';
    const INDUSTRY_CONSULTING_AND_SERVICES = 'Beratung & Dienstleistungen';
    const INDUSTRY_TOURISM_AND_GASTRONOMY = 'Tourismus & Gastronomie';
    const INDUSTRY_AGRICULTURE_AND_FOOD = 'Landwirtschaft & Ernährung';
    const INDUSTRY_CULTURE_AND_CREATIVE_INDUSTRIES = 'Kultur & Kreativwirtschaft';
    const INDUSTRY_NON_PROFIT_AND_ASSOCIATIONS = 'Non-Profit & Vereine';
    private const INDUSTRIES = [
        self::INDUSTRY_IT_AND_SOFTWARE_DEVELOPMENT,
        self::INDUSTRY_MECHANICAL_ENGINEERING_AND_INDUSTRY,
        self::INDUSTRY_AUTOMOTIVE_AND_MOBILITY,
        self::INDUSTRY_ENERGY_AND_ENVIRONMENT,
        self::INDUSTRY_CONSTRUCTION_AND_ARCHITECTURE,
        self::INDUSTRY_CRAFTS,
        self::INDUSTRY_HEALTH_AND_CARE,
        self::INDUSTRY_EDUCATION_AND_RESEARCH,
        self::INDUSTRY_PUBLIC_SERVICE_AND_ADMINISTRATION,
        self::INDUSTRY_FINANCE_AND_INSURANCE,
        self::INDUSTRY_RETAIL_AND_ECOMMERCE,
        self::INDUSTRY_LOGISTICS_AND_TRANSPORT,
        self::INDUSTRY_MARKETING_MEDIA_AND_COMMUNICATION,
        self::INDUSTRY_CONSULTING_AND_SERVICES,
        self::INDUSTRY_TOURISM_AND_GASTRONOMY,
        self::INDUSTRY_AGRICULTURE_AND_FOOD,
        self::INDUSTRY_CULTURE_AND_CREATIVE_INDUSTRIES,
        self::INDUSTRY_NON_PROFIT_AND_ASSOCIATIONS,
    ];


    // Size:
    const SIZE_XS = '1 - 5';
    const SIZE_S = '6 - 20';
    const SIZE_M = '21 - 50';
    const SIZE_L = '51 - 100';
    const SIZE_XL = '101 - 250';
    const SIZE_XXL = '251 - 1000';
    const SIZE_3XL = '+1000';
    private const SIZES = [
        self::SIZE_XS,
        self::SIZE_S,
        self::SIZE_M,
        self::SIZE_L,
        self::SIZE_XL,
        self::SIZE_XXL,
        self::SIZE_3XL,
    ];

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
            'name' => 'Name',
            'category' => 'Kategorie',
            'industry' => 'Branche',
            'size' => 'Mitarbeitenden-Anzahl',
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
        $this->responsibleUserGuids = array_map(function ($user) {
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

    public static function getCategoryOptions()
    {
        return array_combine(self::CATEGORIES, self::CATEGORIES);
    }

    public static function getIndustryOptions()
    {
        return array_combine(self::INDUSTRIES, self::INDUSTRIES);
    }

    public static function getSizeOptions()
    {
        return array_combine(self::SIZES, self::SIZES);
    }


}
