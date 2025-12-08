<?php

use app\modules\crm\models\Interaction;
use app\modules\crm\widgets\CrmNavigation;
use humhub\modules\space\models\Space;
use yii\helpers\Html;
use humhub\widgets\Button;

/**
 * @var $interactions Interaction[]
 * @var $space Space
 */
?>

<?= CrmNavigation::widget([
    'contentContainer' => $space,
    'activeTab' => 'interaction',
    'createButtonLabel' => 'Neue Interaktion',
    'createUrl' => $space->createUrl('/crm/interaction/create')
]) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-building"></i> <strong>Interaktionen</strong> im Space
    </div>

    <div class="panel-body">


        <?php if (empty($interactions)): ?>
            <div class="alert alert-info">
                Noch keine Interaktionen hier. Leg doch die erste an!
            </div>
        <?php else: ?>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Kategorie</th>
                    <th>Stadt</th>
                    <th>Aktionen</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($interactions as $int): ?>
                    <tr>
                        <td style="vertical-align: middle;">
                            <strong><?= Html::encode($int->name) ?></strong>
                        </td>
                        <td style="vertical-align: middle;">
                            <span class="label label-default"><?= Html::encode($int->category) ?></span>
                        </td>
                        <td style="vertical-align: middle;">
                            <?= Html::encode($int->city) ?>
                        </td>
                        <td class="text-right">
                            <?= Button::primary()
                                ->icon('fa-pencil')
                                ->xs()
                                ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/interaction/edit', ['id' => $int->id]))
                            //TODO: Edit-Dialog für Interactions
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>


        <div class="clearfix" style="margin-bottom: 15px;">
            <?= Button::success('Neue Interaktion')
                ->icon('fa-plus')
                ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/interaction/create'))
                ->right()
                ->sm()
                ->loader(false)
            ?>
        </div>
    </div>
</div>
