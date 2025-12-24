<?php

namespace humhub\modules\crm\controllers;

use app\modules\crm\models\Interaction;
use app\modules\crm\models\forms\CrmFilter;
use humhub\modules\content\permissions\CreatePrivateContent;
use humhub\widgets\ModalClose;
use HttpException;
use humhub\modules\content\components\ContentContainerController;
use Yii;

class InteractionController extends ContentContainerController
{
    // URL: /index.php?r=crm/interaction/index&cguid=<space-guid>

    public function actionIndex($view = 'list')
    {
        $filter = new CrmFilter();
        $filter->load(Yii::$app->request->get());

        $query = Interaction::find()
            ->contentContainer($this->contentContainer);

        // join for 'Mine' filter
        if (in_array('mine', $filter->filters)) {
            $query->joinWith('responsibleUsers');
        }

        $filter->apply($query, 'interaction');

        $interactions = $query->all();

        // AJAX vs PJAX
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            return $this->renderAjax('_list', ['interactions' => $interactions]);
        }

        return $this->render('index', [
            'interactions' => $interactions,
            'space' => $this->contentContainer,
            'filter' => $filter,
            'viewMode' => $view // pass view mode to view
        ]);
    }

    public function actionView($id)
    {
        $model = Interaction::find()
            ->contentContainer($this->contentContainer)
            ->where(['crm_interaction.id' => $id])
            ->one();

        if (!$model) throw new HttpException(404);
        if (!$model->content->canView()) throw new HttpException(403);

        // check if ajax to render plain/modal friendly
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', ['model' => $model]);
        }

        return $this->render('view', ['model' => $model]);
    }

    public function actionCreate()
    {
        // permission check
        if (!$this->contentContainer->permissionManager->can(new CreatePrivateContent())) {
            throw new HttpException(401, 'Zugriff verweigert.');
        }

        $model = new Interaction();
        $model->content->setContainer($this->contentContainer);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // close Modal & reload stream
            return ModalClose::widget([
                'saved' => true,
                'script' => 'humhub.modules.client.reload();'
            ]);
        }

        return $this->renderAjax('edit', [
            'model' => $model
        ]);
    }

    public function actionEdit($id)
    {
        $model = Interaction::find()
            ->contentContainer($this->contentContainer)
            ->where(['crm_interaction.id' => $id])
            ->one();

        // check existance
        if (!$model) {
            throw new HttpException(404, 'Interaktion nicht gefunden.');
        }

        // check permissions
        if (!$model->content->canEdit()) {
            throw new HttpException(401, 'Zugriff verweigert.');
        }

        // save
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return ModalClose::widget([
                'saved' => true,
                'script' => 'humhub.modules.client.reload();'
            ]);
        }

        // render view
        return $this->renderAjax('edit', [
            'model' => $model,
            'contentContainer' => $this->contentContainer
        ]);
    }

}
