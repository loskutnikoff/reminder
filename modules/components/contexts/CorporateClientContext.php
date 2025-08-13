<?php

namespace app\modules\reminder\components\contexts;

use app\components\contextRenderer\contexts\ClientContext;
use app\helpers\DateHelper;
use app\models\CorporateClient;
use Yii;
use yii\helpers\Html;

class CorporateClientContext extends BaseContext
{
    public const NAME = 'Корпоративный клиент дилера';
    public const ALIAS = 'client-corporate';
    public const ITEM_CLASS = CorporateClient::class;

    /**
     * @return array
     */
    public function placeholders($object = null): array
    {
        return array_merge(
            ClientContext::placeholders(),
            [
                'LINK' => [
                    'title' => Yii::t('app', 'Ссылка на корп. клиента'),
                    'fetcher' => static function (?CorporateClient $client) {
                        if ($client) {
                            $link = Yii::$app->urlManager->createAbsoluteUrl(['/corporate-client/view', 'id' => $client->id]);
                            return Html::a(
                                $link,
                                $link
                            );
                        }
                    },
                    'testValue' => 'https://autocrm.ru/corporate-client/view?id=123',
                ],
                'POTENTIAL_CONTACT_DATE_TIME' => [
                    'title' => Yii::t('app', 'Дата и время запланированного контакта'),
                    'fetcher' => static function (?CorporateClient $client) use ($object) {
                        if ($object) {
                            return DateHelper::sqlToFrontDateTime($object->scheduled_at);
                        }
                    },
                    'testValue' => '22.03.2024 15:00',
                ],
                'POTENTIAL_CONTACT_COMMENT' => [
                    'title' => Yii::t('app', 'Комментарий в контакте'),
                    'fetcher' => static function (?CorporateClient $client) use ($object) {
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
