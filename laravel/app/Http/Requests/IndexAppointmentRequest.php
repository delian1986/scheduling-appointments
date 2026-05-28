<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class IndexAppointmentRequest extends FormRequest
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
            'start_date' => ['nullable', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
            'end_time'   => ['nullable', 'date_format:H:i'],
            'ucn'        => ['nullable', 'string', 'regex:/^\d+$/', 'min:10', 'max:20'],
            'direction'  => ['nullable', 'in:asc,desc'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function filters(): array
    {
        $validated = $this->validated();

        foreach (['start_date', 'end_date'] as $key) {
            if (! empty($validated[$key])) {
                $isDateOnly = preg_match('/^\d{4}-\d{2}-\d{2}$/', $validated[$key]) === 1;

                if ($isDateOnly) {
                    $validated[$key] .= $key === 'start_date' ? ' 00:00:00' : ' 23:59:59';
                }
            }
        }

        return array_filter([
            'start_date' => $validated['start_date'] ?? null,
            'end_date'   => $validated['end_date'] ?? null,
            'ucn'        => $validated['ucn'] ?? null,
            'direction'  => $validated['direction'] ?? 'desc',
        ], fn ($value) => $value !== null && $value !== '');
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('ucn')) {
            $ucn = trim((string) $this->input('ucn'));
            $this->merge(['ucn' => $ucn === '' ? null : $ucn]);
        }

        foreach ([['start_date', 'start_time'], ['end_date', 'end_time']] as [$dateKey, $timeKey]) {
            $date = $this->input($dateKey);
            $time = $this->input($timeKey);

            if (is_string($date) && $date !== '' && is_string($time) && $time !== '') {
                $this->merge([$dateKey => $date.' '.$time]);
            }
        }
    }
}
