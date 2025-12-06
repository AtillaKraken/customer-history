<?php

namespace humhub\modules\crm\controllers;

use app\modules\crm\models\Organization;
use humhub\modules\content\components\ContentContainerController;
use Yii;

class OrganizationController extends ContentContainerController
{
    /**
     * Show List of all Organizations
     */
    public function actionIndex()
    {
        // Build Query: find all organization models which belong to this space
        $organizations = Organization::find()
            ->contentContainer($this->contentContainer)
            ->all();

        // render the view
        return $this->render('index', [
            'organizations' => $organizations,
            'space' => $this->contentContainer
        ]);
    }

    public function actionCreate()
    {
        $model = new Organization();
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

        return $this->renderAjax('create', [
            'model' => $model
        ]);
    }
}
