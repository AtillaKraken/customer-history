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
            <th>Website</th>
            <th>Kontakte</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($organizations as $org): ?>
            <tr>
                <td>
                    <a href="#" data-action-click="ui.modal.load" data-action-url="<?= $org->content->container->createUrl('/crm/organization/view', ['id' => $org->id]) ?>">
                        <strong><?= Html::encode($org->name) ?></strong>
                    </a>
                </td>
                <td><span class="badge"><?= count($org->contacts) ?></span></td>
                <td class="text-right">
                    <?= Button::primary()->icon('fa-pencil')->xs()
                        ->action('ui.modal.load', $this->context->contentContainer->createUrl('/crm/organization/edit', ['id' => $org->id])) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="text-center"><?= LinkPager::widget(['pagination' => $pagination]) ?></div>
<?php endif; ?>
