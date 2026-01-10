<?php

namespace humhub\modules\crm\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\crm\controllers\actions\CrmActivityStreamAction;
use Yii;

class StreamController extends ContentContainerController
{
    public function init()
    {
        parent::init();

        if (Yii::$app->user->isGuest) {
            throw new \yii\web\HttpException(403, 'Sie müssen sich einloggen, um die internen CRM-Informationen einzusehen.');
        }
    }
    public function actions()
    {
        return [
            'stream' => [
                'class' => CrmActivityStreamAction::class, // use custom filter
                'contentContainer' => $this->contentContainer,
                'limit' => 10,
            ],
        ];
    }
}
