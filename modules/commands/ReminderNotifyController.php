<?php

namespace app\commands;

use app\modules\reminder\models\Reminder;
use DateTimeImmutable;
use Yii;
use yii\console\Controller;
use Throwable;

class ReminderNotifyController extends Controller
{
    public function actionSend()
    {
        $date = new DateTimeImmutable('now');
        $queryReminder = Reminder::find()
            ->andWhere(['<=', 'date_send', $date->format('Y-m-d H:i:59')]);

        /** @var Reminder $reminder */
        foreach ($queryReminder->each() as $reminder) {
            try {
                Yii::$app->reminderComponent->send($reminder);
            } catch (Throwable $e) {
                Yii::error([$e->getMessage(), $e->getLine()]);
            }
        }
    }
}
