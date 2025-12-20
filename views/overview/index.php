<?php

use app\modules\crm\widgets\CrmNavigation;
use app\modules\crm\widgets\InteractionCard;
use app\modules\crm\widgets\CrmStatistics;
use app\modules\crm\widgets\MyInteractions;
use app\modules\crm\widgets\MyOrganizations;
use app\modules\crm\widgets\UpcomingEvents;

/* @var $space humhub\modules\space\models\Space */
/* @var $interactions array */
?>

<!-- Insert Navigation Widget -->
<?= CrmNavigation::widget([
    'contentContainer' => $space,
    'activeTab' => 'overview',
    'createButtonLabel' => 'Schnellerfassung' // Default URL = Dispatcher
]) ?>


<div class="row">
    <div class="col-md-8">

        <h3 style="margin-top: 20px; margin-bottom: 15px;">Persönliche Agenda</h3>

        <div class="crm-agenda-list">
            <?php foreach ($interactions as $interaction): ?>
                <!-- Nutzt das InteractionCard Widget für die große Darstellung -->
                <?= InteractionCard::widget(['interaction' => $interaction]) ?>
            <?php endforeach; ?>
        </div>

        <br>
        <h4>Kundenhistorien-Stream</h4>
        <div class="well">
            Hier kommt später der HumHub Stream hin (ActivityStreamWidget).
        </div>

    </div>

    <div class="col-md-4">
        <div >
            <?php try {
                echo MyInteractions::widget(['contentContainer' => $space]);
                echo MyOrganizations::widget(['contentContainer' => $space]);
                echo UpcomingEvents::widget(['contentContainer' => $space]);
                echo CrmStatistics::widget(['contentContainer' => $space]);
            } catch (Exception $e) {
            } ?>
        </div>
    </div>
</div>
