<?php

namespace app\modules\reminder\components\channels;

use app\models\User;
use app\modules\reminder\models\ReminderTemplate;
use app\modules\telegram\services\TgService;
use yii\helpers\VarDumper;
use Exception;

class TelegramChannel implements ChannelInterface
{
    public function getChannelType(): int
    {
        return ReminderTemplate::CHANNEL_TYPE_TG;
    }

    public function sendRemind(User $user, string $message, string $subject): bool
    {
        $tg = TgService::instance();

        $text = [
            "<b>" . $subject . "</b>",
            "",
            $tg->normalizeHtml(str_replace('<br>', "\n\n", $message)),
        ];

        if (!$tg->sendMessage((array)$user->tg_ext_id, implode("\n", $text))) {
            throw new Exception('Ошибка отправки уведомления ' . VarDumper::dumpAsString($user->tg_ext_id));
        }
    }
}