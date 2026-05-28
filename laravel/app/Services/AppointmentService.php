<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AppointmentNotificationStatus;
use App\Enums\NotificationMethod;
use App\Jobs\SendAppointmentNotificationJob;
use App\Models\Appointment;
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

    public function paginate(int $perPage = 5): LengthAwarePaginator
    {
        return $this->appointmentRepository->paginate($perPage);
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

            $appointment = $this->appointmentRepository->create([
                'client_id' => $client->id,
                'description' => $validated['description'] ?? null,
                'scheduled_at' => $validated['scheduled_at'],
                'notification_method' => $validated['notification_method'] instanceof NotificationMethod
                    ? $validated['notification_method']->value
                    : $validated['notification_method'],
                'notification_status' => AppointmentNotificationStatus::Pending->value,
            ]);

            SendAppointmentNotificationJob::dispatch($appointment->id)->afterCommit();

            return $appointment->load('client');
        });
    }
}
