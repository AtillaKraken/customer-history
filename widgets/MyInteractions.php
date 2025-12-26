<?php

namespace app\modules\crm\widgets;

use humhub\components\Widget;
use app\modules\crm\models\Interaction;
use Yii;

class MyInteractions extends Widget
{
    public $contentContainer;
    public $limit = 5;

    public function run()
    {
        $user = Yii::$app->user->getIdentity();

        // $query: get all interactions which the logged in user is responsible for
        $query = Interaction::find()
            ->contentContainer($this->contentContainer)
            ->joinWith('responsibleUsers')
            ->where(['user.id' => $user->id])
            ->andWhere(['NOT IN', 'crm_interaction.status', ['DONE', 'CANCELLED']]) // hide where no action's needed to be taken
            ->orderBy(['date' => SORT_ASC]); // => show overdue/pending ones first
        $totalCount = $query->count();

        return $this->render('myInteractions', [
            'interactions' => $query->limit($this->limit)->all(),
            'totalCount' => $totalCount,
            'limit' => $this->limit,
            'contentContainer' => $this->contentContainer
        ]);
    }
}
