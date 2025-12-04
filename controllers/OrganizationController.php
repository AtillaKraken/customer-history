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
}
