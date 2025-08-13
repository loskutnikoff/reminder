<?php
namespace app\modules\reminder\components;

use app\models\User;

interface ReminderInterface
{
    public function getDistributorId(): ?int;

    public function getContext(): string;

    public function getRecipient(): ?User;

    public function getSubject(): string;
}