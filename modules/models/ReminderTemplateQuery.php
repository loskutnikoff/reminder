<?php

namespace app\modules\reminder\models;

use app\models\query\ActiveQuery;

class ReminderTemplateQuery extends ActiveQuery
{
    public function byDistributor(array $distributorIds): ReminderTemplateQuery
    {
        return $this->andWhere(
            [
                'EXISTS',
                ReminderTemplateDistributor::find()
                    ->alias('td')
                    ->andWhere(['td.distributor_id' => $distributorIds])
                    ->andWhere($this->alias . '.id = td.template_id'),
            ]
        );
    }
}
