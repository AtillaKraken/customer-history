<?php

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\widgets\ModalDialog;
use humhub\modules\ui\icon\widgets\Icon;

/* @var $contentContainer ContentContainerActiveRecord */
?>

<?php ModalDialog::begin(['header' => 'Zieleintrag für Schnellerfassung <strong>wählen</strong>', 'size' => 'large']) ?>
    <div class="modal-body" style="padding: 20px;">
        <div class="row">
            <!-- Contact -->
            <div class="col-md-6">
                <a href="#" data-action-click="ui.modal.load" data-action-url="<?= $contentContainer->createUrl('/crm/contact/create') ?>" class="panel panel-default text-center" style="display:block; color:inherit; text-decoration:none; padding: 20px; background:#f7f7f7; border:none;">
                <div style="font-size: 40px; margin-bottom: 10px;">
                        <?= Icon::get('user') ?>
                    </div>
                    <h4 style="font-weight:bold;">Kontaktperson</h4>
                    <p class="text-muted small">
                        Neue Person zu einer bestehenden Organisation hinzufügen.
                    </p>
                </a>
            </div>

            <!-- Interaction -->
            <div class="col-md-6">
                <a href="#" data-action-click="ui.modal.load" data-action-url="<?= $contentContainer->createUrl('/crm/interaction/create') ?>" class="panel panel-default text-center" style="display:block; color:inherit; text-decoration:none; padding: 20px; background:#f7f7f7; border:none;">
                    <div style="font-size: 40px; margin-bottom: 10px;">
                        <?= Icon::get('comments-o') ?>
                    </div>
                    <h4 style="font-weight:bold;">Interaktion</h4>
                    <p class="text-muted small">
                        Gespräch, E-Mail oder Meeting protokollieren oder planen.
                    </p>
                </a>
            </div>
        </div>
        <div class="row" style="margin-top: 20px;">
            <!-- Organization -->
            <div class="col-md-6">
                    <a href="#" data-action-click="ui.modal.load" data-action-url="<?= $contentContainer->createUrl('/crm/organization/create') ?>" class="panel panel-default text-center" style="display:block; color:inherit; text-decoration:none; padding: 20px; background:#f7f7f7; border:none;">
                    <div style="font-size: 40px; margin-bottom: 10px;">
                        <?= Icon::get('building') ?>
                    </div>
                    <h4 style="font-weight:bold;">Organisation</h4>
                    <p class="text-muted small">
                        Neue Firma oder Institution anlegen.
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
                        Einen neuen Termin im Kalender eintragen.
                    </p>
                </a>
            </div>
        </div>
    </div>
<?php ModalDialog::end() ?>
