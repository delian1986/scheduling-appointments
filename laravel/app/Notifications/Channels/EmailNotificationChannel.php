<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use App\Models\Appointment;
use Illuminate\Support\Facades\Log;

final class EmailNotificationChannel implements NotificationChannelInterface
{
    public function send(Appointment $appointment): bool
    {
        Log::info('Email notification stub sent', [
            'appointment_id' => $appointment->id,
            'client_email' => $appointment->client?->email,
        ]);

        return true;
    }
}
