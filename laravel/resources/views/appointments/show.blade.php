@extends('layouts.app')

@section('title', 'Appointment Details')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('appointments.index') }}" class="text-sm text-[#706f6c] hover:underline">&larr; Back to appointments</a>
            <h1 class="mt-2 text-2xl font-semibold">Appointment Details</h1>
        </div>
        <a
            href="{{ route('appointments.edit', $appointment) }}"
            class="rounded-md border border-[#e3e3e0] px-4 py-2 text-sm font-medium hover:bg-[#f8f8f6]"
        >
            Edit
        </a>
    </div>

    <div class="mb-8 rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm">
        <dl class="grid gap-4 md:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-[#706f6c]">Scheduled at</dt>
                <dd class="mt-1 text-sm">{{ $appointment->scheduled_at->format('Y-m-d H:i') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-[#706f6c]">Notification method</dt>
                <dd class="mt-1 text-sm">{{ ucfirst($appointment->notification_method->value) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-[#706f6c]">Notification status</dt>
                <dd class="mt-1 text-sm">{{ $appointment->notification_status }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-[#706f6c]">Notified at</dt>
                <dd class="mt-1 text-sm">{{ $appointment->notified_at?->format('Y-m-d H:i') ?? '—' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-[#706f6c]">Description</dt>
                <dd class="mt-1 text-sm">{{ $appointment->description ?? '—' }}</dd>
            </div>
        </dl>

        <h2 class="mb-3 mt-6 text-lg font-medium">Client</h2>
        <dl class="grid gap-4 md:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-[#706f6c]">Full name</dt>
                <dd class="mt-1 text-sm">{{ $appointment->client->full_name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-[#706f6c]">UCN</dt>
                <dd class="mt-1 text-sm">{{ $appointment->client->ucn }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-[#706f6c]">Email</dt>
                <dd class="mt-1 text-sm">{{ $appointment->client->email ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-[#706f6c]">Phone</dt>
                <dd class="mt-1 text-sm">{{ $appointment->client->phone ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    <h2 class="mb-4 text-lg font-semibold">Future appointments for this client</h2>

    @if ($futureAppointments->isEmpty())
        <p class="text-sm text-[#706f6c]">No future appointments for this client.</p>
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
                    @foreach ($futureAppointments as $futureAppointment)
                        <tr class="border-b border-[#e3e3e0] last:border-b-0">
                            <td class="px-4 py-3">{{ $futureAppointment->scheduled_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3">{{ $futureAppointment->client->full_name }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex gap-2">
                                    <a
                                        href="{{ route('appointments.show', $futureAppointment) }}"
                                        class="rounded-md border border-[#e3e3e0] px-3 py-1.5 text-sm hover:bg-[#f8f8f6]"
                                    >
                                        Details
                                    </a>
                                    <a
                                        href="{{ route('appointments.edit', $futureAppointment) }}"
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
            {{ $futureAppointments->links() }}
        </div>
    @endif
@endsection
