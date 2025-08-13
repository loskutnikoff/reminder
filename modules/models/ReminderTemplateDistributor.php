<?php

namespace app\modules\reminder\models;

use app\models\Distributor;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $template_id
 * @property int $distributor_id
 * @property-read ReminderTemplate $reminderTemplate
 * @property-read Distributor $distributor
 */
class ReminderTemplateDistributor extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'dsf_reminder_template_distributor';
    }

    public function rules(): array
    {
        return [
            [['template_id', 'distributor_id'], 'required'],
            ['distributor_id', 'unique', 'targetAttribute' => ['template_id', 'distributor_id']],
            ['distributor_id', 'exist', 'targetRelation' => 'distributor'],
            ['template_id', 'exist', 'targetRelation' => 'reminderTemplate'],
        ];
    }

    public function getReminderTemplate(): ActiveQuery
    {
        return $this->hasOne(ReminderTemplate::class, ['id' => 'template_id']);
    }

    public function getDistributor(): ActiveQuery
    {
        return $this->hasOne(Distributor::class, ['id' => 'distributor_id']);
    }
}
