<?php

namespace humhub\modules\crm\controllers;

use app\modules\crm\models\forms\CrmFilter;
use app\modules\crm\models\Organization;
use HttpException;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\content\permissions\CreatePrivateContent;
use humhub\widgets\ModalClose;
use Yii;
use yii\db\Exception;

class OrganizationController extends ContentContainerController
{
    /**
     * Show List of all Organizations
     */
    public function actionIndex()
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

        $organizations = $query->all();

        // use ajax to update list view
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            return $this->renderAjax('_list', ['organizations' => $organizations]);
        }

        // usual/normal index-call / "show all"
        return $this->render('index', [
            'organizations' => $organizations,
            'space' => $this->contentContainer,
            'filter' => $filter
        ]);
    }

    public function actionCreate()
    {
        if (!$this->contentContainer->permissionManager->can(new CreatePrivateContent())) {
            throw new HttpException(401, 'Zugriff verweigert.');
        }

        $model = new Organization();
        $model->content->container = $this->contentContainer;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return ModalClose::widget([
                'saved' => true,
                'script' => 'humhub.modules.client.reload();'
            ]);
        }

        return $this->renderAjax('edit', [
            'model' => $model,
            'contentContainer' => $this->contentContainer
        ]);
    }

    /**
     * @throws HttpException
     * @throws Exception
     * @throws \yii\base\Exception
     */
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
                'script' => 'humhub.modules.client.reload();'
            ]);
        }

        // render view
        return $this->renderAjax('edit', [
            'model' => $model,
            'contentContainer' => $this->contentContainer
        ]);
    }

}
