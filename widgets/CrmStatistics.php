<?php

namespace humhub\modules\crm\widgets;

use humhub\components\Widget;
use humhub\modules\crm\models\Organization;
use humhub\modules\crm\models\Contact;
use humhub\modules\crm\models\Event;
use humhub\modules\crm\models\Interaction;
use yii\db\Expression;

class CrmStatistics extends Widget
{
    public $contentContainer;

    public function run()
    {
        // get recent interactions (of the last month)
        $interactionsLast30Days = Interaction::find()
            ->contentContainer($this->contentContainer)
            ->where(['>=', 'content.created_at', new Expression('DATE_SUB(NOW(), INTERVAL 30 DAY)')])
            ->count();


        // get the recent interactions + total amount of the remaining 3 objects
        return $this->render('crmStatistics', [
            'countOrgs' => Organization::find()->contentContainer($this->contentContainer)->count(),
            'countContacts' => Contact::find()->contentContainer($this->contentContainer)->count(),
            'countEvents' => Event::find()->contentContainer($this->contentContainer)->count(),
            'countInteractions30d' => $interactionsLast30Days
        ]);
    }
}
