<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NotificationMethod;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'client_id',
    'description',
    'scheduled_at',
    'notification_method',
    'notification_status',
    'notified_at',
])]
final class Appointment extends Model
{
    use SoftDeletes;

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'notified_at' => 'datetime',
            'notification_method' => NotificationMethod::class,
        ];
    }
}
