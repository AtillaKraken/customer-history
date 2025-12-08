<?php

namespace humhub\modules\crm\widgets;

use humhub\components\Widget;

/**
 * InteractionCard Widget
 *
 * Displays an interaction Entry as an collapsable card
 */
class InteractionCard extends Widget
{
    /**
     * @var array Array for the data (currenlty just Mockdata)
     */
    public $interaction;

    /**
     * @inheritdoc
     */
    public function run()
    {
        // operations such as date formatting includable in here
        return $this->render('interactionCard', [
            'interaction' => $this->interaction
        ]);
    }
}
