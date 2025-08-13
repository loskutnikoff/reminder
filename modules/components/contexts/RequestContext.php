<?php

namespace app\modules\reminder\components\contexts;

use app\components\contextRenderer\contexts\ClientContext as BaseClientContext;
use app\helpers\DateHelper;
use app\modules\lms\models\Request;
use app\components\contextRenderer\contexts\RequestContext as BaseRequestContext;
use Yii;
use yii\helpers\Html;

class RequestContext extends BaseContext
{
    public const NAME = 'Лид';
    public const ALIAS = 'lead';
    public const ITEM_CLASS = Request::class;

    /**
     * @return array
     */
    public function placeholders($object = null): array
    {
        return array_merge(
            BaseRequestContext::placeholders(),
            self::applyContextRelation(BaseClientContext::placeholders(), 'client'),
            [
                'LINK' => [
                    'title' => Yii::t('app', 'Ссылка на лид'),
                    'fetcher' => static function (?Request $request) {
                        if ($request) {
                            $link = Yii::$app->urlManager->createAbsoluteUrl(['/lms/request/update', 'id' => $request->id]);
                            return Html::a(
                                $link,
                                $link
                            );
                        }
                    },
                    'testValue' => 'https://autocrm.ru/lms/request/update?id=123',
                ],
                'POTENTIAL_CONTACT_DATE_TIME' => [
                    'title' => Yii::t('app', 'Дата и время запланированного контакта'),
                    'fetcher' => static function (?Request $client) use ($object) {
                        if ($object) {
                            return DateHelper::sqlToFrontDateTime($object->scheduled_at);
                        }
                    },
                    'testValue' => '22.03.2024 15:00',
                ],
                'POTENTIAL_CONTACT_COMMENT' => [
                    'title' => Yii::t('app', 'Комментарий в контакте'),
                    'fetcher' => static function (?Request $client) use ($object) {
                        if ($object) {
                            return $object->comment;
                        }
                    },
                    'testValue' => 'Test comment',
                ],
            ]
        );
    }
}
