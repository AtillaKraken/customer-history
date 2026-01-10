<?php

namespace humhub\modules\crm;

use humhub\modules\crm\models\Interaction;
use humhub\modules\content\widgets\stream\WallStreamEntryWidget;
use humhub\modules\content\widgets\WallCreateContentMenu;
use humhub\modules\crm\notifications\LowQualityInteractionNotification;
use humhub\modules\crm\permissions\CreateCrmEntry;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Menu;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\user\components\ActiveQueryUser;
use humhub\modules\user\models\User;
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

        // permissionCheck
        if (!$menu->contentContainer->permissionManager->can(new CreateCrmEntry())) {
            return;
        }

        $crmUrl = $menu->contentContainer->createUrl('/crm/create/index');

        $menu->addEntry(new MenuLink([
            'label' => 'CRM Eintrag',
            'url' => $crmUrl,
            'icon' => Icon::get('address-card-o'),
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
            'icon' => Icon::get('address-card-o'),
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
    /**
     * Callback for daily cron job event.
     * 1. Updates overdue statuses (Daily)
     * 2. Sends LowQuality notifications (Monthly - last day of month)
     */
    public static function onDailyCron($event)
    {
        // daily jobs:
        if (Yii::$app->request->isConsoleRequest) {
            echo "\n--- CRM Daily Cron Start ---\n";
        }

        // check interacitons-statusses daily
        $updatedCount = Interaction::updateOverdueStatuses();

        if (Yii::$app->request->isConsoleRequest) {
            echo "Status-Update: $updatedCount Interaktionen auf 'Überfällig' gesetzt.\n";
        }

        // monthly jobs

        // check: "is it the last of the month?"
        // date('j') = day (1-31)
        // date('t') = month's day-count (28-31)
        if (date('j') != date('t')) {
            if (Yii::$app->request->isConsoleRequest) {
                echo "--- End CRM Daily Cron Job ---\n";
            }
            return;
        }

        if (Yii::$app->request->isConsoleRequest) {
            echo "End of Month -> Iteraction-quality Check\n";
        }

        // check all spaces
        $spaces = Space::find()->all();

        foreach ($spaces as $space) {
            if (!$space->isModuleEnabled('crm')) {
                continue;
            }

            if (Yii::$app->request->isConsoleRequest) {
                echo "Checking space: " . $space->name . "\n";
            }

            $interactions = Interaction::find()->contentContainer($space)->all();
            $userCounts = [];

            foreach ($interactions as $interaction) {
                if ($interaction->getQualityScore() >= 40) {
                    continue;
                }

                // get all respUsers of the interaction
                foreach ($interaction->responsibleUsers as $user) {
                    if (!isset($userCounts[$user->id])) {
                        $userCounts[$user->id] = 0;
                    }
                    $userCounts[$user->id]++;
                }
            }

            // send notifications
            foreach ($userCounts as $userId => $count) {
                if ($count > 0) {
                    $recipient = User::findOne($userId);
                    if ($recipient) {
                        $notification = new LowQualityInteractionNotification();
                        $notification->source = $space;

                        // set Space Owner as sender, to display an avatar correctly
                        $owner = $space->getOwnerUser()->one();
                        if ($owner) {
                            $notification->from($owner);
                        }

                        $notification->send($recipient);

                        if (Yii::$app->request->isConsoleRequest) {
                            echo "   -> Notifications sent to: " . $recipient->username . " (Anzahl: $count)\n";
                        }
                    }
                }
            }
        }

        if (Yii::$app->request->isConsoleRequest) {
            echo "--- End CRM Daily Cron Job  ---\n";
        }
    }
}
