<?php

namespace humhub\modules\crm\controllers;

use app\modules\crm\models\Organization;
use humhub\modules\content\components\ContentContainerController;
use Yii;

class OrganizationController extends ContentContainerController
{
    /**
     * Show List of all Organizations
     */
    public function actionIndex()
    {
        // Build Query: find all organization models which belong to this space
        $organizations = Organization::find()
            ->contentContainer($this->contentContainer)
            ->all();

        // render the view
        return $this->render('index', [
            'organizations' => $organizations,
            'space' => $this->contentContainer
        ]);
    }

    public function actionCreate()
    {
        $model = new Organization();
        $model->content->container = $this->contentContainer;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->asJson(['success' => true]); // or close modal
        }


        return $this->renderAjax('edit', [
            'model' => $model,
            'contentContainer' => $this->contentContainer
        ]);
    }

    public function actionEdit($id)
    {
        $model = Organization::findOne(['id' => $id]);

        // Security Check: Darf der User das sehen?
        if (!$model || $model->content->contentcontainer_id !== $this->contentContainer->id) {
            throw new \yii\web\HttpException(404);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->asJson(['success' => true]);
        }

        return $this->renderAjax('edit', [
            'model' => $model,
            'contentContainer' => $this->contentContainer
        ]);
    }

}
