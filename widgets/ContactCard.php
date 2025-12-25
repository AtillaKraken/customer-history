<?php

namespace app\modules\crm\widgets;

use humhub\components\Widget;

class ContactCard extends Widget
{
    public $contact;
    public $isStream = false;
    public $startCollapsed = true;

    public function run()
    {
        return $this->render('contactCard', [
            'contact' => $this->contact,
            'isStream' => $this->isStream,
            'startCollapsed' => $this->startCollapsed
        ]);
    }
}
