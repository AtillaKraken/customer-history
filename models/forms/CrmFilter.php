<?php

namespace app\modules\crm\models\forms;

use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;

class CrmFilter extends Model
{
    public $term = '';
    public $filters = [];

    // Constants for Checkbox-Filter
    const FILTER_MINE = 'mine';
    const FILTER_OVERDUE = 'overdue'; // only for interactions

    public function rules()
    {
        return [
            [['term'], 'string'],
            [['filters'], 'safe'],
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
                    $query->andWhere(['like', 'crm_organization.name', $this->term])
                        ->orWhere(['like', 'crm_organization.category', $this->term])
                        ->orWhere(['like', 'crm_organization.industry', $this->term])
                        ->orWhere(['like', 'crm_organization.city', $this->term])
                        ->orWhere(['like', 'crm_organization.notes', $this->term]);
                    break;
                case 'contact':
                    $query->andWhere(['like', 'crm_contact.name', $this->term])
                        ->orWhere(['like', 'crm_contact.email', $this->term])
                        ->orWhere(['like', 'crm_contact.phone_number', $this->term])
                        ->orWhere(['like', 'crm_contact.gender', $this->term]);
                    // TODO: Evaluieren: Organisation als Suchbegruff?
                    break;
                case 'interaction':
                    $query->andWhere(['like', 'crm_interaction.title', $this->term])
                        ->orWhere(['like', 'crm_interaction.channel', $this->term])
                        ->orWhere(['like', 'crm_interaction.description', $this->term])
                        ->orWhere(['like', 'crm_interaction.result', $this->term]);
                    // TODO: Evalurieren: Möglichkeit nach Firmen/Kontaktpersonen/Resp Users zu filtern?
                    break;
                case 'event':
                    $query->andWhere(['like', 'crm_event.title', $this->term])
                        ->orWhere(['like', 'crm_event.description', $this->term]);
                    break;
            }
        }

        //  "Mine" Filter
        if (in_array(self::FILTER_MINE, $this->filters)) {
            $query->andWhere(['user.id' => Yii::$app->user->id]);
        }

        // Interactions
        if ($entityType === 'interaction') {
            if (in_array(self::FILTER_OVERDUE, $this->filters)) {
                $query->andWhere(['<', 'date', date('Y-m-d')])
                    ->andWhere(['=', 'crm_interaction.status', 'OVERDUE']);
            }
            if (in_array('open', $this->filters)) {
                $query->andWhere(['!=', 'crm_interaction.status', 'DONE']);
            }
        }

        // Events
        if ($entityType === 'event') {
            if (in_array('upcoming', $this->filters)) {
                $query->andWhere(['>=', 'date', date('Y-m-d')]);
            }
        }

        // Organizations
        if ($entityType === 'organization') {
            if (in_array('category_customer', $this->filters)) {
                $query->andWhere(['category' => 'Kunde']); // Oder wie auch immer der String in der DB heißt
            }
            if (in_array('category_partner', $this->filters)) {
                $query->andWhere(['category' => 'Partner']);
            }
        }
    }
}
