<?php

namespace app\modules\reminder\components;

use app\helpers\ArrayHelper;
use app\models\User;
use app\modules\contacts\models\Contact;
use app\modules\reminder\components\channels\ChannelInterface;
use app\modules\reminder\components\channels\EmailChannel;
use app\modules\reminder\components\channels\TelegramChannel;
use app\modules\reminder\components\contexts\ContextInterface;
use app\modules\reminder\models\Reminder;
use app\modules\reminder\models\ReminderTemplate;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\db\Query;
use yii\di\Instance;

/**
 * Компонент для напоминаний
 *
 * @property-read array $contextNameList
 * @property-read array $contextAliasList
 */
class ReminderComponent extends Component
{
    /** @var array */
    public $contexts = [];

    /** @var array  */
    public $channels = [];

    /** @var ContextInterface[] */
    private $contextInstancesByClassName = [];

    /** @var ContextInterface[] */
    private $contextInstancesByAlias = [];

    /** @var ChannelInterface[] */
    private $channelInstances = [];

    /**
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();

        if (!is_array($this->contexts) || empty($this->contexts)) {
            throw new InvalidConfigException('Не задан массив contexts');
        }

        if (!is_array($this->channels) || empty($this->channels)) {
            throw new InvalidConfigException('Не задан массив channels');
        }

        foreach ($this->contexts as $contextClass) {
            /** @var ContextInterface $context */
            $context = Instance::ensure($contextClass, ContextInterface::class);
            $this->contextInstancesByAlias[$context->getAlias()] = $context;
            $this->contextInstancesByClassName[$context->getItemClass()] = $context;
        }

        foreach ($this->channels as $channelClass) {
            /** @var ChannelInterface $channel */
            $channel = Instance::ensure($channelClass, ChannelInterface::class);
            $this->channelInstances[$channel->getChannelType()] = $channel;
        }
    }

    /**
     *
     * @throws InvalidConfigException
     */
    public function getContext($object): ContextInterface
    {
        $objectClassName = get_class($object);
        if (array_key_exists($objectClassName, $this->contextInstancesByClassName)) {
            return $this->contextInstancesByClassName[$objectClassName];
        }
        foreach ($this->contexts as $contextClassName) {
            /** @var ContextInterface $context */
            $context = Instance::ensure($contextClassName, ContextInterface::class);
            $itemClass = $context->getItemClass();
            if ($object instanceof $itemClass) {
                $this->contextInstancesByClassName[$objectClassName] = $context;
                $this->contextInstancesByAlias[$context->getAlias()] = $context;

                return $context;
            }
        }
        throw new InvalidConfigException("Не найден контекст для модели $objectClassName");
    }

    /**
     * @throws InvalidConfigException
     */
    public function getContextByAlias(string $alias): ContextInterface
    {
        $context = $this->contextInstancesByAlias[$alias] ?? null;
        if (!$context) {
            throw new InvalidConfigException("Не найден контекст с алиасом $alias");
        }

        return $context;
    }

    public function getContextAliasList(): array
    {
        return array_keys($this->contextInstancesByAlias);
    }

    public function getContextNameList(): array
    {
        return array_map(
            static function (ContextInterface $context) {
                return $context->name;
            },
            $this->contextInstancesByAlias
        );
    }

    /**
     * @param Reminder $reminder
     * @throws Exception
     * @throws InvalidConfigException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function send(Reminder $reminder)
    {
        /** @var Contact $object */
        $object = $reminder->getObject();

        if (!$object) {
            throw new Exception('Не найдена сущность напоминания');
        }

        /** @var User $recipient */
        $recipient = $object->getRecipient();

        if (!$recipient) {
            throw new Exception('Не найден получатель');
        }

        /** @var ReminderTemplate $template */
        $template = $this->getTemplateQuery($reminder, $object)->one();

        if (!$template) {
            return;
            //throw new Exception('Не найден шаблон отправки');
        }

        $channel = ArrayHelper::getValue($this->channelInstances, $reminder->channel_type);
        if (!$channel) {
            throw new Exception('Не найден канал отправки');
        } elseif ($channel instanceof EmailChannel && !$recipient->email) {
            throw new Exception('Отправка не удалась, у пользователя нет Email');
        } elseif ($channel instanceof TelegramChannel && !$recipient->tg_ext_id) {
            throw new Exception('Отправка не удалась, пользователь не подключен к Telegram');
        }

        $message = $this->getMessageByContext($template, $object);

        $channel->sendRemind($recipient, $message, $object->getSubject());

        $reminder->delete();
    }

    public function getTemplateQuery(Reminder $reminder, $object): Query
    {
        $query = ReminderTemplate::find()
            ->andWhere(['context' => $object->getContext()])
            ->andFilterWhere(['channel_type' => $reminder->channel_type]);

        if ($distributorId = $object->getDistributorId()) {
            $query->byDistributor((array)$distributorId);
        }
        return $query;
    }

    /**
     * @throws InvalidConfigException
     */
    private function getMessageByContext(ReminderTemplate $template, $object): string
    {
        $context = $template->contextInstance;
        $contextItem = $context->getItemById($object->getEntityId());
        return $template->fillByContextItem($contextItem, $object);
    }
}