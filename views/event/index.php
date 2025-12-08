<?php

use app\modules\crm\models\Event;
use humhub\modules\space\models\Space;
use yii\helpers\Html;
use humhub\widgets\Button;

/**
 * @var $events Event[]
 * @var $space Space
 */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-building"></i> <strong>Veranstaltungen</strong> im Space
    </div>

    <div class="panel-body">
        <div class="clearfix" style="margin-bottom: 15px;">
            <?= Button::success('Neue Veranstaltung')
                ->icon('fa-plus')
                ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/event/create'))
                ->right()
                ->sm()
                ->loader(false)
            ?>
        </div>
        <hr>

        <?php if (empty($events)): ?>
            <div class="alert alert-info">
                Es liegen noch keine Veranstaltungen vor. Leg doch die erste an!
            </div>
        <?php else: ?>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Titel</th>
                    <th>Format</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($events as $evt): ?>
                    <tr>
                        <td style="vertical-align: middle;">
                            <strong><?= Html::encode($evt->name) ?></strong>
                        </td>
                        <td style="vertical-align: middle;">
                            <span class="label label-default"><?= Html::encode($evt->type) ?></span>
                        </td>
                        <td class="text-right">
                            <?= Button::primary()
                                ->icon('fa-pencil')
                                ->xs()
                                ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/event/edit', ['id' => $evt->id]))
                            //TODO: Edit-Dialog für Events
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
</div>
