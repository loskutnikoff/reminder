<?php

namespace app\modules\reminder\models;

use app\components\behaviors\TimestampBehavior;
use app\modules\reminder\components\contexts\ContextInterface;
use app\models\Distributor;
use app\models\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Класс для шаблонов Уведомлений
 *
 * @property int $id
 * @property string $name
 * @property int $channel_type
 * @property string $context
 * @property string $message
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ContextInterface $contextInstance
 * @property string $contextName
 * @property-read User $createdBy
 * @property-read User $updatedBy
 * @property-read ReminderTemplateDistributor[] $reminderTemplateDistributors
 * @property-read Distributor[] $distributors
 */
class ReminderTemplate extends ActiveRecord
{
    public const CHANNEL_TYPE_EMAIL = 1;
    public const CHANNEL_TYPE_TG = 2;

    public static function tableName(): string
    {
        return 'dsf_reminder_template';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['name', 'context', 'message'], 'required'],
            [['context', 'message'], 'string'],
            [['name'], 'string', 'max' => 120],
            ['context', 'in', 'range' => $this->getAvailableContexts()],
            ['channel_type', 'in', 'range' => array_keys(self::channelTypeList())],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Название'),
            'channel_type' => Yii::t('app', 'Канал'),
            'context' => Yii::t('app', 'Контекст'),
            'contextName' => Yii::t('app', 'Контекст'),
            'message' => Yii::t('app', 'Сообщение'),
            'created_by' => Yii::t('app', 'Автор'),
            'updated_by' => Yii::t('app', 'Изменил'),
            'created_at' => Yii::t('app', 'Дата создания'),
            'updated_at' => Yii::t('app', 'Дата изменения'),
        ];
    }

    public static function channelTypeList(): array
    {
        return [
            self::CHANNEL_TYPE_EMAIL => 'Email',
            self::CHANNEL_TYPE_TG => 'Telegram',
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

    /**
     * @return ReminderTemplateQuery
     */
    public static function find(): ReminderTemplateQuery
    {
        return new ReminderTemplateQuery(static::class);
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

    /**
     * @return string
     */
    public function getContextName(): string
    {
        return Yii::$app->reminderComponent->getContextNameList()[$this->context];
    }

    /**
     * @return ContextInterface
     * @throws InvalidConfigException
     */
    public function getContextInstance(): ContextInterface
    {
        return Yii::$app->reminderComponent->getContextByAlias($this->context);
    }

    /**
     * @param $contextItem
     * @param $object
     * @return string
     * @throws InvalidConfigException
     */
    public function fillByContextItem($contextItem, $object): string
    {
        return $this->getContextInstance()->render($this->message, $contextItem, $object);
    }

    /**
     * @param ContextInterface|null $context
     * @param string|null           $alias
     * @return array
     */
    public static function getListByContext(?ContextInterface $context = null, ?string $alias = null): array
    {
        return ArrayHelper::map(
            self::find()
                ->andWhere(['context' => $context->alias ?? $alias])
                ->all(),
            'id',
            'name'
        );
    }

    /**
     * @param string|null $contextAlias
     * @param int[] $distributorId
     * @return array
     */
    public static function getListByContextAlias($contextAlias = null, array $distributorId = []): array
    {
        $query = self::find()
            ->select(['name', 'id'])
            ->andFilterWhere(['context' => $contextAlias])
            ->indexBy('id');
        if ($distributorId) {
            $query->andWhere(
                [
                    'EXISTS',
                    ReminderTemplateDistributor::find()
                        ->andWhere(['distributor_id' => $distributorId])
                        ->andWhere(static::tableName() . '.id = ' . ReminderTemplateDistributor::tableName() . '.template_id')
                ]
            );
        }
        return $query->column();
    }

    public function getAvailableContextsNameList(): array
    {
        return array_intersect_key(Yii::$app->reminderComponent->getContextNameList(), array_flip($this->getAvailableContexts()));
    }

    protected function getAvailableContexts(): array
    {
        return Yii::$app->reminderComponent->getContextAliasList();
    }

    public function getReminderTemplateDistributors(): ActiveQuery
    {
        return $this->hasMany(ReminderTemplateDistributor::class, ['template_id' => 'id']);
    }

    public function getDistributors(): ActiveQuery
    {
        return $this->hasMany(Distributor::class, ['id' => 'distributor_id'])
            ->via('reminderTemplateDistributors');
    }
}
