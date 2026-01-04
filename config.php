<?php

use humhub\modules\content\widgets\WallCreateContentMenu;
use humhub\modules\space\widgets\Menu;
use humhub\modules\crm\Events;
use humhub\commands\CronController;

return [
    'id' => 'crm',
    'class' => 'humhub\modules\crm\Module',
    'namespace' => 'humhub\modules\crm',
    'events' => [
        // button in the stream (attached to the menu)
        [
            'class' => WallCreateContentMenu::class,
            'event' => WallCreateContentMenu::EVENT_INIT,
            'callback' => [Events::class, 'onWallCreateContentMenuInit'],
        ],

        // link in the space navigation (makes the module appear on the navigation bar)
        [
            'class' => Menu::class,
            'event' => Menu::EVENT_INIT,
            'callback' => [Events::class, 'onSpaceMenuInit'],
        ],

        // daily Cron Job to automatically update PLANNED, yet past interactions to OVERDUE
        // & send LowQualityInteractionNotifications to respUsers for this month's interactions
        [
            'class' => CronController::class,
            'event' => CronController::EVENT_ON_DAILY_RUN,
            'callback' => [Events::class, 'onDailyCron'],
        ],
    ],
    'urlManagerRules' => [
        ['pattern' => 'contentcontainer/<containerId:\d+>/crm/<controller:\w+>/<action:\w*>', 'route' => 'crm/<controller>/<action>'],
    ]
];
