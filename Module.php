<?php

namespace humhub\modules\crm;

use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\space\models\Space;
use Yii;

class Module extends ContentContainerModule
{
    public $controllerNamespace = 'humhub\modules\crm\controllers';

    public function getContentContainerTypes()
    {
        return [Space::class];
    }

    public function getPermissions($contentContainer = null)
    {
        return [];
    }

    /**
     * HIER IST DER NEUE CODE:
     * Fügt den Link ins Space-Menü ein.
     */
    public static function onSpaceMenuInit($event)
    {
        // Which space are we in?
        $space = $event->sender->space;

        // Is the Module enabled in said space?
        // this enables the Module-URLs only in activated spaces
        if ($space->isModuleEnabled('crm')) {

            $event->sender->addItem([
                'label' => 'Kundenhistorie',
                'url' => $space->createUrl('/crm/overview/index'), // URL to the crm space-overview
                'icon' => '<i class="fa fa-building"></i>', // TODO: suchen nach dem UI Kit wie in Figma
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'crm'),
                'sortOrder' => 400,
            ]);
        }
    }
}
