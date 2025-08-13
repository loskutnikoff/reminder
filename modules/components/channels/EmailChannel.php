<?php

namespace app\modules\reminder\components\channels;

use app\models\User;
use app\modules\reminder\models\ReminderTemplate;
use Yii;
use Exception;
use yii\helpers\VarDumper;

class EmailChannel implements ChannelInterface
{
    public function getChannelType(): int
    {
        return ReminderTemplate::CHANNEL_TYPE_EMAIL;
    }

    public function sendRemind(User $user, string $message, string $subject): bool
    {
        $message = Yii::$app->mailer
            ->compose('empty', ['content' => $message])
            ->setSubject($subject)
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($user->email);

        if (!$message->send()) {
            throw new Exception('Ошибка отправки уведомления ' . VarDumper::dumpAsString($user->email));
        }

        return true;
    }
}