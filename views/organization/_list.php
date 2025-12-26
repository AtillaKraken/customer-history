<?php

use humhub\widgets\Button;
use yii\helpers\Html;
use humhub\widgets\LinkPager;

/* @var $organizations app\modules\crm\models\Organization[] */
/* @var $pagination yii\data\Pagination */
?>

<?php if (empty($organizations)): ?>
    <div class="alert alert-info">Keine Organisationen gefunden.</div>
<?php else: ?>
    <table class="table table-hover">
        <thead>
        <tr>
            <th>Name</th>
            <th>Stadt</th>
            <th>Größe</th>
            <th class="text-center" title="Anzahl Kontakte"><i class="fa fa-users"></i></th>
            <th class="text-center" title="Anzahl Interaktionen"><i class="fa fa-comments-o"></i></th>
            <th class="text-center" title="Anzahl Veranstaltungen"><i class="fa fa-calendar"></i></th>
            <th class="text-right">Bearbeiten</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($organizations as $org): ?>
            <tr>
                <td style="vertical-align: middle;">
                    <a href="#" data-action-click="ui.modal.load"
                       data-action-url="<?= $org->content->container->createUrl('/crm/organization/view', ['id' => $org->id]) ?>">
                        <strong><?= Html::encode($org->name) ?></strong>
                    </a>
                </td>
                <td style="vertical-align: middle;">
                    <?= $org->hasAttribute('city') ? Html::encode($org->city) : '-' ?>
                </td>
                <td style="vertical-align: middle;">
                    <?= $org->hasAttribute('size') ? Html::encode($org->size) : '-' ?>
                </td>

                <td style="vertical-align: middle;" class="text-center">
                    <span class="label label-default">
                        <?= $org->getContacts()->count() ?>
                    </span>
                </td>
                <td style="vertical-align: middle;" class="text-center">
                    <span class="label label-default">
                        <?= $org->getInteractions()->count() ?>
                    </span>
                </td>
                <td style="vertical-align: middle;" class="text-center">
                    <span class="label label-default">
                        <?= $org->getParticipations()->count() ?>
                    </span>
                </td>

                <td class="text-right" style="vertical-align: middle;">
                    <?= Button::primary()->icon('fa-pencil')->xs()
                        ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/organization/edit', ['id' => $org->id])) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="text-center"><?= LinkPager::widget(['pagination' => $pagination]) ?></div>
<?php endif; ?>
