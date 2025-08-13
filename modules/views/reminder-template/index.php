<?php

/** @noinspection PhpUnhandledExceptionInspection */

use app\components\Perm;
use app\modules\reminder\models\ReminderTemplate;
use app\modules\reminder\models\ReminderTemplateSearch;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var ReminderTemplateSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = Yii::t('app', 'Шаблоны уведомлений');
$this->registerJs('DSF.ReminderTemplate(".js-show-modal");');
?>
<h2 class="page-title"><?= $this->title ?></h2>
<?= $this->render('_search', ['model' => $searchModel]) ?>
<section class="item">
    <div class="table-inner">
        <?= GridView::widget(
            [
                'tableOptions' => ['class' => 'table table-striped'],
                'dataProvider' => $dataProvider,
                'filterModel' => null,
                'options' => [
                    'class' => 'grid-view shifted-grid',
                ],
                'columns' => [
                    'id',
                    'name',
                    'contextName',
                    [
                        'attribute' => 'message',
                        'contentOptions' => ['style' => 'max-width: 500px; word-wrap: break-word;'],
                    ],
                    [
                        'header' => Yii::t('app', 'Дистрибьюторы'),
                        'value' => static function ($model) {
                            return implode(', ', array_column($model->distributors, 'name'));
                        },
                    ],
                    [
                        'attribute' => 'channel_type',
                        'value' => static function (ReminderTemplate $model) {
                            return ArrayHelper::getValue(ReminderTemplate::channelTypeList(), $model->channel_type);
                        },
                    ],
                    'createdBy.fullName:text:' . Yii::t('app', 'Автор'),
                    [
                        'attribute' => 'created_at',
                        'format' => ['date', 'php:d.m.Y H:i'],
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '<div style="float: right">{update}{delete}</div>',
                        'visibleButtons' => [
                            'update' => Yii::$app->user->can(Perm::REMINDER_TEMPLATE_UPDATE),
                            'delete' => Yii::$app->user->can(Perm::REMINDER_TEMPLATE_DELETE),
                        ],
                        'buttons' => [
                            'update' => static fn($url, $model) => Html::a(
                                '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                ['update', 'id' => $model->id],
                                ['title' => Yii::t('app', 'Редактировать'), 'class' => 'btn-sm js-show-modal']
                            ),
                            'delete' => static fn($url) => Html::a(
                                '<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                $url,
                                ['title' => Yii::t('app', 'Удалить'), 'class' => 'js-delete']
                            ),
                        ],
                    ],
                ],
            ]
        ) ?>
    </div>
</section>
