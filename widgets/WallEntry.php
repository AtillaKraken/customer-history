<?php

namespace app\modules\crm\widgets;

// humhubs class to display stream entries
use humhub\modules\content\widgets\stream\WallStreamEntryWidget;

class WallEntry extends WallStreamEntryWidget
{
    /**
     * renderContent displays main content of the entry
     */
    protected function renderContent()
    {

        return InteractionCard::widget(['interaction' => $this->model]);
    }
}
