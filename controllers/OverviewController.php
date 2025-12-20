<?php

namespace humhub\modules\crm\controllers;

use app\modules\crm\models\Interaction;
use humhub\modules\content\components\ContentContainerController;
use Yii;

class OverviewController extends ContentContainerController
{
    public function actionIndex()
    {
        // get all interactions from current space
        $query = Interaction::find()
            ->contentContainer($this->contentContainer);

        // join: get responsibleUsers
        // 'innerJoinWith' get Interactions with responsibleUsers
        $query->innerJoinWith([
            'responsibleUsers' => function ($q) {
                $q->from(['relUser' => 'user']); // Alias for User-Table
            }
        ]);

        // filter: where 'relUser.id' == currentUser's ID
        $query->andWhere(['relUser.id' => Yii::$app->user->id]);

        // sort: show oldest first => to not miss any pending interactions
        $query->orderBy(['date' => SORT_ASC]);

        // filter: Hide done and cancelled interactions
        $query->andWhere(['!=', 'crm_interaction.status', Interaction::STATUS_DONE]);
        $query->andWhere(['!=', 'crm_interaction.status', Interaction::STATUS_CANCELLED]);

        $interactions = $query->all();

        // give interactions to overview/index.php
        return $this->render('index', [
            'space' => $this->contentContainer,
            'interactions' => $interactions
        ]);
    }
}
