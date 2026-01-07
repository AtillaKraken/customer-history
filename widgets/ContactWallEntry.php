<?php

namespace humhub\modules\crm\widgets;

use humhub\modules\content\widgets\stream\WallStreamEntryWidget;
use humhub\modules\content\widgets\EditLink;
use humhub\modules\content\widgets\VisibilityLink;

class ContactWallEntry extends WallStreamEntryWidget
{
    protected function renderContent()
    {
        return ContactCard::widget([
            'contact' => $this->model,
            'isStream' => true,
            'startCollapsed' => false
        ]);
    }

    public function getControlsMenuEntries()
    {
        $entries = parent::getControlsMenuEntries();
        // Visibility entfernen
        foreach ($entries as $key => $entry) {
            if (is_array($entry) && isset($entry[0]) && $entry[0] === VisibilityLink::class) {
                unset($entries[$key]);
            }
        }
        // Custom Edit (Modal)
        if ($this->model->content->canEdit()) {
            $entries[] = [EditLink::class, ['model' => $this->model, 'url' => $this->model->content->container->createUrl('/crm/contact/edit', ['id' => $this->model->id]), 'mode' => self::EDIT_MODE_MODAL], ['sortOrder' => 100]];
        }
        return $entries;
    }
}
