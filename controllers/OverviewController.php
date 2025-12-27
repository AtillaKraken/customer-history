<?php

namespace humhub\modules\crm\controllers;

use humhub\modules\content\components\ContentContainerController;
use app\modules\crm\models\Interaction;
use Yii;
use yii\data\Pagination;

class OverviewController extends ContentContainerController
{
    /**
     * Renders the CRM Dashboard
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->getIdentity();

        // get my (regarding responsibility) & pending (!DONE && !CANCELLED) interactiosn
        $query = Interaction::find()
            ->contentContainer($this->contentContainer)
            ->joinWith('responsibleUsers')
            ->where(['user.id' => $user->id])
            ->andWhere(['NOT IN', 'crm_interaction.status', [Interaction::STATUS_DONE, Interaction::STATUS_CANCELLED]])
            ->orderBy(['date' => SORT_ASC]);

        // prep pagination
        $countQuery = clone $query;
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 5,
            'route' => '/crm/overview/index'
        ]);

        // get data (with limit & offset for pagination)
        $interactions = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('index', [
            'space' => $this->contentContainer,
            'interactions' => $interactions,
            'pagination' => $pages
        ]);
    }}
