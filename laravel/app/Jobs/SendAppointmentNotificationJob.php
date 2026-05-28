<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\AppointmentNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

final class SendAppointmentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;

    public function __construct(
        public readonly int $appointmentId,
    ) {}

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [10, 30, 60];
    }

    public function handle(AppointmentNotificationService $notificationService): void
    {
        $notificationService->send($this->appointmentId);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendAppointmentNotificationJob failed', [
            'appointment_id' => $this->appointmentId,
            'error' => $exception->getMessage(),
        ]);

        app(AppointmentNotificationService::class)->markFailedAndRelease($this->appointmentId);
    }
}
