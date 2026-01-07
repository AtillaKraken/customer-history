<?php

namespace humhub\modules\crm\widgets;

use humhub\components\Widget;
use humhub\modules\crm\models\Event;
use yii\db\Expression;

class UpcomingEvents extends Widget
{
    public $contentContainer;
    public $limit = 5;

    public function run()
    {
        // query: get all upcoming/pending events - by examining the date
        // Note: not considering the Event's time because its meant to be nullable
        $query = Event::find()
            ->contentContainer($this->contentContainer)
            ->where(['>=', 'date', new Expression('CURDATE()')])
            ->orderBy(['date' => SORT_ASC]);

        $totalCount = $query->count();

        return $this->render('upcomingEvents', [
            'events' => $query->limit($this->limit)->all(),
            'totalCount' => $totalCount,
            'limit' => $this->limit,
            'contentContainer' => $this->contentContainer
        ]);
    }
}
