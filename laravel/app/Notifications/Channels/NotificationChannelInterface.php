<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use App\Models\Appointment;

interface NotificationChannelInterface
{
    public function send(Appointment $appointment): bool;
}
