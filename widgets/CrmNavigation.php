<?php

namespace app\modules\crm\widgets;

use humhub\components\Widget;
use humhub\modules\content\components\ContentContainerActiveRecord;

class CrmNavigation extends Widget
{
    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    /**
     * @var string active Tab (overview, organization, contact, interaction, event)
     */
    public $activeTab = 'overview';

    /**
     * @var string Action Button Label
     */
    public $createButtonLabel = 'Schnellerfassung';

    /**
     * @var string Button's URL. If null, dispatcher /crm/create/index (quick capture) is used
     */
    public $createUrl = null;

    public function run()
    {
        // Default URL: quick capture
        if ($this->createUrl === null) {
            $this->createUrl = $this->contentContainer->createUrl('/crm/create/index');
        }

        return $this->render('crmNavigation', [
            'contentContainer' => $this->contentContainer,
            'activeTab' => $this->activeTab,
            'createButtonLabel' => $this->createButtonLabel,
            'createUrl' => $this->createUrl,
        ]);
    }
}
