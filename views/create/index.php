<?php

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\widgets\ModalDialog;
use humhub\modules\ui\icon\widgets\Icon;

/* @var $contentContainer ContentContainerActiveRecord */
?>

<?php ModalDialog::begin(['header' => 'Zieleintrag für Schnellerfassung <strong>wählen</strong>', 'size' => 'large']) ?>
    <div class="modal-body" style="padding: 20px;">
        <div class="row">
            <!-- Organization -->
            <div class="col-md-6">
                <a href="#" data-action-click="ui.modal.load" data-action-url="<?= $contentContainer->createUrl('/crm/organization/create') ?>" class="panel panel-default text-center" style="display:block; color:inherit; text-decoration:none; padding: 20px; background:#f7f7f7; border:none;">
                    <div style="font-size: 40px; margin-bottom: 10px;">
                        <?= Icon::get('building') ?>
                    </div>
                    <h4 style="font-weight:bold;">Organisation</h4>
                    <p class="text-muted small">
                        Erfasse Unternehmen, Institutionen oder Partner mit Stammdaten wie Branche und Standort.  Grundlage für alle Kontakte und Interaktionen.
                    </p>
                </a>
            </div>

            <!-- Contact -->
            <div class="col-md-6">
                <a href="#" data-action-click="ui.modal.load" data-action-url="<?= $contentContainer->createUrl('/crm/contact/create') ?>" class="panel panel-default text-center" style="display:block; color:inherit; text-decoration:none; padding: 20px; background:#f7f7f7; border:none;">
                <div style="font-size: 40px; margin-bottom: 10px;">
                        <?= Icon::get('user') ?>
                    </div>
                    <h4 style="font-weight:bold;">Kontaktperson</h4>
                    <p class="text-muted small">
                        Lege Ansprechpartner einer Organisation an und speichere deren Rollen und Kontaktdaten für gezielte Kommunikation.
                    </p>
                </a>
            </div>
        </div>
        <div class="row" style="margin-top: 20px;">

            <!-- Interaction -->
            <div class="col-md-6">
                <a href="#" data-action-click="ui.modal.load" data-action-url="<?= $contentContainer->createUrl('/crm/interaction/create') ?>" class="panel panel-default text-center" style="display:block; color:inherit; text-decoration:none; padding: 20px; background:#f7f7f7; border:none;">
                    <div style="font-size: 40px; margin-bottom: 10px;">
                        <?= Icon::get('comments-o') ?>
                    </div>
                    <h4 style="font-weight:bold;">Interaktion</h4>
                    <p class="text-muted small">
                        Dokumentiere Gespräche und E-Mails mit Kontaktpersonen. Plane Follow-ups und weise Verantwortlichkeiten zu, damit nichts verloren geht.
                    </p>
                </a>
            </div>
            <!-- Event -->
            <div class="col-md-6">
                <a href="#" data-action-click="ui.modal.load" data-action-url="<?= $contentContainer->createUrl('/crm/event/create') ?>" class="panel panel-default text-center" style="display:block; color:inherit; text-decoration:none; padding: 20px; background:#f7f7f7; border:none;">
                    <div style="font-size: 40px; margin-bottom: 10px;">
                        <?= Icon::get('calendar') ?>
                    </div>
                    <h4 style="font-weight:bold;">Veranstaltung</h4>
                    <p class="text-muted small">
                        Erstelle Veranstaltungen wie Workshops oder Messen. Verknüpfe Teilnehmende, um später nachvollziehen zu können, wer dabei war.
                    </p>
                </a>
            </div>
        </div>
    </div>
<?php ModalDialog::end() ?>
