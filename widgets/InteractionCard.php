<?php

namespace app\modules\crm\widgets;

use app\modules\crm\models\Interaction;
use humhub\components\Widget;

/**
 * InteractionCard Widget
 *
 * Displays an interaction Entry as an collapsable card
 */
class InteractionCard extends Widget
{
    /**
     * @var Interaction
     */
    public $interaction;

    /**
     * @var bool true: card is rendered in "Stream Mode" (slim display w/o duplicate header/footer)
     */
    public $isStream = false;

    /**
     * @var bool $startCollapsed true: card is autmatically collapsed when rendered => usability
     */
    public $startCollapsed = true;

    /**
     * @inheritdoc
     */
    public function run()
    {
        // operations such as date formatting includable in here
        return $this->render('interactionCard', [
            'interaction' => $this->interaction,
            'isStream' => $this->isStream,
            'startCollapsed' => $this->startCollapsed
        ]);
    }
}
