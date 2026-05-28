<?php

declare(strict_types=1);

namespace App\Enums;

enum AppointmentNotificationStatus: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Failed = 'failed';
}
