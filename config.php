<?php

use humhub\modules\space\widgets\Menu;
use humhub\modules\crm\Module;

return [
    'id' => 'crm',
    'class' => Module::class,
    'namespace' => 'humhub\modules\crm',
    'events' => [
        // When Space menu is initialized (EVENT_INIT),
        // call 'onSpaceMenuInit' in Module.php
        [
            'class' => Menu::class,
            'event' => Menu::EVENT_INIT,
            'callback' => [Module::class, 'onSpaceMenuInit'],
        ],
    ],
];
