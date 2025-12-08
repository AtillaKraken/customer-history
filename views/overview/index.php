<?php

use humhub\widgets\Button;
use yii\helpers\Html;

/* @var $space humhub\modules\space\models\Space */
/* @var $interactions array */
?>

<div class="panel panel-default">
    <div class="panel-body" style="padding: 10px;">
        <ul class="nav nav-pills">
            <li class="active"><a href="#">Übersicht</a></li>
            <li><a href="<?= $space->createUrl('/crm/organization/index') ?>">Organisationen</a></li>
            <li><a href="<?= $space->createUrl('/crm/contact/index') ?>">Kontaktpersonen</a></li>
            <li><a href="<?= $space->createUrl('/crm/interaction/index') ?>">Interaktionen</a></li>
            <li><a href="<?= $space->createUrl('/crm/event/index') ?>">Veranstaltungen</a></li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-md-8">

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-2" style="padding-top: 6px;">
                        <strong><i class="fa fa-filter"></i> Filter</strong> <span class="caret"></span>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Suchbegriff eingeben...">
                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                    <?= Button::success('Schnellerfassung')
                            ->icon('fa-plus')
                            ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/create/'))
                            ->right()
                            ->sm()
                            ->loader(false)
                        ?>
                    </div>
                </div>
            </div>
        </div>

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

        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><i class="fa fa-commenting-o"></i> Deine Interaktionen</strong>
                <small class="pull-right"><a href="#">Zeige alle</a></small>
            </div>
            <div class="panel-body" style="padding: 0;">
                <div style="padding: 10px; border-bottom: 1px solid #eee;">
                    <div style="margin-bottom: 2px;">
                        <strong>Anruf bei Marketing HAW</strong>
                        <span class="label label-danger pull-right" style="font-size: 9px;">ÜBERFÄLLIG</span>
                    </div>
                    <small class="text-muted">
                        19.10.24 | Betroffen: <i class="fa fa-building-o"></i> HAW Hamburg, <i class="fa fa-building-o"></i>+2
                    </small>
                </div>
                <div style="padding: 10px;">
                    <div style="margin-bottom: 2px;">
                        <strong>Längst überfällige Interaktion</strong>
                        <span class="label label-danger pull-right" style="font-size: 9px;">ÜBERFÄLLIG</span>
                    </div>
                    <small class="text-muted">
                        21.10.24 | Betroffen: <i class="fa fa-building-o"></i> MalocherMann...
                    </small>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><i class="fa fa-building"></i> Deine Organisationen</strong>
                <small class="pull-right"><a href="#">Zeige alle</a></small>
            </div>
            <div class="panel-body" style="padding: 0;">
                <div style="padding: 10px; border-bottom: 1px solid #eee;">
                    <strong>MalocherMannschaftHL</strong><br>
                    <small class="text-muted">Unternehmen | Handwerk | Lübeck</small>
                </div>
                <div style="padding: 10px;">
                    <strong>Innovative Hochschule</strong><br>
                    <small class="text-muted">Forschungsprojekt | Bildung</small>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"><strong>Schnellzugriff & Statistik</strong></div>
            <div class="panel-body">
                <ul class="list-unstyled" style="margin-bottom: 0;">
                    <li>Organisationen: <strong>XX</strong></li>
                    <li>Kontaktpersonen: <strong>XXX</strong></li>
                    <li>Interaktionen (30 Tage): <strong>XX</strong></li>
                </ul>
            </div>
        </div>

    </div>
</div>
