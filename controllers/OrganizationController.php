<?php

namespace humhub\modules\crm\controllers;

use humhub\modules\crm\models\Organization;
use humhub\modules\crm\models\forms\CrmFilter;
use HttpException;
use humhub\modules\content\permissions\CreatePrivateContent;
use humhub\modules\crm\permissions\CreateCrmEntry;
use humhub\widgets\ModalClose;
use humhub\modules\content\components\ContentContainerController;
use Yii;
use yii\data\Pagination;

class OrganizationController extends ContentContainerController
{
    public function init()
    {
        parent::init();

        if (Yii::$app->user->isGuest) {
            throw new \yii\web\HttpException(403, 'Sie müssen sich einloggen, um die internen CRM-Informationen einzusehen.');
        }
    }
    /**
     * Show List of all Organizations
     */
    public function actionIndex($view = 'list')
    {
        // load filter
        $filter = new CrmFilter();
        $filter->load(Yii::$app->request->get());

        // build query
        $query = Organization::find()
            ->contentContainer($this->contentContainer);

        // join for "mine" (regarding responsibility)
        if (in_array('mine', $filter->filters)) {
            $query->joinWith('responsibleUsers');
        }

        // apply filter
        $filter->apply($query, 'organization');

        // Pagination
        $countQuery = clone $query;
        $pageSize = ($view === 'cards') ? 8 : 10;

        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => $pageSize,
            'params' => array_merge(Yii::$app->request->get(), ['view' => $view]),
        ]);

        $organizations = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        // AJAX
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            $viewFile = ($view === 'cards') ? '_accordionList' : '_list';
            return $this->renderAjax($viewFile, [
                'organizations' => $organizations,
                'pagination' => $pages,
            ]);
        }

        // usual/normal index-call / "show all"
        return $this->render('index', [
            'organizations' => $organizations,
            'space' => $this->contentContainer,
            'filter' => $filter,
            'viewMode' => $view,
            'pagination' => $pages,
        ]);
    }

    public function actionCreate()
    {
        if (!$this->contentContainer->permissionManager->can(new CreateCrmEntry())) {
            throw new HttpException(401, 'Zugriff verweigert.');
        }

        $model = new Organization();
        $model->content->setContainer($this->contentContainer);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return ModalClose::widget([
                'saved' => true,
                'script' => 'humhub.modules.client.reload();',
            ]);
        }

        return $this->renderAjax('edit', [
            'model' => $model,
        ]);
    }
    public function actionEdit($id)
    {
        $model = Organization::find()
            ->contentContainer($this->contentContainer)
            ->where(['crm_organization.id' => $id])
            ->one();

        // check existence
        if (!$model) {
            throw new HttpException(404, 'Organisation nicht gefunden.');
        }

        // check permissions
        if (!$model->content->canEdit()) {
            throw new HttpException(401, 'Zugriff verweigert.');
        }

        // save
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return ModalClose::widget([
                'saved' => true,
                'script' => 'humhub.modules.client.reload();',
            ]);
        }

        // render view
        return $this->renderAjax('edit', [
            'model' => $model,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionDelete($id)
    {
        $model = Organization::find()
            ->contentContainer($this->contentContainer)
            ->where(['crm_organization.id' => $id])
            ->one();

        if (!$model) {
            throw new HttpException(404, 'Organisation nicht gefunden.');
        }

        if (!$model->canDelete()) {
            throw new HttpException(401, 'Zugriff verweigert.');
        }

        $model->delete();

        return $this->redirect($this->contentContainer->createUrl('/crm/organization/index'));
    }

    public function actionView($id)
    {
        $model = Organization::find()
            ->contentContainer($this->contentContainer)
            ->where(['crm_organization.id' => $id])
            ->one();

        if (!$model) {
            throw new HttpException(404);
        }
        if (!$model->content->canView()) {
            throw new HttpException(403);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', ['model' => $model]);
        }

        return $this->render('view', ['model' => $model]);
    }

    public function actionLoadMyOrganizations()
    {
        $user = Yii::$app->user->getIdentity();

        $query = \humhub\modules\crm\models\Organization::find()
            ->contentContainer($this->contentContainer)
            ->joinWith('responsibleUsers')
            ->where(['user.id' => $user->id])
            ->orderBy(['crm_organization.name' => SORT_ASC]);

        $organizations = $query->all();

        return $this->renderAjax('_modal_list', [
            'organizations' => $organizations,
            'title' => 'Meine Organisationen',
        ]);
    }

}
