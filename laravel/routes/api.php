<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::get('/appointments', [AppointmentController::class, 'index'])->name('api.appointments.index');
Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('api.appointments.show');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('api.appointments.store');
Route::patch('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('api.appointments.update');
Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('api.appointments.destroy');
