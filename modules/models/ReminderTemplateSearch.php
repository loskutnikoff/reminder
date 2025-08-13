<?php

namespace app\modules\reminder\models;

use app\helpers\Helper;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 *
 * @property string $context
 * @property string $modelClass
 */
class ReminderTemplateSearch extends ReminderTemplate
{
    public $distributorList;

    public function attributeLabels(): array
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'distributorList' => Yii::t('app', 'Дистрибьютор'),
            ]
        );
    }

    public function rules(): array
    {
        return [
            [
                [
                    'context',
                    'distributorList',
                    'channel_type',
                ],
                'safe',
            ],
        ];
    }

    public function formName(): string
    {
        return '';
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $this->load($params);
        $query = ReminderTemplate::find();

        $query->innerJoin(
            ['ntd' => ReminderTemplateDistributor::tableName()],
            'ntd.template_id = ' . ReminderTemplate::tableName() . '.id'
        );

        $query
            ->andFilterWhere(['context' => $this->context])
            ->andFilterWhere(['td.distributor_id' => $this->distributorList])
            ->andFilterWhere(['channel_type' => $this->channel_type]);

        $query->andWhere(['ntd.distributor_id' => Helper::getUserDistributorIds()]);

        return new ActiveDataProvider(
            [
                'query' => $query,
                'sort' => [
                    'defaultOrder' => [
                        'id' => ['default' => SORT_DESC],
                    ],
                ],
            ]
        );
    }
}
