<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\NotificationMethod;

final class NotificationMessageService
{
    public function successMessage(NotificationMethod|string $method): string
    {
        $value = $method instanceof NotificationMethod ? $method->value : $method;

        return match ($value) {
            NotificationMethod::Email->value => 'Успешно запазихте час! Клиентът ще бъде уведомен чрез Email.',
            NotificationMethod::Sms->value => 'Успешно запазихте час! Клиентът ще бъде уведомен чрез SMS.',
        };
    }
}
