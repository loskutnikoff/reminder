<?php

namespace app\modules\reminder\components\contexts;

interface ContextInterface
{
    public function getName(): string;

    public function getAlias(): string;

    public function getItemClass(): string;

    public function getItemId($contextItem);

    public function getItemById($contextItemID);

    public function placeholders($object = null): array;

    public function render(string $template, $contextItem, $object = null): string;
}
