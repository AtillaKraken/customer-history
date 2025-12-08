<?php

use app\modules\crm\widgets\CrmNavigation;

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
                <?= $this->render('../../widgets/views/interactionCard', ['interaction' => $interaction]) ?>
            <?php endforeach; ?>
        </div>

        <br>
        <h4>Kundenhistorien-Stream</h4>
        <div class="well">
            Hier kommt später der HumHub Stream hin (ActivityStreamWidget).
        </div>

    </div>

    <div class="col-md-4">
        <!-- Sidebar Content (Statistik, Deine Interaktionen etc.) unverändert lassen -->
        <div class="panel panel-default">
            <div class="panel-heading"><strong>Schnellzugriff & Statistik</strong></div>
            <div class="panel-body">...</div>
        </div>
    </div>
</div>
