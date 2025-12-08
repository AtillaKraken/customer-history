<?php

namespace humhub\modules\crm\controllers;

use app\modules\crm\models\Interaction;
use humhub\modules\content\components\ContentContainerController;
use Yii;

class InteractionController extends ContentContainerController
{
    // URL: /index.php?r=crm/interaction/index&cguid=<space-guid>

    public function actionIndex()
    {
        $interactions = Interaction::find()
            ->contentContainer($this->contentContainer)
            ->all();

        // render the view
        return $this->render('index', [
            'interactions' => $interactions,
            'space' => $this->contentContainer
        ]);
    }

    public function actionCreate()
    {
        $model = new Interaction();
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
