<?php

namespace humhub\modules\crm\notifications;

use humhub\modules\notification\components\NotificationCategory;
use Yii;

class CrmNotificationCategory extends NotificationCategory
{
    /**
     * @var string the category id
     */
    public $id = 'crm';

    /**
     * Returns a human readable title of this category
     */
    public function getTitle()
    {
        return 'CRM';
    }

    /**
     * Returns a group description
     */
    public function getDescription()
    {
        return 'Benachrichtigungen über CRM Interaktionen und Events.';
    }
}
