<?php

namespace humhub\modules\crm\controllers;

use humhub\modules\crm\models\Event;
use humhub\modules\crm\models\forms\CrmFilter;
use HttpException;
use humhub\modules\crm\permissions\CreateCrmEntry;
use humhub\widgets\modal\ModalClose;
use humhub\modules\content\components\ContentContainerController;
use Yii;
use yii\data\Pagination;

class EventController extends ContentContainerController
{

    public function init()
    {
        parent::init();

        if (Yii::$app->user->isGuest) {
            throw new \yii\web\HttpException(403, 'Sie müssen sich einloggen, um die internen CRM-Informationen einzusehen.');
        }
    }
    /**
     * Show List of all Events
     */
    public function actionIndex($view = 'list')
    {
        $filter = new CrmFilter();
        $filter->load(Yii::$app->request->get());

        $query = Event::find()
            ->contentContainer($this->contentContainer);

        $filter->apply($query, 'event');

        // pagination
        $countQuery = clone $query;
        $pageSize = ($view === 'cards') ? 8 : 10;

        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => $pageSize,
            'params' => array_merge(Yii::$app->request->get(), ['view' => $view]),
        ]);

        $events = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        // AJAX vs PJAX
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            $viewFile = ($view === 'cards') ? '_accordionList' : '_list';
            return $this->renderAjax($viewFile, [
                'events' => $events,
                'pagination' => $pages,
            ]);
        }

        return $this->render('index', [
            'events' => $events,
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

        $model = new Event();
        $model->content->setContainer($this->contentContainer);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return ModalClose::widget([
                'saved' => true,
                'reload' => true,
            ]);
        }

        return $this->renderAjax('edit', [
            'model' => $model,
        ]);
    }
    public function actionEdit($id)
    {
        $model = Event::find()
            ->contentContainer($this->contentContainer)
            ->where(['crm_event.id' => $id])
            ->one();

        // check existence
        if (!$model) {
            throw new HttpException(404, 'Veranstaltung nicht gefunden.');
        }

        // check custom permission embedded in Modle
        if (!$model->canEdit()) {
            throw new HttpException(401, 'Zugriff verweigert.');
        }

        // save
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return ModalClose::widget([
                'saved' => true,
                'reload' => true,
            ]);
        }

        // render view
        return $this->renderAjax('edit', [
            'model' => $model,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionView($id)
    {
        $model = Event::find()
            ->contentContainer($this->contentContainer)
            ->where(['crm_event.id' => $id])
            ->one();

        if (!$model) {
            throw new HttpException(404, 'Veranstaltung nicht gefunden.');
        }
        if (!$model->content->canView()) {
            throw new HttpException(403, 'Zugriff verweigert.');
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', ['model' => $model]);
        }

        return $this->render('view', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = Event::find()
            ->contentContainer($this->contentContainer)
            ->where(['crm_event.id' => $id])
            ->one();

        if (!$model) {
            throw new HttpException(404, 'Veranstaltung nicht gefunden.');
        }

        if (!$model->canDelete()) {
            throw new HttpException(401, 'Zugriff verweigert.');
        }

        $model->delete();

        return $this->redirect($this->contentContainer->createUrl('/crm/event/index'));
    }

    public function actionLoadUpcoming()
    {
        $query = Event::find()
            ->contentContainer($this->contentContainer)
            ->where(['>=', 'date', new \yii\db\Expression('CURDATE()')])
            ->orderBy(['date' => SORT_ASC]);

        $events = $query->all();

        return $this->renderAjax('_modal_list', [
            'events' => $events,
            'title' => 'Anstehende Veranstaltungen',
        ]);
    }
}
