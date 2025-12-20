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
                    $query->andWhere(['like', 'name', $this->term]);
                    $query->orWhere(['like', 'city', $this->term]);
                    break;
                case 'contact':
                    $query->andWhere(['like', 'name', $this->term])
                        ->orWhere(['like', 'email', $this->term]);
                    break;
                case 'interaction':
                    $query->andWhere(['like', 'crm_interaction.title', $this->term]);
                    break;
                case 'event':
                    $query->andWhere(['like', 'crm_event.title', $this->term]);
                    break;
            }
        }

        //  "Mine" Filter
        if (in_array(self::FILTER_MINE, $this->filters)) {
            $query->andWhere(['user.id' => Yii::$app->user->id]);
        }

        // Interactions
        if ($entityType === 'interaction') {
            if (in_array(self::FILTER_OVERDUE , $this->filters)) {
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
