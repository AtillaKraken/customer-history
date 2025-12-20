<?php
use yii\helpers\Html;
use humhub\widgets\Button;

/* @var $contacts app\modules\crm\models\Contact[] */
?>

<?php if (empty($contacts)): ?>
    <div class="alert alert-info">
        Keine Kontaktpersonen gefunden.
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
