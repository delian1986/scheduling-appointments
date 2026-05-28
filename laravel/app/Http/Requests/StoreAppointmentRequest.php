<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\NotificationMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'min:2', 'max:255'],
            'ucn' => ['required', 'string', 'regex:/^\d+$/', 'max:20'],
            'description' => ['nullable', 'string'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'notification_method' => ['required', Rule::enum(NotificationMethod::class)],
            'email' => ['required_if:notification_method,email', 'nullable', 'email', 'max:255'],
            'phone' => ['required_if:notification_method,sms', 'nullable', 'string', 'max:30'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $scheduledAt = $this->input('scheduled_at');

        if (is_string($scheduledAt) && str_contains($scheduledAt, 'T')) {
            $this->merge([
                'scheduled_at' => str_replace('T', ' ', $scheduledAt),
            ]);
        }
    }
}
