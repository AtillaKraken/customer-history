<?php
/* @var $countOrgs int */
/* @var $countContacts int */
/* @var $countEvents int */
/* @var $countInteractions30d int */
?>
<div class="panel panel-default">
    <div class="panel-heading"><strong>Schnellzugriff & Statistik</strong></div>
    <div class="panel-body">
        <ul class="list-unstyled" style="margin-bottom: 0;">
            <li>Organisationen: <strong><?= $countOrgs ?></strong></li>
            <li>Kontaktpersonen: <strong><?= $countContacts ?></strong></li>
            <li>Veranstaltungen: <strong><?= $countEvents ?></strong></li>
            <li>Interaktionen (30 Tage): <strong><?= $countInteractions30d ?></strong></li>
        </ul>
    </div>
</div>
