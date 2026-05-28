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

    @if ($errors->any())
        <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('appointments.index') }}" class="mb-6 rounded-lg border border-[#e3e3e0] bg-white p-4 shadow-sm">
        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="mb-1 block text-sm font-medium">Start date</label>
                <div class="flex gap-2">
                    <input
                        type="date"
                        name="start_date"
                        value="{{ old('start_date', $filters['start_date_date'] ?? '') }}"
                        class="w-full rounded-md border border-[#e3e3e0] px-3 py-2 text-sm"
                    >
                    <input
                        type="time"
                        name="start_time"
                        value="{{ old('start_time', $filters['start_date_time'] ?? '') }}"
                        class="w-32 rounded-md border border-[#e3e3e0] px-3 py-2 text-sm"
                    >
                </div>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">End date</label>
                <div class="flex gap-2">
                    <input
                        type="date"
                        name="end_date"
                        value="{{ old('end_date', $filters['end_date_date'] ?? '') }}"
                        class="w-full rounded-md border border-[#e3e3e0] px-3 py-2 text-sm"
                    >
                    <input
                        type="time"
                        name="end_time"
                        value="{{ old('end_time', $filters['end_date_time'] ?? '') }}"
                        class="w-32 rounded-md border border-[#e3e3e0] px-3 py-2 text-sm"
                    >
                </div>
            </div>
            <div>
                <label for="ucn" class="mb-1 block text-sm font-medium">Client UCN</label>
                <input
                    type="text"
                    id="ucn"
                    name="ucn"
                    inputmode="numeric"
                    value="{{ old('ucn', $filters['ucn'] ?? '') }}"
                    class="w-full rounded-md border border-[#e3e3e0] px-3 py-2 text-sm @error('ucn') border-red-500 @enderror"
                    placeholder="1234567890"
                >
                @error('ucn')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="mt-4 flex gap-2">
            <button
                type="submit"
                class="rounded-md bg-[#1b1b18] px-4 py-2 text-sm font-medium text-white hover:bg-black"
            >
                Filter
            </button>
            <a
                href="{{ route('appointments.index') }}"
                class="rounded-md border border-[#e3e3e0] px-4 py-2 text-sm font-medium hover:bg-[#f8f8f6]"
            >
                Reset
            </a>
        </div>
    </form>

    @if ($appointments->isEmpty())
        <p class="text-sm text-[#706f6c]">No appointments yet.</p>
    @else
        <div class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-[#e3e3e0] bg-[#f8f8f6]">
                    <tr>
                        <th class="px-4 py-3 font-medium">Scheduled at</th>
                        <th class="px-4 py-3 font-medium">Client</th>
                        <th class="px-4 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appointments as $appointment)
                        <tr class="border-b border-[#e3e3e0] last:border-b-0">
                            <td class="px-4 py-3">{{ $appointment->scheduled_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3">{{ $appointment->client->full_name }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex gap-2">
                                    <a
                                        href="{{ route('appointments.show', $appointment) }}"
                                        class="rounded-md border border-[#e3e3e0] px-3 py-1.5 text-sm hover:bg-[#f8f8f6]"
                                    >
                                        Details
                                    </a>
                                    <a
                                        href="{{ route('appointments.edit', $appointment) }}"
                                        class="rounded-md border border-[#e3e3e0] px-3 py-1.5 text-sm hover:bg-[#f8f8f6]"
                                    >
                                        Edit
                                    </a>
                                    <button
                                        type="button"
                                        class="rounded-md border border-red-200 px-3 py-1.5 text-sm text-red-700 hover:bg-red-50"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $appointments->links() }}
        </div>
    @endif
@endsection
