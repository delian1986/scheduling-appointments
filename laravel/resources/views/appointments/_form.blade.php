<form
    action="{{ $action }}"
    method="POST"
    class="space-y-5 rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm"
>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div>
        <label for="scheduled_at" class="mb-1 block text-sm font-medium">Scheduled time</label>
        <input
            type="datetime-local"
            id="scheduled_at"
            name="scheduled_at"
            value="{{ old('scheduled_at', $appointment?->scheduled_at?->format('Y-m-d\TH:i')) }}"
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
            value="{{ old('full_name', $appointment?->client?->full_name) }}"
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
            value="{{ old('ucn', $appointment?->client?->ucn) }}"
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
        >{{ old('description', $appointment?->description) }}</textarea>
        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <fieldset>
        <legend class="mb-2 block text-sm font-medium">Notification method</legend>
        <div class="flex gap-6">
            @foreach ($notificationMethods as $methodOption)
                <label class="inline-flex items-center gap-2 text-sm">
                    <input
                        type="radio"
                        name="notification_method"
                        value="{{ $methodOption->value }}"
                        class="notification-method-radio"
                        @checked(old('notification_method', $appointment?->notification_method?->value ?? 'email') === $methodOption->value)
                    >
                    {{ ucfirst($methodOption->value) }}
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
            value="{{ old('email', $appointment?->client?->email) }}"
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
            value="{{ old('phone', $appointment?->client?->phone) }}"
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
        {{ $submitLabel }}
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
