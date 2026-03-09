<?php

namespace App\Http\Requests\Calendar;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlanRequest extends FormRequest
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
            'schedule.days.*' => ['integer', 'min:0', 'max:31'],
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
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['sometimes', 'boolean'],
            'tasks' => ['required', 'array', 'min:1'],
            'tasks.*.id' => ['sometimes', 'nullable', 'integer'],
            'tasks.*.name' => ['required', 'string', 'max:255'],
            'tasks.*.sort_order' => ['required', 'integer'],
            'tasks.*.start_time' => ['sometimes', 'nullable', 'date_format:H:i'],
            'tasks.*.end_time' => ['sometimes', 'nullable', 'date_format:H:i'],
        ];
    }
}
