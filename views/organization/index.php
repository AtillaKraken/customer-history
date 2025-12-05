<?php

use app\modules\crm\models\Organization;
use humhub\modules\space\models\Space;
use yii\helpers\Html;
use humhub\widgets\Button;

/**
 * @var $organizations Organization[]
 * @var $space Space
 */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-building"></i> <strong>Organisationen</strong> im Space
    </div>

    <div class="panel-body">
        <div class="clearfix">
            <?= Button::success('Neue Organisation')
                ->icon('fa-plus')
                ->right()
                ->sm()
            ?>
        </div>
        <hr>

        <?php if (empty($organizations)): ?>
            <div class="alert alert-info">
                Noch keine Organisationen hier. Leg doch die erste an!
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
                <?php foreach ($organizations as $org): ?>
                    <tr>
                        <td>
                            <strong><?= Html::encode($org->name) ?></strong>
                        </td>
                        <td>
                            <span class="label label-default"><?= Html::encode($org->category) ?></span>
                        </td>
                        <td>
                            <?= Html::encode($org->city) ?>
                        </td>
                        <td>
                            <?= Button::primary()->icon('fa-pencil')->xs() ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
