<?php

namespace app\modules\reminder\models;

use app\components\behaviors\TimestampBehavior;
use app\models\User;
use app\modules\contacts\models\Contact;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use Yii;

/**
 * @property int $object_id
 * @property int $object_type
 * @property int $channel_type
 * @property string $date_send
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property-read User $createdBy
 * @property-read User $updatedBy
 */
class Reminder extends ActiveRecord
{
    public const OBJECT_TYPE_CONTACT = 1;

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'dsf_reminder';
    }

    public function rules(): array
    {
        return [
            [['object_id', 'object_type', 'channel_type', 'date_send'], 'required'],
            ['channel_type', 'in', 'range' => array_keys(ReminderTemplate::channelTypeList())],
            ['date_send', 'date', 'format' => 'yyyy-MM-dd HH:mm:ss'],
            ['object_type', 'in', 'range' => array_keys(self::objectTypeList())],
        ];
    }

    public function attributeLabels()
    {
        return [
            'object_id' => Yii::t('app', 'ID сущности'),
            'object_type' => Yii::t('app', 'Тип сущности'),
            'channel_type' => Yii::t('app', 'Канал'),
            'date_send' => Yii::t('app', 'Дата запланированной отправки'),
            'created_by' => Yii::t('app', 'Автор'),
            'updated_by' => Yii::t('app', 'Изменил'),
            'created_at' => Yii::t('app', 'Дата создания'),
            'updated_at' => Yii::t('app', 'Дата изменения'),
        ];
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ],
            BlameableBehavior::class => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
        ];
    }

    public static function objectTypeList(): array
    {
        return [
            self::OBJECT_TYPE_CONTACT => Yii::t('app', 'Контакт'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCreatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUpdatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public function getObject()
    {
        /** @var Contact $model */
        $model = match ($this->object_type) {
            self::OBJECT_TYPE_CONTACT => Contact::class,
        };

        return $model::find()->andWhere(['id' => $this->object_id])->one();
    }
}
