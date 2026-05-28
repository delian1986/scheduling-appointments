<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Appointment;
use App\Repositories\Contracts\AppointmentRepositoryInterface;

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
}
