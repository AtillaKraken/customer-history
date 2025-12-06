<?php

namespace humhub\modules\crm\controllers;

use humhub\modules\content\components\ContentContainerController;

class CreateController extends ContentContainerController
{
    /**
     * Show quick capture Modal
     */
    public function actionIndex()
    {
        return $this->renderAjax('index', [
            'contentContainer' => $this->contentContainer
        ]);
    }
}
