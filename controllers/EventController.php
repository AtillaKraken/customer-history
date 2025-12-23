<?php

namespace humhub\modules\crm\controllers;

use app\modules\crm\models\Event;
use app\modules\crm\models\forms\CrmFilter;
use HttpException;
use humhub\modules\content\permissions\CreatePrivateContent;
use humhub\widgets\ModalClose;
use humhub\modules\content\components\ContentContainerController;
use Yii;

class EventController extends ContentContainerController
{
    /**
     * Show List of all Events
     */
    public function actionIndex()
    {
        $filter = new CrmFilter();
        $filter->load(Yii::$app->request->get());

        $query = Event::find()
            ->contentContainer($this->contentContainer);

        $filter->apply($query, 'event');

        $events = $query->all();

        // AJAX vs PJAX
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            return $this->renderAjax('_list', ['events' => $events]);
        }

        return $this->render('index', [
            'events' => $events,
            'space' => $this->contentContainer,
            'filter' => $filter
        ]);
    }

    public function actionCreate()
    {
        if (!$this->contentContainer->permissionManager->can(new CreatePrivateContent())) {
            throw new HttpException(401, 'Zugriff verweigert.');
        }

        $model = new Event();
        $model->content->setContainer($this->contentContainer);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
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
        $model = Event::find()
            ->contentContainer($this->contentContainer)
            ->where(['crm_event.id' => $id])
            ->one();

        // check existence
        if (!$model) {
            throw new HttpException(404, 'Veranstlatung nicht gefunden.');
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
