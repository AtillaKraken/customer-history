<?php

use app\modules\crm\models\Organization;
use yii\helpers\Html;
use humhub\widgets\Button;

/**
 * @var $organizations Organization[]
 */
?>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Kategorie</th>
                    <th>Stadt</th>
                    <th>Interaktionen</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($organizations as $org): ?>
                    <tr>
                        <td style="vertical-align: middle;">
                            <strong><?= Html::encode($org->name) ?></strong>
                        </td>
                        <td style="vertical-align: middle;">
                            <span class="label label-default"><?= Html::encode($org->category) ?></span>
                        </td>
                        <td style="vertical-align: middle;">
                            <?= Html::encode($org->city) ?>
                        </td>
                        <td class="text-right">
                            <?= Button::primary()
                                ->icon('fa-pencil')
                                ->xs()
                                ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/organization/edit', ['id' => $org->id]))
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
