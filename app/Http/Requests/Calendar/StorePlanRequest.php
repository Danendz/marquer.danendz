<?php

namespace App\Http\Requests\Calendar;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $type = $this->input('schedule.type');

        return [
            'name' => ['required', 'string', 'max:255'],
            'schedule' => ['required', 'array'],
            'schedule.type' => ['required', 'string', Rule::in(['daily', 'weekly', 'interval', 'monthly_dates', 'monthly_weekday'])],
            'schedule.days' => [
                Rule::requiredIf(in_array($type, ['weekly', 'monthly_dates'])),
                'nullable', 'array', 'min:1',
            ],
            'schedule.days.*' => match ($type) {
                'weekly' => ['integer', 'min:0', 'max:6'],
                'monthly_dates' => ['integer', 'min:1', 'max:31'],
                default => ['integer'],
            },
            'schedule.every' => [
                Rule::requiredIf($type === 'interval'),
                'nullable', 'integer', 'min:1',
            ],
            'schedule.week' => [
                Rule::requiredIf($type === 'monthly_weekday'),
                'nullable', 'integer', Rule::in([-1, 1, 2, 3, 4]),
            ],
            'schedule.weekday' => [
                Rule::requiredIf($type === 'monthly_weekday'),
                'nullable', 'integer', 'min:0', 'max:6',
            ],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'color' => ['sometimes', 'nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'tasks' => ['required', 'array', 'min:1'],
            'tasks.*.name' => ['required', 'string', 'max:255'],
            'tasks.*.sort_order' => ['required', 'integer'],
            'tasks.*.start_time' => ['sometimes', 'nullable', 'date_format:H:i'],
            'tasks.*.end_time' => ['sometimes', 'nullable', 'date_format:H:i', 'after_or_equal:tasks.*.start_time'],
        ];
    }
}
