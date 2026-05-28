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
                                    <button
                                    type="button"
                                    class="rounded-md border border-[#e3e3e0] px-3 py-1.5 text-sm hover:bg-[#f8f8f6]"
                                    >
                                        Details
                                    </button>
                                    <button
                                        type="button"
                                        class="rounded-md border border-[#e3e3e0] px-3 py-1.5 text-sm hover:bg-[#f8f8f6]"
                                    >
                                        Edit
                                    </button>
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
