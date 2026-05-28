<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Appointment;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function findById(int $id): ?Appointment
    {
        return Appointment::query()->find($id);
    }

    public function create(array $attributes): Appointment
    {
        return Appointment::query()->create($attributes);
    }

    public function update(Appointment $appointment, array $attributes): Appointment
    {
        $appointment->update($attributes);

        return $appointment->refresh();
    }

    public function delete(Appointment $appointment): bool
    {
        return (bool) $appointment->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Appointment::query()
            ->with('client')
            ->when($filters['start_date'] ?? null, fn ($query, $value) => $query->where('scheduled_at', '>=', $value))
            ->when($filters['end_date'] ?? null, fn ($query, $value) => $query->where('scheduled_at', '<=', $value))
            ->when($filters['ucn'] ?? null, fn ($query, $value) => $query->whereHas('client', fn ($clientQuery) => $clientQuery->where('ucn', $value)))
            ->orderByDesc('scheduled_at')
            ->paginate($perPage)
            ->withQueryString();
    }
}
