<?php

namespace app\modules\crm\widgets;

// humhubs class to display stream entries
use humhub\modules\content\widgets\stream\WallStreamEntryWidget;
use humhub\modules\content\widgets\EditLink;
use humhub\modules\content\widgets\VisibilityLink;

/**
 *
 * @property-read mixed $controlsMenuEntries
 */
class InteractionWallEntry extends WallStreamEntryWidget
{
    /**
     * renderContent displays main content of the entry
     */
    protected function renderContent()
    {
        return InteractionCard::widget([
            'interaction' => $this->model,
            'isStream' => true,
            'startCollapsed' => false // in global/space stream: have it collapsed for better usability (overview)
        ]);
    }

    public function getControlsMenuEntries()
    {
        $entries = parent::getControlsMenuEntries();

        foreach ($entries as $key => $entry) {
            if (is_array($entry)) {
                $class = $entry[0] ?? null;

                // remove Visibility Link from menu
                if ($class === VisibilityLink::class) {
                    unset($entries[$key]);
                }

                // remove existing EditLink (to use the custome one instead => using the edit-modal)
                if ($class === EditLink::class) {
                    unset($entries[$key]);
                }
            }
        }

        // custom Edit-Link using the modal
        if ($this->model->content->canEdit()) {
            $entries[] = [
                EditLink::class,
                [
                    'model' => $this->model,
                    'url' => $this->model->content->container->createUrl('/crm/interaction/edit', ['id' => $this->model->id]),
                    'mode' => self::EDIT_MODE_MODAL
                ],
                ['sortOrder' => 100]
            ];
        }

        return $entries;
    }
}
