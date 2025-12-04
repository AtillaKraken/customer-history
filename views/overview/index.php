<?php

use app\modules\crm\models\Interaction;
use humhub\modules\space\models\Space;
use humhub\widgets\Button;

/* @var $space Space */
/* @var $interactions Interaction[] */
?>

<div class="panel panel-default">
    <div class="panel-body" style="padding: 10px;">
        <ul class="nav nav-pills">
            <li class="active"><a href="#">Übersicht</a></li>
            <li><a href="<?= $space->createUrl('/crm/organization/index') ?>">Organisationen</a></li>
            <li><a href="#">Kontaktpersonen</a></li>
            <li><a href="#">Interaktionen</a></li>
            <li><a href="#">Veranstaltungen</a></li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-md-8">

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-2">
                        <strong><i class="fa fa-filter"></i> Filter</strong>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" placeholder="Suchbegriff eingeben...">
                    </div>
                    <div class="col-md-4 text-right">
                        <?= Button::success('Schnellerfassung')->icon('fa-plus')->sm() ?>
                    </div>
                </div>
            </div>
        </div>

        <h3>Persönliche Agenda</h3>

        <?php foreach ($interactions as $interaction): ?>
            <?= $this->render('_interaction_card', ['interaction' => $interaction]) ?>
        <?php endforeach; ?>

        <?php if (empty($interactions)): ?>
            <div class="alert alert-info">Keine anstehenden Interaktionen gefunden.</div>
        <?php endif; ?>

        <hr>
        <h4>Kundenhistorien-Stream</h4>
        <div class="well">
            Hier kommt später der HumHub Stream hin (ActivityStreamWidget).
        </div>

    </div>

    <div class="col-md-4">

        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>🗓️ Deine Interaktionen</strong>
                <small class="pull-right"><a href="#">Zeige alle</a></small>
            </div>
            <div class="panel-body">
                <div style="margin-bottom: 10px; border-left: 3px solid #d9534f; padding-left: 10px;">
                    <strong>Anruf bei Marketing HAW</strong> <span class="label label-danger">Überfällig</span><br>
                    <small class="text-muted">19.10.24 • Ministerium für Verkehr...</small>
                </div>
                <div style="margin-bottom: 10px; border-left: 3px solid #d9534f; padding-left: 10px;">
                    <strong>Längst überfällige Interaktion</strong> <span class="label label-danger">Überfällig</span><br>
                    <small class="text-muted">21.10.24 • MalocherMannschaft...</small>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>🏢 Deine Organisationen</strong>
                <small class="pull-right"><a href="#">Zeige alle</a></small>
            </div>
            <div class="panel-body">
                <ul class="list-unstyled">
                    <li><strong>MalocherMannschaftHL</strong><br><small>Handwerk | Lübeck</small></li>
                    <hr style="margin: 5px 0;">
                    <li><strong>Innovative Hochschule</strong><br><small>Bildung | Forschung</small></li>
                </ul>
            </div>
        </div>

    </div>
</div>
