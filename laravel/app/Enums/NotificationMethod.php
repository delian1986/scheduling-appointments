<?php

declare(strict_types=1);

namespace App\Enums;

enum NotificationMethod: string
{
    case Email = 'email';
    case Sms = 'sms';
}
