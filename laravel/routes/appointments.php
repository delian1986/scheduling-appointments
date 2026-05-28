<?php

declare(strict_types=1);

use App\Http\Controllers\Web\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AppointmentController::class, 'index'])->name('appointments.index');
Route::get('/appointments/add', [AppointmentController::class, 'create'])->name('appointments.add');
Route::post('/appointments/add', [AppointmentController::class, 'store'])->name('appointments.store');
