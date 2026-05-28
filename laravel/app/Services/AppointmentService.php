<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AppointmentNotificationStatus;
use App\Enums\NotificationMethod;
use App\Jobs\SendAppointmentNotificationJob;
use App\Models\Appointment;
use App\Models\Client;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class AppointmentService
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly AppointmentRepositoryInterface $appointmentRepository,
    ) {}

    /**
     * @param  array<string, string>  $filters
     */
    public function paginate(array $filters = [], int $perPage = 5): LengthAwarePaginator
    {
        return $this->appointmentRepository->paginate($filters, $perPage);
    }

    public function paginateFutureForClient(Appointment $appointment, int $perPage = 5): LengthAwarePaginator
    {
        $appointment->loadMissing('client');

        return $this->appointmentRepository->paginate([
            'ucn' => $appointment->client->ucn,
            'start_date' => now()->toDateTimeString(),
            'direction' => 'asc',
            'exclude_id' => $appointment->id,
        ], $perPage);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function create(array $validated): Appointment
    {
        /**
         * TODO:: before saving to database most likely we'll need to apply additional logic 
         * like validating the client, checking if the appointment is already scheduled, etc.
         * how many appointments can we have in given time window
         */

        return DB::transaction(function () use ($validated): Appointment {
            $client = $this->upsertClient($validated);

            $appointment = $this->appointmentRepository->create(
                $this->appointmentAttributes($validated, $client->id),
            );

            return $this->finalizeAndNotify($appointment);
        });
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function update(Appointment $appointment, array $validated): Appointment
    {
        return DB::transaction(function () use ($appointment, $validated): Appointment {
            $client = $this->upsertClient($validated);

            $appointment = $this->appointmentRepository->update($appointment, [
                ...$this->appointmentAttributes($validated, $client->id),
                'notified_at' => null,
            ]);

            return $this->finalizeAndNotify($appointment);
        });
    }

    public function delete(Appointment $appointment): bool
    {
        return $this->appointmentRepository->delete($appointment);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function upsertClient(array $validated): Client
    {
        $clientData = [
            'full_name' => $validated['full_name'],
        ];

        // Only forward optional contact fields when present, so existing
        // values for a returning client are not clobbered with NULL.
        if (! empty($validated['email'])) {
            $clientData['email'] = $validated['email'];
        }

        if (! empty($validated['phone'])) {
            $clientData['phone'] = $validated['phone'];
        }

        return $this->clientRepository->upsertByUcn($validated['ucn'], $clientData);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function appointmentAttributes(array $validated, int $clientId): array
    {
        return [
            'client_id' => $clientId,
            'description' => $validated['description'] ?? null,
            'scheduled_at' => $validated['scheduled_at'],
            'notification_method' => $validated['notification_method'] instanceof NotificationMethod
                ? $validated['notification_method']->value
                : $validated['notification_method'],
            'notification_status' => AppointmentNotificationStatus::Pending->value,
        ];
    }

    private function finalizeAndNotify(Appointment $appointment): Appointment
    {
        SendAppointmentNotificationJob::dispatch($appointment->id)->afterCommit();

        return $appointment->load('client');
    }
}
