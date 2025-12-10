<?php

namespace app\modules\crm\widgets;

use humhub\components\Widget;
use app\modules\crm\models\Organization;
use Yii;

class MyOrganizations extends Widget
{
    public $contentContainer;
    public $limit = 5;

    public function run()
    {
        $user = Yii::$app->user->getIdentity();

        // $query: get all organizations the logged in user is responsible for
        $query = Organization::find()
            ->contentContainer($this->contentContainer)
            ->joinWith('responsibleUsers')
            ->where(['user.id' => $user->id])
            ->orderBy(['crm_organization.name' => SORT_ASC]);

        return $this->render('myOrganizations', [
            'organizations' => $query->limit($this->limit)->all()
        ]);
    }
}
