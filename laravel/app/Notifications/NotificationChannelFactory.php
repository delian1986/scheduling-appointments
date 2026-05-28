<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\NotificationMethod;
use App\Notifications\Channels\EmailNotificationChannel;
use App\Notifications\Channels\NotificationChannelInterface;
use App\Notifications\Channels\SmsNotificationChannel;

final class NotificationChannelFactory
{
    public function make(NotificationMethod $method): NotificationChannelInterface
    {
        return match ($method) {
            NotificationMethod::Email => app(EmailNotificationChannel::class),
            NotificationMethod::Sms => app(SmsNotificationChannel::class),
        };
    }
}
