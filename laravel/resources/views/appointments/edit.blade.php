@extends('layouts.app')

@section('title', 'Edit Appointment')

@section('content')
    <div class="mb-6">
        <a href="{{ route('appointments.index') }}" class="text-sm text-[#706f6c] hover:underline">&larr; Back to appointments</a>
        <h1 class="mt-2 text-2xl font-semibold">Edit Appointment</h1>
    </div>

    @if (session('error'))
        <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @include('appointments._form', [
        'action' => route('appointments.update', $appointment),
        'method' => 'PATCH',
        'submitLabel' => 'Update appointment',
        'appointment' => $appointment,
        'notificationMethods' => $notificationMethods,
    ])
@endsection
