<?php

namespace humhub\modules\crm;

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
     * Fügt den "CRM Eintrag" Tab über dem Stream hinzu.
     * Behebt das Problem, dass Standard-Buttons verschwinden.
     */
    public static function onWallCreateContentMenuInit(Event $event)
    {
        /** @var WallCreateContentMenu $menu */
        $menu = $event->sender;

        // Sicherheitscheck: Nur in Spaces
        if (!($menu->contentContainer instanceof Space)) {
            return;
        }

        // 1. STANDARD-BUTTONS WIEDERHERSTELLEN
        // Da wir hier eingreifen, lädt HumHub die Standard-Buttons nicht mehr selbst.
        // Wir müssen das manuell tun, indem wir durch alle Module loopen.
        foreach ($menu->contentContainer->moduleManager->getContentClasses() as $content) {
            // Holt das Widget für den Inhaltstyp (z.B. Poll, Task)
            $wallEntryWidget = WallStreamEntryWidget::getByContent($content);

            // Überspringen, wenn es keinen Creator gibt
            if (!$wallEntryWidget || !$wallEntryWidget->createRoute) {
                continue;
            }

            $url = $menu->contentContainer->createUrl($wallEntryWidget->createRoute);
            $label = ucfirst($content->getContentName());

            // Optionen bauen (Logik aus HumHub Core übernommen)
            $menuOptions = [
                'label' => $label,
                'icon' => $content->getIcon(),
                'url' => '#',
                'sortOrder' => $wallEntryWidget->createFormSortOrder ?? 900,
            ];

            // Klick-Verhalten je nach Modus (Inline Formular vs Modal)
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

            // Standard-Eintrag hinzufügen
            $menu->addEntry(new MenuLink($menuOptions));
        }

        // 2. UNSEREN CRM BUTTON HINZUFÜGEN
        // Jetzt können wir unseren Button sicher hinzufügen.

        // Modul-Check
        if (!$menu->contentContainer->isModuleEnabled('crm')) {
            return;
        }

        $crmUrl = $menu->contentContainer->createUrl('/crm/create/index');

        $menu->addEntry(new MenuLink([
            'label' => 'CRM Eintrag',
            'url' => $crmUrl,
            'icon' => Icon::get('address-card'),
            'sortOrder' => 350, // Positioniert zwischen Beitrag und Aufgaben
            'htmlOptions' => [
                'data-action-click' => 'ui.modal.load', // Öffnet das Modal
                'data-action-url' => $crmUrl
            ]
        ]));
    }

    /**
     * Fügt das Modul zur Space-Navigation (links) hinzu.
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
}
