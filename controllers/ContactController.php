<?php

namespace humhub\modules\crm\controllers;

use app\modules\crm\models\Contact;
use app\modules\crm\models\Organization;
use app\modules\crm\models\forms\CrmFilter;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\crm\permissions\CreateCrmEntry;
use humhub\widgets\ModalClose;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

/**
 *
 * @property-read mixed $organizationList
 */
class ContactController extends ContentContainerController
{
    /**
     * Show List of all Contacts
     */
    public function actionIndex($view = 'list')
    {
        $filter = new CrmFilter();
        $filter->load(Yii::$app->request->get());
        $query = Contact::find()->contentContainer($this->contentContainer);
        $filter->apply($query, 'contact');

        $countQuery = clone $query;
        $pageSize = ($view === 'cards') ? 8 : 10;
        $pages = new \yii\data\Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize, 'params' => array_merge(Yii::$app->request->get(), ['view' => $view])]);

        $contacts = $query->offset($pages->offset)->limit($pages->limit)->all();

        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            $viewFile = ($view === 'cards') ? '_accordionList' : '_list';
            return $this->renderAjax($viewFile, ['contacts' => $contacts, 'pagination' => $pages]);
        }

        return $this->render('index', ['contacts' => $contacts, 'space' => $this->contentContainer, 'filter' => $filter, 'viewMode' => $view, 'pagination' => $pages]);
    }

    public function actionView($id)
    {
        $model = Contact::find()
            ->contentContainer($this->contentContainer)
            ->where(['crm_contact.id' => $id])
            ->one();

        if (!$model) {
            throw new HttpException(404, 'Kontaktperson nicht gefunden.');
        }
        if (!$model->content->canView()) {
            throw new HttpException(403, 'Zugriff verweigert.');
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', ['model' => $model]);
        }
        return $this->render('view', ['model' => $model]);
    }

    public function actionCreate()
    {
        if (!$this->contentContainer->permissionManager->can(new CreateCrmEntry())) {
            throw new HttpException(401, 'Zugriff verweigert.');
        }

        $model = new Contact();
        $model->content->container = $this->contentContainer;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return ModalClose::widget([
                'saved' => true,
                'script' => 'humhub.modules.client.reload();',
            ]);
        }

        return $this->renderAjax('edit', [
            'model' => $model,
            'organizations' => $this->getOrganizationList(),
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
                'script' => 'humhub.modules.client.reload();',
            ]);
        }

        return $this->renderAjax('edit', [
            'model' => $model,
            'organizations' => $this->getOrganizationList(),
        ]);
    }

    public function actionDelete($id)
    {
        $model = Contact::find()
            ->contentContainer($this->contentContainer)
            ->where(['crm_contact.id' => $id])
            ->one();

        if (!$model) {
            throw new HttpException(404, 'Kontaktperson nicht gefunden.');
        }

        if (!$model->canDelete()) {
            throw new HttpException(401, 'Zugriff verweigert.');
        }

        $model->delete();

        return $this->redirect($this->contentContainer->createUrl('/crm/contact/index'));
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
