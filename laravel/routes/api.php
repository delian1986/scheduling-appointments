<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::get('/appointments', [AppointmentController::class, 'index'])->name('api.appointments.index');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('api.appointments.store');
