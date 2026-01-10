<?php

namespace humhub\modules\crm\widgets;

use humhub\modules\content\widgets\stream\WallStreamEntryWidget;
use humhub\modules\content\widgets\EditLink;
use humhub\modules\content\widgets\VisibilityLink;

class OrganizationWallEntry extends WallStreamEntryWidget
{
    protected function renderContent()
    {
        return OrganizationCard::widget([
            'organization' => $this->model,
            'isStream' => true,
            'startCollapsed' => false
        ]);
    }

    public function getControlsMenuEntries()
    {
        $entries = parent::getControlsMenuEntries();

        foreach ($entries as $key => $entry) {
            if (is_array($entry)) {
                $class = isset($entry[0]) ? $entry[0] : null;

                if ($class === VisibilityLink::class) {
                    unset($entries[$key]);
                }
                if ($class === EditLink::class) {
                    unset($entries[$key]);
                }
            }
        }

        if ($this->model->content->canEdit()) {
            $entries[] = [
                EditLink::class,
                [
                    'model' => $this->model,
                    'url' => $this->model->content->container->createUrl('/crm/organization/edit', ['id' => $this->model->id]),
                    'mode' => self::EDIT_MODE_MODAL
                ],
                ['sortOrder' => 100]
            ];
        }

        return $entries;
    }
}
