<?php

namespace app\modules\crm\widgets;

use humhub\components\Widget;
use app\modules\crm\models\forms\CrmFilter;
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

    /**
     * @var CrmFilter Filter Model has to be given by the controller,
     * to maintain the status (z.B. Search entry).
     */
    public ?CrmFilter $filter = null;

    public function run()
    {
        // Default URL: quick capture
        if ($this->createUrl === null) {
            $this->createUrl = $this->contentContainer->createUrl('/crm/create/index');
        }

        // Fallback: empty filter
        if ($this->filter === null) {
            $this->filter = new CrmFilter();
        }

        return $this->render('crmNavigation', [
            'contentContainer' => $this->contentContainer,
            'activeTab' => $this->activeTab,
            'createButtonLabel' => $this->createButtonLabel,
            'createUrl' => $this->createUrl,
            'filter' => $this->filter, // An View weitergeben
        ]);
    }
}
