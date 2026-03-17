<?php

namespace humhub\modules\crm\models\forms;

use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;
use humhub\modules\crm\models\Organization;

class CrmFilter extends Model
{
    public $term = '';
    public $filters = []; // checkboxes (z.B. "von mir betreute Orgas")

    // Organization filters
    public $orgCategories = [];
    public $orgIndustries = [];
    public $orgSize = null;
    public $orgRespUsers = [];

    // Contact filters
    public $contactGender = [];
    public $contactOrg = [];
    public $contactRoles = [];
    public $contactOwnOrg = false; // bool due to Checkbox: "von mir betreute orgas"

    // Interaction filters
    public $interactionDate = null;
    public $interactionStatus = [];
    public $interactionChannel = [];
    public $interactionRespUsers = [];

    // Event filters
    public $eventDate = null;
    public $eventType = [];
    public $eventOrg = [];
    public $eventOwnOrg = false; // bool due to Checkbox: "von mir betreute orgas"

    public function rules()
    {
        return [
            [['term', 'orgSize', 'interactionDate', 'eventDate'], 'string'],
            [['filters', 'orgCategories', 'orgIndustries', 'orgRespUsers'], 'safe'],
            [['contactGender', 'contactOrg', 'contactRoles'], 'safe'],
            [['interactionStatus', 'interactionChannel', 'interactionRespUsers'], 'safe'],
            [['eventType', 'eventOrg'], 'safe'],
            [['contactOwnOrg', 'eventOwnOrg'], 'boolean'],
        ];
    }

    /**
     * Applies Filter on ActiveQuery
     */
    public function apply(ActiveQuery $query, $entityType = 'global')
    {
        // Text-Search (adjusted to each Entity Type)
        if (!empty($this->term)) {
            switch ($entityType) {
                case 'organization':
                    $query->andWhere(['or', ['like', 'crm_organization.name', $this->term], ['like', 'crm_organization.city', $this->term]]);
                    break;
                case 'contact':
                    $query->andWhere(['or', ['like', 'crm_contact.name', $this->term], ['like', 'crm_contact.email', $this->term]]);
                    break;
                case 'interaction':
                    $query->andWhere(['like', 'crm_interaction.title', $this->term]);
                    break;
                case 'event':
                    $query->andWhere(['like', 'crm_event.title', $this->term]);
                    break;
            }
        }


        // ORGANIZATION
        if ($entityType === 'organization') {
            if (!empty($this->orgCategories)) $query->andWhere(['in', 'crm_organization.category', $this->orgCategories]);
            if (!empty($this->orgIndustries)) $query->andWhere(['in', 'crm_organization.industry', $this->orgIndustries]);
            if (!empty($this->orgSize)) $query->andWhere(['crm_organization.size' => $this->orgSize]);
            if (!empty($this->orgRespUsers)) {
                $query->innerJoin('crm_organization_user org_ru', 'crm_organization.id = org_ru.organization_id')
                    ->andWhere(['in', 'org_ru.user_id', $this->orgRespUsers]);
            }
        }

        // CONTACT
        if ($entityType === 'contact') {
            if (!empty($this->contactGender)) $query->andWhere(['in', 'crm_contact.gender', $this->contactGender]);
            if (!empty($this->contactRoles)) $query->andWhere(['in', 'crm_contact.roles', $this->contactRoles]);
            if (!empty($this->contactOrg)) $query->andWhere(['in', 'crm_contact.organization_id', $this->contactOrg]);

            // => "Von mir betreute Orgas"
            if ($this->contactOwnOrg) {
                $myOrgIds = Organization::find()->select('crm_organization.id')
                    ->joinWith('responsibleUsers')->where(['user.id' => Yii::$app->user->id]);
                $query->andWhere(['in', 'crm_contact.organization_id', $myOrgIds]);
            }
        }

        // INTERACTION
        if ($entityType === 'interaction') {
            if (!empty($this->interactionDate)) $query->andWhere(['crm_interaction.date' => $this->interactionDate]);
            if (!empty($this->interactionStatus)) $query->andWhere(['in', 'crm_interaction.status', $this->interactionStatus]);
            if (!empty($this->interactionChannel)) $query->andWhere(['in', 'crm_interaction.channel', $this->interactionChannel]);
            if (!empty($this->interactionRespUsers)) {
                $query->innerJoin('crm_interaction_responsible_user int_ru', 'crm_interaction.id = int_ru.interaction_id')
                    ->andWhere(['in', 'int_ru.user_id', $this->interactionRespUsers]);
            }
        }

        // EVENT
        if ($entityType === 'event') {
            if (!empty($this->eventDate)) $query->andWhere(['crm_event.date' => $this->eventDate]);
            if (!empty($this->eventType)) $query->andWhere(['in', 'crm_event.type', $this->eventType]);
            if (!empty($this->eventOrg)) {
                $query->innerJoin('crm_event_organization evt_org', 'crm_event.id = evt_org.event_id')
                    ->andWhere(['in', 'evt_org.organization_id', $this->eventOrg]);
            }
            // => "Von mir betreute Orgas"
            if ($this->eventOwnOrg) {
                $myOrgIds = Organization::find()->select('crm_organization.id')
                    ->joinWith('responsibleUsers')->where(['user.id' => Yii::$app->user->id]);
                $query->innerJoin('crm_event_organization evt_org_own', 'crm_event.id = evt_org_own.event_id')
                    ->andWhere(['in', 'evt_org_own.organization_id', $myOrgIds]);
            }
        }
    }
}
