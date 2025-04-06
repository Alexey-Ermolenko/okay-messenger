<?php

declare(strict_types=1);

namespace App\Enum;

enum NotificationPreference: string
{
    case Email = 'email';
    case Telegram = 'telegram';
    case Phone = 'phone';
}
