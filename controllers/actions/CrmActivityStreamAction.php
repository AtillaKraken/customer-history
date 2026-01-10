<?php

namespace humhub\modules\crm\controllers\actions;

use humhub\modules\activity\actions\ActivityStreamAction;
use humhub\modules\activity\models\Activity;
use humhub\modules\comment\models\Comment;
use humhub\modules\like\models\Like;
use humhub\modules\crm\models\Interaction;
use humhub\modules\crm\models\Event;
use humhub\modules\crm\models\Contact;
use humhub\modules\crm\models\Organization;
use Yii;

class CrmActivityStreamAction extends ActivityStreamAction
{

    public function init()
    {
        parent::init();

        if (Yii::$app->user->isGuest) {
            throw new \yii\web\HttpException(403, 'Sie müssen sich einloggen, um die internen CRM-Informationen einzusehen.');
        }
    }
    /**
     * @inheritdoc
     */
    public function initQuery($options = [])
    {
        $query = parent::initQuery($options);

        // join activity table
        // connect Content-Stream-Entry with activity-table
        $query->query()->leftJoin('activity', 'content.object_id = activity.id AND content.object_model = :activityModel', [
            ':activityModel' => Activity::class
        ]);

        // join coment table
        // to ensure the ability to check to which kind of entry a "comment created"-event is referring to
        $query->query()->leftJoin('comment', 'activity.object_model = :commentModel AND activity.object_id = comment.id', [
            ':commentModel' => Comment::class
        ]);

        // join like table
        // to ensure the ability to check to which kind of entry a "user liked entry"-event is referring to
        $query->query()->leftJoin('like', 'activity.object_model = :likeModel AND activity.object_id = `like`.id', [
            ':likeModel' => Like::class
        ]);

        // list of Module entities
        $crmModels = [
            Interaction::class,
            Event::class,
            Contact::class,
            Organization::class
        ];

        // Filter | show entry when:
        // A) activity is part of one of the crm-entity tables (z.B. "Interaktion erstellt")
        // B) OR if its a comment AND belongs to a crm-entity-object
        // B) OR if its a like AND belongs to a crm-entity-object
        $query->query()->andWhere(['or',
            ['in', 'activity.object_model', $crmModels],
            ['and', ['activity.object_model' => Comment::class], ['in', 'comment.object_model', $crmModels]],
            ['and', ['activity.object_model' => Like::class], ['in', '`like`.object_model', $crmModels]]
        ]);

        return $query;
    }
}
