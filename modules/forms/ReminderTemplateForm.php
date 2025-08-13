<?php

namespace app\modules\reminder\forms;

use app\models\Distributor;
use app\modules\reminder\models\ReminderTemplateDistributor;
use app\modules\reminder\models\ReminderTemplate;
use Yii;

class ReminderTemplateForm extends ReminderTemplate
{
    public $distributorId;

    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            'distributorId' => Yii::t('app', 'Дистрибьюторы'),
        ]);
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            ['distributorId', 'required'],
            ['distributorId', 'each', 'rule' => ['integer']],
            ['distributorId', 'exist', 'targetClass' => Distributor::class, 'targetAttribute' => 'id', 'allowArray' => true],
            ['distributorId', 'uniqueValidate'],
        ]);
    }

    public function uniqueValidate($attribute, $params)
    {
        $query = self::find()
            ->joinWith('distributors', false, 'INNER JOIN')
            ->andWhere([
                'context' => $this->context,
                'channel_type' => $this->channel_type,
                'distributor_id' => $this->distributorId
            ]);

        if ($this->isNewRecord && (clone $query)->exists()) {
            $this->addError($attribute, Yii::t('app', 'Шаблон с параметрами дистрибьютор, контекст и канал уже существует'));
        }

        if (!$this->isNewRecord && (clone $query)->andWhere(['<>', self::tableName() . '.id', $this->id])->exists()) {
            $this->addError($attribute, Yii::t('app', 'Шаблон с параметрами дистрибьютор, контекст и канал уже существует'));
        }

    }

    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_INSERT | self::OP_UPDATE | self::OP_DELETE,
        ];
    }

    public function afterFind(): void
    {
        parent::afterFind();

        $this->distributorId = array_column($this->reminderTemplateDistributors, 'distributor_id');
    }

    public function beforeDelete(): bool
    {
        ReminderTemplateDistributor::deleteAll(['template_id' => $this->id]);
        return parent::beforeDelete();
    }

    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);
        ReminderTemplateDistributor::deleteAll(['template_id' => $this->id]);

        $rows = array_map(fn($distributorId) => [$this->id, $distributorId], (array) $this->distributorId);
        Yii::$app->db->createCommand()->batchInsert(ReminderTemplateDistributor::tableName(), ['template_id', 'distributor_id'], $rows)->execute();
    }
}
