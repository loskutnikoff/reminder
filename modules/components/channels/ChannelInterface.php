<?php

namespace app\modules\reminder\components\channels;

use app\models\User;

interface ChannelInterface
{
    public function getChannelType(): int;

    public function sendRemind(User $user, string $message, string $subject): bool;
}
