<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use App\Models\Appointment;
use Illuminate\Support\Facades\Log;

final class SmsNotificationChannel implements NotificationChannelInterface
{
    public function send(Appointment $appointment): bool
    {
        Log::info('SMS notification stub sent', [
            'appointment_id' => $appointment->id,
            'client_phone' => $appointment->client?->phone,
        ]);

        return true;
    }
}
