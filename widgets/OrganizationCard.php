<?php

namespace app\modules\crm\widgets;

use humhub\components\Widget;

class OrganizationCard extends Widget
{
    public $organization;
    public $isStream = false;
    public $startCollapsed = true;

    public function run()
    {
        return $this->render('organizationCard', [
            'organization' => $this->organization,
            'isStream' => $this->isStream,
            'startCollapsed' => $this->startCollapsed
        ]);
    }
}
