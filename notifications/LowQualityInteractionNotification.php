<?php

namespace humhub\modules\crm\notifications;

use humhub\modules\notification\components\BaseNotification;
use app\modules\crm\models\Interaction;
use humhub\modules\space\models\Space;
use Yii;
use yii\helpers\Html;

class LowQualityInteractionNotification extends BaseNotification
{
    public $moduleId = 'crm';

    // refers to view (in views/notifications/)
    public $viewName = 'lowQualityInteractionNotification';

    public function category()
    {
        return new CrmNotificationCategory();
    }

    public function getUrl()
    {
        try {
            if ($this->source && method_exists($this->source, 'createUrl')) {
                return $this->source->createUrl('/crm/interaction/index');
            }
        } catch (\Throwable $ex) {
            Yii::error("CrmNotification: getUrl failed: " . $ex->getMessage(), __METHOD__);
        }
        return '';
    }

    protected function getCount()
    {
        // check does the record exist?
        if (!isset($this->record)) {
            throw new \Exception("Notification Record is missing/null.");
        }

        $user = $this->record->user;
        /** @var Space $space */
        $space = $this->source;

        if (!$user) throw new \Exception("User missing in Record.");
        if (!$space) throw new \Exception("Source Space missing.");

        // loq-quality interaction query
        // also get their responsibleUsers (to inform and nudge them into reconsidering this month's entries)
        $interactions = Interaction::find()
            ->contentContainer($space)
            ->leftJoin('crm_interaction_responsible_user', 'crm_interaction.id = crm_interaction_responsible_user.interaction_id')
            ->andWhere(['crm_interaction_responsible_user.user_id' => $user->id])
            ->all();

        $count = 0;
        foreach ($interactions as $interaction) {
            if ($interaction->getQualityScore() < 40) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Notification-text
     */
    public function html()
    {
        try {
            $count = $this->getCount();
            $spaceName = '';
            if ($this->source && isset($this->source->displayName)) {
                $spaceName = Html::encode($this->source->displayName);
            }

            // count > 0
            if ($count > 0) {
                return 'Du hast diesen Monat <strong>' . $count . ' Interaktion(en)</strong> in "<strong>' . $spaceName . '</strong>" mit zu wenig Informationen versorgt. Bitte trage sie nach.';
            }

            // Fallback: if count is 0 / Good work., Team!
            return 'Keine deiner Interaktionen in "<strong>' . $spaceName . '</strong>" sind diesen Monat mit zu wenig Informationen versorgt worden. Gute Arbeit, weiter so!';

        } catch (\Throwable $ex) {
            // throw error
            return '<strong style="color:red">Fehler bei der Berechnung qualitativ schlecht-erfasster Interaktioenn: ' . $ex->getMessage() . '</strong>';
        }
    }
}
