<?php

namespace app\modules\reminder\widgets;

use app\components\BaseFormModel;
use app\modules\reminder\models\Reminder;
use app\modules\reminder\models\ReminderTemplate;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\bootstrap\ActiveForm;
use Yii;

class ReminderWidget extends Widget
{
    /** @var BaseFormModel */
    public $model;

    /** @var ActiveForm */
    public $form;

    public function init()
    {
        if (!$this->model) {
            throw new InvalidConfigException('Не указана модель');
        }

        if (!$this->form || !($this->form instanceof ActiveForm)) {
            throw new InvalidConfigException('Не указана форма');
        }

        parent::init();
    }

    public function run()
    {
        $templatesQuery = Yii::$app->reminderComponent->getTemplateQuery(new Reminder(), $this->model);
        if (!$templatesQuery->exists()) {
            return '';
        }

        $channelTypes = $templatesQuery->select(['channel_type'])->distinct(true)->createCommand()->queryColumn();

        return $this->render('reminder-partial', [
            'model' => $this->model,
            'form' => $this->form,
            'channelTypesList' => array_intersect_key(ReminderTemplate::channelTypeList(), array_flip($channelTypes))
        ]);
    }
}
