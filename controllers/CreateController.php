<?php

namespace humhub\modules\crm\controllers;

use humhub\modules\content\components\ContentContainerController;
use Yii;

class CreateController extends ContentContainerController
{

    public function init()
    {
        parent::init();

        if (Yii::$app->user->isGuest) {
            throw new \yii\web\HttpException(403, 'Sie müssen sich einloggen, um die internen CRM-Informationen einzusehen.');
        }
    }
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
