<?php

use app\modules\crm\models\Contact;
use app\modules\crm\widgets\CrmNavigation;
use humhub\modules\space\models\Space;
use yii\helpers\Html;
use humhub\widgets\Button;

/**
 * @var $contacts Contact[]
 * @var $space Space
 */
?>


<?= CrmNavigation::widget([
    'contentContainer' => $space,
    'activeTab' => 'contact',
    'createButtonLabel' => 'Neue Kontaktperson',
    'createUrl' => $space->createUrl('/crm/contact/create')
]) ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-address-card-o"></i> <strong>Kontaktpersonen</strong> im Space
    </div>

    <div class="panel-body">
        <?php if (empty($contacts)): ?>
            <div class="alert alert-info">
                Noch keine Kontaktpersonen hier.
            </div>
        <?php else: ?>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Organisation</th>
                    <th>Rolle</th>
                    <th>Kontakt</th>
                    <th class="text-right">Aktionen</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($contacts as $cnt): ?>
                    <tr>
                        <td style="vertical-align: middle;">
                            <strong><?= Html::encode($cnt->name) ?></strong><br>
                            <small class="text-muted"><?= Html::encode($cnt->gender) ?></small>
                        </td>
                        <td style="vertical-align: middle;">
                            <?php if($cnt->organization): ?>
                                <i class="fa fa-building-o"></i> <?= Html::encode($cnt->organization->name) ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td style="vertical-align: middle;">
                            <?= Html::encode($cnt->roles) ?>
                        </td>
                        <td style="vertical-align: middle;">
                            <?php if($cnt->email): ?>
                                <div><i class="fa fa-envelope-o"></i> <a href="mailto:<?= Html::encode($cnt->email) ?>"><?= Html::encode($cnt->email) ?></a></div>
                            <?php endif; ?>
                            <?php if($cnt->phone_number): ?>
                                <div><i class="fa fa-phone"></i> <?= Html::encode($cnt->phone_number) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-right">
                            <?= Button::primary()
                                ->icon('fa-pencil')
                                ->xs()
                                ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/contact/edit', ['id' => $cnt->id]))
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>


        <div class="clearfix" style="margin-bottom: 15px;">
            <?= Button::success('Neue Kontaktperson')
                ->icon('fa-plus')
                ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/contact/create'))
                ->right()
                ->sm()
                ->loader(false)
            ?>
        </div>
    </div>
</div>
