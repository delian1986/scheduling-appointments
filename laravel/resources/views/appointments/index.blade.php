@extends('layouts.app')

@section('title', 'Appointments')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Appointments</h1>
        <a
            href="{{ route('appointments.add') }}"
            class="rounded-md bg-[#1b1b18] px-4 py-2 text-sm font-medium text-white hover:bg-black"
        >
            Add appointment
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <p class="text-sm text-[#706f6c]">Appointment list will be available here.</p>
@endsection
