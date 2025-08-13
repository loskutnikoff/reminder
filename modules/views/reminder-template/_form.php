<?php

/** @noinspection PhpUnhandledExceptionInspection */

use app\components\Html;
use app\models\Distributor;
use app\modules\reminder\models\ReminderTemplate;
use app\widgets\ActiveForm;
use app\widgets\Select;
use yii\bootstrap\Modal;
use yii\web\View;

/**
 * @var View $this
 * @var ReminderTemplate $model
 */

$distributorList = Distributor::getList();
?>
<?php Modal::begin(
    [
        'closeButton' => false,
        'options' => [
            'class' => 'fade modal',
        ],
        'footer' =>
            Html::button(Yii::t('app', 'Закрыть'), ['class' => 'btn-default', 'data-dismiss' => 'modal']) .
            Html::button(
                $model->isNewRecord ? Yii::t('app', 'Добавить') : Yii::t('app', 'Сохранить'),
                ['class' => 'btn-primary js-submit']
            ),
    ]
); ?>
<?= Html::errorSummary($model, ['class' => 'alert alert-danger']) ?>
<?php $form = ActiveForm::begin(['options' => ['autocomplete' => 'off']]); ?>
<div class="row">
    <?php if (count($distributorList) > 1): ?>
        <div class="col-md-12 clearfix">
            <?= $form->field($model, 'distributorId')->widget(
                Select::class,
                [
                    'options' => [
                        'class' => 'form-control',
                        'data-style' => 'btn-default',
                        'multiple' => true,
                    ],
                    'items' => $distributorList,
                ]
            ) ?>
        </div>
    <?php else: ?>
        <?= $form->field($model, 'distributorId[]')->label(false)->hiddenInput(['value' => key($distributorList)]) ?>
    <?php endif; ?>
    <div class="col-md-12 clearfix">
        <?= $form->field($model, 'name')->textInput(['class' => 'form-control']) ?>
    </div>
    <div class="col-md-12">
        <?= $form->field($model, 'channel_type')->widget(
            Select::class,
            [
                'options' => [
                    'class' => 'form-control',
                    'data-style' => 'btn-default',
                ],
                'items' => ReminderTemplate::channelTypeList(),
            ]
        ) ?>
    </div>
    <div class="col-md-12">
        <?= $form->field($model, 'context')->widget(
            Select::class,
            [
                'options' => [
                    'class' => 'form-control js-modal-context',
                    'data-style' => 'btn-default',
                ],
                'items' => $model->getAvailableContextsNameList(),
            ]
        ) ?>
    </div>
    <div class="col-md-12">
        <?= $form->field($model, 'message')->summernote(['placeholders' => [], 'config' => ['height' => 200]], ['class' => ['js-message']]) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php Modal::end(); ?>
