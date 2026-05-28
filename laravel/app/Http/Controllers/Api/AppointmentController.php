<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexAppointmentRequest;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Services\AppointmentService;
use App\Services\NotificationMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class AppointmentController extends Controller
{
    public function index(IndexAppointmentRequest $request, AppointmentService $appointmentService): AnonymousResourceCollection
    {
        return AppointmentResource::collection($appointmentService->paginate($request->filters()));
    }

    public function store(
        StoreAppointmentRequest $request,
        AppointmentService $appointmentService,
        NotificationMessageService $notificationMessageService,
    ): JsonResponse {
        $validated = $request->validated();

        $appointment = $appointmentService->create($validated);

        return response()->json([
            'message' => $notificationMessageService->successMessage($validated['notification_method']),
            'data' => AppointmentResource::make($appointment),
        ], 201);
    }

    public function update(
        UpdateAppointmentRequest $request,
        Appointment $appointment,
        AppointmentService $appointmentService,
        NotificationMessageService $notificationMessageService,
    ): JsonResponse {
        $validated = $request->validated();

        $appointment = $appointmentService->update($appointment, $validated);

        return response()->json([
            'message' => $notificationMessageService->successMessage($validated['notification_method']),
            'data' => AppointmentResource::make($appointment),
        ]);
    }
}
