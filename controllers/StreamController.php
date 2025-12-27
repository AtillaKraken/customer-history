<?php

namespace humhub\modules\crm\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\crm\controllers\actions\CrmActivityStreamAction;

class StreamController extends ContentContainerController
{
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
