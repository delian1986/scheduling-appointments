<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Appointment
 */
final class AppointmentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'notification_method' => $this->notification_method?->value,
            'notification_status' => $this->notification_status,
            'client' => [
                'id' => $this->client?->id,
                'full_name' => $this->client?->full_name,
                'ucn' => $this->client?->ucn,
                'email' => $this->client?->email,
                'phone' => $this->client?->phone,
            ],
        ];
    }
}
