<?php

namespace humhub\modules\crm\controllers;

use app\modules\crm\models\Event;
use humhub\modules\content\components\ContentContainerController;
use Yii;

class EventController extends ContentContainerController
{
    /**
     * Show List of all Events
     */
    public function actionIndex()
    {
        // Build Query: find all event models which belong to this space
        $events = Event::find()
            ->contentContainer($this->contentContainer)
            ->all();

        // render the view
        return $this->render('index', [
            'events' => $events,
            'space' => $this->contentContainer
        ]);
    }

    public function actionCreate()
    {
        $model = new Event();
        $model->content->setContainer($this->contentContainer);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // close Modal & reload stream
            return $this->renderAjaxContent('
                <script>
                    humhub.modules.client.reload();
                    humhub.modules.ui.modal.global.close();
                </script>
            ');
        }

        return $this->renderAjax('edit', [
            'model' => $model
        ]);
    }
}
