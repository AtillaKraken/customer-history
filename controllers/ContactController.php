<?php

namespace humhub\modules\crm\controllers;

use app\modules\crm\models\Contact;
use app\modules\crm\models\Organization;
use humhub\modules\content\components\ContentContainerController;
use Yii;
use yii\helpers\ArrayHelper;

class ContactController extends ContentContainerController
{
    /**
     * Show List of all Contacts
     */
    public function actionIndex()
    {
        $contacts = Contact::find()
            ->contentContainer($this->contentContainer)
            ->all();

        return $this->render('index', [
            'contacts' => $contacts,
            'space' => $this->contentContainer
        ]);
    }

    public function actionCreate()
    {
        $model = new Contact();
        $model->content->container = $this->contentContainer;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->asJson(['success' => true]);
        }

        return $this->renderAjax('edit', [
            'model' => $model,
            'organizations' => $this->getOrganizationList()
        ]);
    }

    public function actionEdit($id)
    {
        $model = Contact::findOne(['id' => $id]);

        if (!$model || $model->content->contentcontainer_id !== $this->contentContainer->id) {
            throw new \yii\web\HttpException(404);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->asJson(['success' => true]);
        }

        return $this->renderAjax('edit', [
            'model' => $model,
            'organizations' => $this->getOrganizationList()
        ]);
    }

    /**
     * Helper to get list for Dropdown [id => name]
     */
    private function getOrganizationList()
    {
        $orgs = Organization::find()
            ->contentContainer($this->contentContainer)
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return ArrayHelper::map($orgs, 'id', 'name');
    }
}
