<?php

namespace humhub\modules\crm\controllers;

use app\modules\crm\models\Contact;
use app\modules\crm\models\Organization;
use app\modules\crm\models\forms\CrmFilter;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\content\permissions\CreatePrivateContent;
use humhub\widgets\ModalClose;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

class ContactController extends ContentContainerController
{
    /**
     * Show List of all Contacts
     */
    public function actionIndex()
    {
        $filter = new CrmFilter();
        $filter->load(Yii::$app->request->get());

        $query = Contact::find()
            ->contentContainer($this->contentContainer);

        $filter->apply($query, 'contact');

        $contacts = $query->all();

        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            return $this->renderAjax('_list', ['contacts' => $contacts]);
        }

        return $this->render('index', [
            'contacts' => $contacts,
            'space' => $this->contentContainer,
            'filter' => $filter
        ]);
    }

    public function actionCreate()
    {
        // CreatePrivateContent to only allow Space-Members contentCreation (=> space-membership is required)
        if (!$this->contentContainer->permissionManager->can(new CreatePrivateContent())) {
            throw new HttpException(401, 'Sie haben keine Berechtigung, Kontakte zu erstellen.');
        }

        $model = new Contact();
        $model->content->container = $this->contentContainer;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return ModalClose::widget([
                'saved' => true,
                'script' => 'humhub.modules.client.reload();'
            ]);
        }

        return $this->renderAjax('edit', [
            'model' => $model,
            'organizations' => $this->getOrganizationList()
        ]);
    }

    public function actionEdit($id)
    {
        $model = Contact::find()
            ->contentContainer($this->contentContainer)
            ->where(['crm_contact.id' => $id])
            ->one();

        if (!$model) {
            throw new HttpException(404, 'Kontakt nicht gefunden.');
        }

        // native editable-check of Content-Object
        if (!$model->content->canEdit()) {
            throw new HttpException(401, 'Zugriff verweigert.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return ModalClose::widget([
                'saved' => true,
                'script' => 'humhub.modules.client.reload();'
            ]);
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
