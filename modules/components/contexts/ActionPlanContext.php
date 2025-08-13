<?php

namespace app\modules\reminder\components\contexts;

use app\helpers\DateHelper;
use app\modules\CheckList\models\ActionPlan;
use app\modules\lms\models\Request;
use Yii;
use yii\helpers\Html;

class ActionPlanContext extends BaseContext
{
    public const NAME = 'План действий';
    public const ALIAS = 'lead';
    public const ITEM_CLASS = Request::class;

    /**
     * @return array
     */
    public function placeholders($object = null): array
    {
        return array_merge(
            [
                'LINK' => [
                    'title' => Yii::t('app', 'Ссылка на план действий'),
                    'fetcher' => static function (?ActionPlan $actionPlan) {
                        if (!$actionPlan) {
                            return null;
                        }
                        $link = Yii::$app->urlManager->createAbsoluteUrl(['/action-plan/view', 'id' => $actionPlan->id]);
                        return Html::a($link, $link);
                    },
                    'testValue' => 'https://autocrm.ru/action-plan/view?id=123',
                ],
                'POTENTIAL_CONTACT_DATE_TIME' => [
                    'title' => Yii::t('app', 'Дата и время запланированного контакта'),
                    'fetcher' => static function (?ActionPlan $actionPlan) use ($object) {
                        if ($object) {
                            return DateHelper::sqlToFrontDateTime($object->scheduled_at);
                        }
                        return '';
                    },
                    'testValue' => date('d.m.Y H:i'),
                ],
                'POTENTIAL_CONTACT_COMMENT' => [
                    'title' => Yii::t('app', 'Комментарий в контакте'),
                    'fetcher' => static function (?ActionPlan $actionPlan) use ($object) {
                        return $object->comment ?? '';
                    },
                    'testValue' => 'Test comment',
                ],
            ]
        );
    }
}
