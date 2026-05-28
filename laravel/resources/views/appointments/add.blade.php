@extends('layouts.app')

@section('title', 'Add Appointment')

@section('content')
    <div class="mb-6">
        <a href="{{ route('appointments.index') }}" class="text-sm text-[#706f6c] hover:underline">&larr; Back to appointments</a>
        <h1 class="mt-2 text-2xl font-semibold">Add Appointment</h1>
    </div>

    <form
        action="{{ route('appointments.store') }}"
        method="POST"
        class="space-y-5 rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm"
    >
        @csrf

        <div>
            <label for="scheduled_at" class="mb-1 block text-sm font-medium">Scheduled time</label>
            <input
                type="datetime-local"
                id="scheduled_at"
                name="scheduled_at"
                value="{{ old('scheduled_at') }}"
                min="{{ now()->format('Y-m-d\TH:i') }}"
                class="w-full rounded-md border border-[#e3e3e0] px-3 py-2 text-sm @error('scheduled_at') border-red-500 @enderror"
                required
            >
            @error('scheduled_at')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="full_name" class="mb-1 block text-sm font-medium">Client full name</label>
            <input
                type="text"
                id="full_name"
                name="full_name"
                value="{{ old('full_name') }}"
                class="w-full rounded-md border border-[#e3e3e0] px-3 py-2 text-sm @error('full_name') border-red-500 @enderror"
                required
            >
            @error('full_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="ucn" class="mb-1 block text-sm font-medium">UCN</label>
            <input
                type="text"
                id="ucn"
                name="ucn"
                value="{{ old('ucn') }}"
                minlength="10"
                maxlength="20"
                inputmode="numeric"
                pattern="[0-9]*"
                class="w-full rounded-md border border-[#e3e3e0] px-3 py-2 text-sm @error('ucn') border-red-500 @enderror"
                required
            >
            @error('ucn')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="mb-1 block text-sm font-medium">Description (optional)</label>
            <textarea
                id="description"
                name="description"
                rows="3"
                class="w-full rounded-md border border-[#e3e3e0] px-3 py-2 text-sm @error('description') border-red-500 @enderror"
            >{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <fieldset>
            <legend class="mb-2 block text-sm font-medium">Notification method</legend>
            <div class="flex gap-6">
                @foreach ($notificationMethods as $method)
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input
                            type="radio"
                            name="notification_method"
                            value="{{ $method->value }}"
                            class="notification-method-radio"
                            @checked(old('notification_method', 'email') === $method->value)
                        >
                        {{ ucfirst($method->value) }}
                    </label>
                @endforeach
            </div>
            @error('notification_method')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </fieldset>

        <div id="email-field" class="hidden">
            <label for="email" class="mb-1 block text-sm font-medium">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                class="w-full rounded-md border border-[#e3e3e0] px-3 py-2 text-sm @error('email') border-red-500 @enderror"
            >
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div id="phone-field" class="hidden">
            <label for="phone" class="mb-1 block text-sm font-medium">Phone</label>
            <input
                type="text"
                id="phone"
                name="phone"
                value="{{ old('phone') }}"
                class="w-full rounded-md border border-[#e3e3e0] px-3 py-2 text-sm @error('phone') border-red-500 @enderror"
            >
            @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="rounded-md bg-[#1b1b18] px-4 py-2 text-sm font-medium text-white hover:bg-black"
        >
            Save appointment
        </button>
    </form>

    <script>
        const emailField = document.getElementById('email-field');
        const phoneField = document.getElementById('phone-field');
        const radios = document.querySelectorAll('.notification-method-radio');

        function toggleContactFields() {
            const selected = document.querySelector('.notification-method-radio:checked')?.value;

            emailField.classList.toggle('hidden', selected !== 'email');
            phoneField.classList.toggle('hidden', selected !== 'sms');
        }

        radios.forEach((radio) => radio.addEventListener('change', toggleContactFields));
        toggleContactFields();
    </script>
@endsection
