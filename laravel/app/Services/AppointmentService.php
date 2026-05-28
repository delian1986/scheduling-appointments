<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\NotificationMethod;
use App\Models\Appointment;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Support\Facades\DB;

final class AppointmentService
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly AppointmentRepositoryInterface $appointmentRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public function create(array $validated): Appointment
    {
        return DB::transaction(function () use ($validated): Appointment {
            $clientData = [
                'full_name' => $validated['full_name'],
                'ucn' => $validated['ucn'],
            ];

            if (! empty($validated['email'])) {
                $clientData['email'] = $validated['email'];
            }

            if (! empty($validated['phone'])) {
                $clientData['phone'] = $validated['phone'];
            }

            $existing = $this->clientRepository->findByUcn($validated['ucn']);

            $client = $existing !== null
                ? $this->clientRepository->update($existing, $clientData)
                : $this->clientRepository->create($clientData);

            return $this->appointmentRepository->create([
                'client_id' => $client->id,
                'description' => $validated['description'] ?? null,
                'scheduled_at' => $validated['scheduled_at'],
                'notification_method' => $validated['notification_method'] instanceof NotificationMethod
                    ? $validated['notification_method']->value
                    : $validated['notification_method'],
                'notification_status' => 'pending',
            ])->load('client');
        });
    }
}
