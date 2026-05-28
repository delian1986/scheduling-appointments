<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Enums\NotificationMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Services\AppointmentService;
use App\Services\NotificationMessageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

final class AppointmentController extends Controller
{
    public function index(): View
    {
        return view('appointments.index');
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
}
