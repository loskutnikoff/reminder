<?php

namespace app\modules\reminder\components\contexts;

use app\components\contextRenderer\ContextRelationTrait;
use app\components\contextRenderer\ContextRender;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\BaseActiveRecord;
use yii\di\Instance;

/**
 * @property-read string $itemClass
 * @property-read string $name
 * @property-read string $alias
 */
abstract class BaseContext extends Component implements ContextInterface
{
    use ContextRelationTrait;

    public const NAME = '';
    public const ALIAS = '';
    public const ITEM_CLASS = '';

    public function getName(): string
    {
        return Yii::t('app', static::NAME);
    }

    public function getAlias(): string
    {
        return static::ALIAS;
    }

    public function getItemClass(): string
    {
        return static::ITEM_CLASS;
    }

    public function getItemId($contextItem)
    {
        return $contextItem->id;
    }

    /**
     * @throws InvalidConfigException
     */
    public function getItemById($contextItemID): ?BaseActiveRecord
    {
        /** @var BaseActiveRecord $class */
        $class = Instance::ensure($this->getItemClass(), BaseActiveRecord::class);

        return $class::findOne($contextItemID);
    }

    public function render(string $template, $contextItem = null, $object = null): string
    {
        $renderer = new ContextRender($contextItem, $this->placeholders($object), ContextRender::MODE_DEFAULT);

        return $renderer->renderTemplate($template);
    }
}
