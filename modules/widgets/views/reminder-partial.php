<?php

use app\modules\contacts\forms\BaseContactForm;
use app\modules\contacts\forms\ContactCreateForm;
use app\modules\reminder\models\ReminderTemplate;
use app\widgets\Select;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $model ContactCreateForm
 * @var $form ActiveForm
 * @var $this View
 * @var array $channelTypesList
 */

?>
<?= $form->field($model, 'remind')->checkbox(['class' => 'js-remind']) ?>
<div class="js-wrap-remind" style="display: none">
    <?= $form->field($model, 'channelType')->widget(
        Select::class,
        [
            'options' => [
                'class' => 'form-control',
                'data-style' => 'btn-sm btn-default',
            ],
            'items' => $channelTypesList
        ]
    ) ?>
    <?= $form->field($model, 'remindTime')->radioList(BaseContactForm::remindTimeList(), [
        'class' => 'd-flex',
        'item' => function ($index, $label, $name, $checked, $value) use ($model) {
            if (!$model->remindTime && $index == 0) {
                $checked = true;
            }
            $options = [
                'label' => null,
                'value' => $value,
                'id' => $id = Html::getInputId($model, 'remindTime') . "-$value",
            ];

            return '<div class="radio-item mr-10">'
                . Html::radio($name, $checked, $options)
                . Html::label($label, $id, ['class' => 'radio'])
                . '</div>';
        }
    ])->label(false) ?>
</div>