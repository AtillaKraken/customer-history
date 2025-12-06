<?php

use humhub\modules\content\widgets\WallCreateContentMenu; // NEUES ZIEL
use humhub\modules\space\widgets\Menu; // FÜR SPACE SIDEBAR
use humhub\modules\crm\Events;

return [
    'id' => 'crm',
    'class' => 'humhub\modules\crm\Module',
    'namespace' => 'humhub\modules\crm',
    'events' => [
        // 1. Button im Stream (Jetzt korrekt am MENU, nicht am Formular)
        [
            'class' => WallCreateContentMenu::class,
            'event' => WallCreateContentMenu::EVENT_INIT,
            'callback' => [Events::class, 'onWallCreateContentMenuInit'],
        ],

        // 2. Link in der Space-Navigation (Damit das Modul links wieder auftaucht)
        [
            'class' => Menu::class,
            'event' => Menu::EVENT_INIT,
            'callback' => [Events::class, 'onSpaceMenuInit'],
        ],
    ],
    'urlManagerRules' => [
        ['pattern' => 'contentcontainer/<containerId:\d+>/crm/<controller:\w+>/<action:\w*>', 'route' => 'crm/<controller>/<action>'],
    ]
];
