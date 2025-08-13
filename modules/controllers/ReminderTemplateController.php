<?php

namespace app\modules\reminder\controllers;

use app\actions\CreateAction;
use app\actions\IndexAction;
use app\actions\UpdateAction;
use app\components\Perm;
use app\modules\reminder\forms\ReminderTemplateForm;
use app\modules\reminder\models\ReminderTemplateSearch;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ReminderTemplateController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'denyCallback' => static fn() => Yii::$app->user->loginRequired(),
                'rules' => [
                    ['actions' => ['index'], 'allow' => true, 'roles' => [Perm::REMINDER_TEMPLATE_LIST]],
                    ['actions' => ['create', 'placeholders'], 'allow' => true, 'roles' => [Perm::REMINDER_TEMPLATE_CREATE]],
                    ['actions' => ['update', 'placeholders'], 'allow' => true, 'roles' => [Perm::REMINDER_TEMPLATE_UPDATE]],
                    ['actions' => ['delete'], 'allow' => true, 'roles' => [Perm::REMINDER_TEMPLATE_DELETE]],
                ],
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function actions(): array
    {
        return [
            'index' => [
                'class' => IndexAction::class,
                'modelClass' => ReminderTemplateSearch::class,
            ],
            'create' => [
                'class' => CreateAction::class,
                'modelClass' => ReminderTemplateForm::class,
            ],
            'update' => [
                'class' => UpdateAction::class,
                'modelClass' => ReminderTemplateForm::class,
            ],
        ];
    }

    /**
     * @param $alias
     * @return Response
     * @throws InvalidConfigException
     */
    public function actionPlaceholders($alias): Response
    {
        $context = Yii::$app->reminderComponent->getContextByAlias($alias);
        $placeholders = $context->placeholders() ?? [];
        return $this->asJson(
            [
                'placeholders' => array_map(
                    function (array $data, $placeholder) {
                        return [
                            'title' => Html::encode($data['title'] ?? ''),
                            'placeholder' => $data['macro'] ?? "%$placeholder%",
                        ];
                    },
                    $placeholders,
                    array_keys($placeholders)
                ),
            ]
        );
    }

    public function actionDelete($id): Response
    {
        $model = ReminderTemplateForm::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        $model->delete();

        return $this->redirect(['index']);
    }
}
