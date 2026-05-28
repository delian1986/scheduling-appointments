<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Enums\NotificationMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexAppointmentRequest;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Services\AppointmentService;
use App\Services\NotificationMessageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

final class AppointmentController extends Controller
{
    public function index(IndexAppointmentRequest $request, AppointmentService $appointmentService): View
    {
        $validated = $request->validated();

        [$startDate, $startTime] = $this->splitDateTime($validated['start_date'] ?? null);
        [$endDate, $endTime]     = $this->splitDateTime($validated['end_date'] ?? null);

        return view('appointments.index', [
            'appointments' => $appointmentService->paginate($request->filters()),
            'filters' => [
                'start_date_date' => $startDate,
                'start_date_time' => $startTime,
                'end_date_date'   => $endDate,
                'end_date_time'   => $endTime,
                'ucn'             => $validated['ucn'] ?? null,
            ],
        ]);
    }

    public function show(Appointment $appointment, AppointmentService $appointmentService): View
    {
        $appointment->load('client');

        $futureAppointments = $appointmentService->paginateFutureForClient($appointment);

        return view('appointments.show', [
            'appointment' => $appointment,
            'futureAppointments' => $futureAppointments,
        ]);
    }

    public function create(): View
    {
        return view('appointments.add', [
            'notificationMethods' => NotificationMethod::cases(),
        ]);
    }

    public function store(
        StoreAppointmentRequest $request,
        AppointmentService $appointmentService,
        NotificationMessageService $notificationMessageService,
    ): RedirectResponse {
        $validated = $request->validated();

        try {
            $appointmentService->create($validated);
        } catch (\Throwable $e) {
            Log::error('Appointment creation failed', [
                'error' => $e->getMessage(),
                'ucn' => $validated['ucn'] ?? null,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Възникна грешка при запазване на часа. Опитайте отново.');
        }

        return redirect()
            ->route('appointments.index')
            ->with('success', $notificationMessageService->successMessage($validated['notification_method']));
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function splitDateTime(?string $dateTime): array
    {
        if ($dateTime === null || $dateTime === '') {
            return [null, null];
        }

        [$date, $time] = array_pad(explode(' ', $dateTime, 2), 2, null);

        if ($time !== null) {
            $time = substr($time, 0, 5);
        }

        return [$date, $time];
    }
}
