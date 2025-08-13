<?php

use app\components\Perm;
use app\models\Distributor;
use app\modules\reminder\models\ReminderTemplate;
use app\modules\reminder\models\ReminderTemplateSearch;
use app\widgets\Select;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var ReminderTemplateSearch $model
 * @var ActiveForm $form
 */

$distributorList = Distributor::getList();
?>
<section class="item item--action-panel">
    <div class="section-filters-btn">
        <?= Html::a(
            '<span class="svg--icon svg--glyphicons-basic-filter" data-grunticon-embed></span>',
            '#',
            ['class' => 'btn btn-sm btn-success js-filter']
        ) ?>
        <?php if (Yii::$app->user->can(Perm::REMINDER_TEMPLATE_CREATE)): ?>
            <?= Html::a(Yii::t('app', 'Новый шаблон'), ['create'], ['class' => 'btn btn-sm btn-primary js-show-modal']) ?>
        <?php endif; ?>
    </div>
    <div class="section-filters js-section-filters" style="display: none;">
        <?php $form = ActiveForm::begin(
            [
                'action' => ['index'],
                'method' => 'get',
                'options' => ['class' => 'js-filter-form'],
            ]
        ); ?>
        <div class="section-filters-body">
            <div class="section-filters-body-row css-xs-padding">
                <h4><?= Yii::t('app', 'Общие данные') ?></h4>
                <div class="css-xs-padding row">
                    <div class="col-sm-3">
                        <?= $form->field($model, 'context')->widget(
                            Select::class,
                            [
                                'items' => $model->getAvailableContextsNameList(),
                                'options' => [
                                    'class' => 'form-control input-sm',
                                    'data-style' => 'btn-sm btn-default',
                                    'multiple' => true,
                                ],
                            ]
                        ); ?>
                    </div>
                    <?php if (count($distributorList) > 1): ?>
                        <div class="col-sm-3">
                            <?= $form->field($model, 'distributorList')->widget(
                                Select::class,
                                [
                                    'options' => [
                                        'class' => 'form-control input-sm',
                                        'data-style' => 'btn-sm btn-default',
                                        'multiple' => true,
                                    ],
                                    'items' => $distributorList,
                                ]
                            ) ?>
                        </div>
                    <?php endif; ?>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'channel_type')->widget(
                            Select::class,
                            [
                                'options' => [
                                    'class' => 'form-control',
                                    'data-style' => 'btn-sm btn-default',
                                    'prompt' => Yii::t('app', 'Не указано'),
                                ],
                                'items' => ReminderTemplate::channelTypeList(),
                            ]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="section-filters-footer clearfix">
            <?= Html::submitButton(Yii::t('app', 'Найти'), ['class' => 'btn btn-sm btn-primary pull-left']) ?>
            <?= Html::a(Yii::t('app', 'Сбросить'), ['index'], ['class' => 'btn btn-sm btn-default pull-left']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</section>
