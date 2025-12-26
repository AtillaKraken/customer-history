<?php

use yii\helpers\Html;
use humhub\widgets\Button;
use humhub\widgets\LinkPager;
use app\modules\crm\models\Interaction;

/* @var $contacts app\modules\crm\models\Contact[] */
/* @var $pagination yii\data\Pagination */
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
            <th class="text-center" title="Anzahl Interaktionen"><i class="fa fa-comments-o"></i></th>
            <th class="text-center" title="Anzahl Veranstaltungen"><i class="fa fa-calendar"></i></th>
            <th class="text-right">Aktionen</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($contacts as $cnt): ?>
            <?php
            // Count Interactions on the fly
            $interactionCount = Interaction::find()
                ->innerJoinWith('contacts')
                ->where(['crm_contact.id' => $cnt->id])
                ->count();

            // Count Events
            $eventCount = $cnt->getParticipations()->count();
            ?>
            <tr>
                <td style="vertical-align: middle;">
                    <a href="#" data-action-click="ui.modal.load"
                       data-action-url="<?= $cnt->content->container->createUrl('/crm/contact/view', ['id' => $cnt->id]) ?>">

                        <?php if (empty($cnt->name)): ?>
                            <strong class="text-danger">
                                <?= Html::encode($cnt->getDisplayName()) ?>
                            </strong>
                        <?php else: ?>
                            <strong>
                                <?= Html::encode($cnt->getDisplayName()) ?>
                            </strong>
                        <?php endif; ?>
                        <br>
                        <small class="text-muted"><?= Html::encode($cnt->gender) ?></small>
                </td>
                <td style="vertical-align: middle;">
                    <?php if ($cnt->organization): ?>
                        <i class="fa fa-building-o"></i> <?= Html::encode($cnt->organization->name) ?>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
                <td style="vertical-align: middle;">
                    <?php foreach ($cnt->roleList as $role): ?>
                        <span class="label label-default">
                        <?= Html::encode($role) ?>
                    </span>
                    <?php endforeach; ?>
                </td>
                <td style="vertical-align: middle;">
                    <?php if ($cnt->email): ?>
                        <div><i class="fa fa-envelope-o"></i> <a
                                href="mailto:<?= Html::encode($cnt->email) ?>"><?= Html::encode($cnt->email) ?></a>
                        </div>
                    <?php endif; ?>
                    <?php if ($cnt->phone_number): ?>
                        <div><i class="fa fa-phone"></i> <?= Html::encode($cnt->phone_number) ?></div>
                    <?php endif; ?>
                </td>

                <td style="vertical-align: middle;" class="text-center">
                    <span class="label label-default"><?= $interactionCount ?></span>
                </td>
                <td style="vertical-align: middle;" class="text-center">
                    <span class="label label-default"><?= $eventCount ?></span>
                </td>

                <td class="text-right" style="vertical-align: middle;">
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
    <div class="text-center">
        <?= LinkPager::widget(['pagination' => $pagination]) ?>
    </div>
<?php endif; ?>
