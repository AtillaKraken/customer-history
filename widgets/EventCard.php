<?php

namespace humhub\modules\crm\widgets;

use humhub\modules\crm\models\Event;
use humhub\components\Widget;

/**
 * displays an event entry as a collapsable card
 */
class EventCard extends Widget
{
    public Event $event;
    public $isStream = false;
    public $startCollapsed = true;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('eventCard', [
            'event' => $this->event,
            'isStream' => $this->isStream,
            'startCollapsed' => $this->startCollapsed
        ]);
    }
}
