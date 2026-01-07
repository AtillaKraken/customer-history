<?php

use humhub\modules\crm\widgets\CrmNavigation;
use humhub\modules\crm\widgets\InteractionCard;
use humhub\modules\crm\widgets\CrmStatistics;
use humhub\modules\crm\widgets\MyInteractions;
use humhub\modules\crm\widgets\MyOrganizations;
use humhub\modules\crm\widgets\UpcomingEvents;
use humhub\modules\activity\widgets\ActivityStreamViewer;
use humhub\widgets\LinkPager;

/* @var $space humhub\modules\space\models\Space */
/* @var $interactions array */
/* @var $pagination yii\data\Pagination */
?>

<!-- Insert Navigation Widget -->
<?= CrmNavigation::widget([
    'contentContainer' => $space,
    'activeTab' => 'overview',
    'createButtonLabel' => 'Schnellerfassung'
]) ?>


<div class="row">
    <div class="col-md-8">

        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list-ol"></i> <strong>Persönliche Agenda</strong>
            </div>
            <div class="panel-body">
                <div class="crm-agenda-list">
                    <?php if (empty($interactions)): ?>
                        <div class="text-muted text-center" style="padding: 20px;">
                            <i class="fa fa-check-circle-o fa-2x"></i><br>
                            Alles erledigt! Keine offenen Interaktionen in deiner Agenda.
                        </div>
                    <?php else: ?>
                        <?php foreach ($interactions as $interaction): ?>
                            <?= InteractionCard::widget(['interaction' => $interaction]) ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="text-center" style="margin-top: 15px;">
                    <?= LinkPager::widget(['pagination' => $pagination]) ?>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
                <?= ActivityStreamViewer::widget([
                    'contentContainer' => $space,
                    'streamAction' => '/crm/stream/stream',
                    'messageStreamEmpty' => 'Keine Aktivitäten gefunden.',
                ]) ?>
        </div>

    </div>

    <div class="col-md-4">
        <div>
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
