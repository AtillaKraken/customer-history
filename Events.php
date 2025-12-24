<?php

namespace humhub\modules\crm;

use app\modules\crm\models\Interaction;
use humhub\modules\content\widgets\stream\WallStreamEntryWidget;
use humhub\modules\content\widgets\WallCreateContentMenu;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Menu;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\ui\icon\widgets\Icon;
use yii\base\Event;
use Yii;

class Events
{
    /**
     * Adds the "CRM-Eintrag" tab above the stream.
     * Fixes the issue where standard buttons disappear.
     */
    public static function onWallCreateContentMenuInit(Event $event)
    {
        /** @var WallCreateContentMenu $menu */
        $menu = $event->sender;

        // check: only in spaces | enabling each profile to have this would be overkill
        // => emphasis on collaboration therefore space-onky
        if (!($menu->contentContainer instanceof Space)) {
            return;
        }

        // RESTORE STANDARD BUTTONS
        // Since we intervene/change behaviour here, HumHub no longer loads the standard buttons itself
        // therefore we have to do this manually by looping through all modules
        foreach ($menu->contentContainer->moduleManager->getContentClasses() as $content) {
            // Gets the widget for the content type (e.g. Poll, Task)
            $wallEntryWidget = WallStreamEntryWidget::getByContent($content);

            // skip if there is no creator
            if (!$wallEntryWidget || !$wallEntryWidget->createRoute) {
                continue;
            }

            $url = $menu->contentContainer->createUrl($wallEntryWidget->createRoute);
            $label = ucfirst($content->getContentName());

            // build options (logic from HumHubs core)
            $menuOptions = [
                'label' => $label,
                'icon' => $content->getIcon(),
                'url' => '#',
                'sortOrder' => $wallEntryWidget->createFormSortOrder ?? 900,
            ];

            // click behavior depending on mode (Inline Form vs Modal)
            if ($wallEntryWidget->createMode === WallStreamEntryWidget::EDIT_MODE_INLINE) {
                $menuOptions['htmlOptions'] = [
                    'data-action-click' => $wallEntryWidget->createFormMenuAction ?? 'loadForm',
                    'data-action-url' => $url,
                ];
            } elseif ($wallEntryWidget->createMode === WallStreamEntryWidget::EDIT_MODE_MODAL) {
                $menuOptions['htmlOptions'] = [
                    'data-action-click' => $wallEntryWidget->createFormMenuAction ?? 'ui.modal.load',
                    'data-action-url' => $url,
                ];
            } else {
                $menuOptions['url'] = $url;
            }

            // add standard entry
            $menu->addEntry(new MenuLink($menuOptions));
        }

        // add CRM button in space index
        if (!$menu->contentContainer->isModuleEnabled('crm')) {
            return;
        }

        $crmUrl = $menu->contentContainer->createUrl('/crm/create/index');

        $menu->addEntry(new MenuLink([
            'label' => 'CRM Eintrag',
            'url' => $crmUrl,
            'icon' => Icon::get('address-card'),
            'sortOrder' => 350, // positioned between Post and Tasks
            'htmlOptions' => [
                'data-action-click' => 'ui.modal.load', // opens the modal
                'data-action-url' => $crmUrl
            ]
        ]));
    }

    /**
     * Add the module to the space navigation bar
     */
    public static function onSpaceMenuInit(Event $event)
    {
        /** @var Menu $menu */
        $menu = $event->sender;

        if (!($menu->space instanceof Space)) {
            return;
        }

        if (!$menu->space->isModuleEnabled('crm')) {
            return;
        }

        $menu->addEntry(new MenuLink([
            'label' => 'CRM',
            'url' => $menu->space->createUrl('/crm/overview/index'),
            'icon' => Icon::get('users'),
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'crm'),
            'sortOrder' => 400,
        ]));
    }

    /**
     * Callback for daily cron job event.
     * Automatically update overdue interactions.
     *
     * @param Event $event
     */
    public static function onDailyCron($event)
    {
        Interaction::updateOverdueStatuses();
    }
}
