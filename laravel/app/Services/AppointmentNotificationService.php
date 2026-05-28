<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AppointmentNotificationStatus;
use App\Models\Appointment;
use App\Notifications\NotificationChannelFactory;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use Illuminate\Support\Facades\Log;

final class AppointmentNotificationService
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointmentRepository,
        private readonly IdempotencyService $idempotencyService,
        private readonly NotificationChannelFactory $notificationChannelFactory,
    ) {}

    public function send(int $appointmentId): void
    {
        $idempotencyKey = $this->idempotencyKeyFor($appointmentId);

        if (! $this->idempotencyService->acquire($idempotencyKey)) {
            Log::info('Duplicate appointment notification job skipped', [
                'appointment_id' => $appointmentId,
                'idempotency_key' => $idempotencyKey,
            ]);

            return;
        }

        $appointment = $this->findWithClient($appointmentId);

        if ($appointment === null) {
            $this->idempotencyService->release($idempotencyKey);

            return;
        }

        if ($appointment->notification_status !== AppointmentNotificationStatus::Pending) {
            $this->idempotencyService->release($idempotencyKey);

            return;
        }

        if ($appointment->notified_at !== null) {
            $this->idempotencyService->release($idempotencyKey);

            return;
        }

        if ($appointment->scheduled_at->isPast()) {
            $this->idempotencyService->release($idempotencyKey);

            return;
        }

        if (! filled($appointment->client?->contactFor($appointment->notification_method))) {
            $this->idempotencyService->release($idempotencyKey);

            return;
        }

        $sent = $this->notificationChannelFactory
            ->make($appointment->notification_method)
            ->send($appointment);

        if (! $sent) {
            $this->idempotencyService->release($idempotencyKey);
        }

        $this->updateStatus(
            $appointment,
            $sent ? AppointmentNotificationStatus::Sent : AppointmentNotificationStatus::Failed,
        );
    }

    public function markFailedAndRelease(int $appointmentId): void
    {
        $this->idempotencyService->release(
            $this->idempotencyKeyFor($appointmentId),
        );

        $appointment = $this->findWithClient($appointmentId);

        if ($appointment === null) {
            return;
        }

        $this->updateStatus($appointment, AppointmentNotificationStatus::Failed);
    }

    private function findWithClient(int $id): ?Appointment
    {
        return $this->appointmentRepository->findById($id)?->load('client');
    }

    private function updateStatus(Appointment $appointment, AppointmentNotificationStatus $status): Appointment
    {
        $update = [
            'notification_status' => $status->value,
        ];

        if ($status === AppointmentNotificationStatus::Sent) {
            $update['notified_at'] = now();
        }

        return $this->appointmentRepository->update($appointment, $update);
    }

    private function idempotencyKeyFor(int $appointmentId): string
    {
        return "appointment-notification:{$appointmentId}";
    }
}
