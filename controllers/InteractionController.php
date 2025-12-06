<?php

namespace humhub\modules\crm\controllers;

use app\modules\crm\models\Interaction;
use humhub\modules\content\components\ContentContainerController;

class InteractionController extends ContentContainerController
{
    // URL: /index.php?r=crm/interaction/index&cguid=<space-guid>

    public function actionIndex()
    {
        $space = $this->contentContainer;

        $interactions = Interaction::find()->contentContainer($space)->all();

        return $this->render('index', ['interactions' => $interactions]);
    }
}
