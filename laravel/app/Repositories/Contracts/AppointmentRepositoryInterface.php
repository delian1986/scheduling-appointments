<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Appointment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AppointmentRepositoryInterface
{
    public function findById(int $id): ?Appointment;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Appointment;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Appointment $appointment, array $attributes): Appointment;

    public function delete(Appointment $appointment): bool;

    public function paginate(int $perPage = 15): LengthAwarePaginator;
}
