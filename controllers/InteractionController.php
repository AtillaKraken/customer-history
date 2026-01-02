<?php

namespace humhub\modules\crm\controllers;

use app\modules\crm\models\Interaction;
use app\modules\crm\models\forms\CrmFilter;
use humhub\modules\crm\permissions\CreateCrmEntry;
use humhub\widgets\ModalClose;
use HttpException;
use humhub\modules\content\components\ContentContainerController;
use Yii;
use yii\data\Pagination;

class InteractionController extends ContentContainerController
{
    // URL: /index.php?r=crm/interaction/index&cguid=<space-guid>

    public function actionIndex($view = 'list')
    {
        $filter = new CrmFilter();
        $filter->load(Yii::$app->request->get());

        $query = Interaction::find()
            ->contentContainer($this->contentContainer);

        // join for 'Mine' filter
        if (in_array('mine', $filter->filters)) {
            $query->joinWith('responsibleUsers');
        }

        $filter->apply($query, 'interaction');

        // pagination taken from yii framework
        $countQuery = clone $query;
        // 15 items for _list, 8 for cards (_accordionList)
        $pageSize = ($view === 'cards') ? 12 : 15;

        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => $pageSize,
            // save 'view' parameter in pagination links
            'params' => array_merge(Yii::$app->request->get(), ['view' => $view])
        ]);

        $interactions = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        // AJAX vs PJAX
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            // render view based on current mode
            $viewFile = ($view === 'cards') ? '_accordionList' : '_list';
            return $this->renderAjax($viewFile, [
                'interactions' => $interactions,
                'pagination' => $pages
            ]);
        }

        return $this->render('index', [
            'interactions' => $interactions,
            'space' => $this->contentContainer,
            'filter' => $filter,
            'viewMode' => $view,
            'pagination' => $pages
        ]);
    }

    public function actionView($id)
    {
        $model = Interaction::find()
            ->contentContainer($this->contentContainer)
            ->where(['crm_interaction.id' => $id])
            ->one();

        if (!$model) throw new HttpException(404);
        if (!$model->content->canView()) throw new HttpException(403);

        // check if ajax to render plain/modal friendly
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', ['model' => $model]);
        }

        return $this->render('view', ['model' => $model]);
    }

    public function actionCreate()
    {
        // permission check
        if (!$this->contentContainer->permissionManager->can(new CreateCrmEntry())) {
            throw new HttpException(401, 'Zugriff verweigert.');
        }

        $model = new Interaction();
        $model->content->setContainer($this->contentContainer);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // close Modal & reload stream
            return ModalClose::widget([
                'saved' => true,
                'script' => 'humhub.modules.client.reload();'
            ]);
        }

        return $this->renderAjax('edit', [
            'model' => $model
        ]);
    }

    public function actionEdit($id)
    {
        $model = Interaction::find()
            ->contentContainer($this->contentContainer)
            ->where(['crm_interaction.id' => $id])
            ->one();

        // check existance
        if (!$model) {
            throw new HttpException(404, 'Interaktion nicht gefunden.');
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
    public function actionDelete($id)
    {
        $model = Interaction::find()
            ->contentContainer($this->contentContainer)
            ->where(['crm_interaction.id' => $id])
            ->one();

        if (!$model) {
            throw new HttpException(404, 'Interaktion nicht gefunden.');
        }

        if (!$model->canDelete()) {
            throw new HttpException(401, 'Zugriff verweigert.');
        }

        $model->delete();

        return $this->redirect($this->contentContainer->createUrl('/crm/interaction/index'));
    }

    /**
     * get all interactions of the loggedin user for the "show all" modal
     */
    public function actionLoadMyInteractions()
    {
        $user = Yii::$app->user->getIdentity();

        $query = Interaction::find()
            ->contentContainer($this->contentContainer)
            ->joinWith('responsibleUsers')
            ->where(['user.id' => $user->id])
            ->andWhere(['NOT IN', 'crm_interaction.status', [Interaction::STATUS_DONE, Interaction::STATUS_CANCELLED]])
            ->orderBy(['date' => SORT_ASC]);

        $interactions = $query->all();

        return $this->renderAjax('_modal_list', [
            'interactions' => $interactions,
            'title' => 'Meine offenen Interaktionen'
        ]);
    }

}
