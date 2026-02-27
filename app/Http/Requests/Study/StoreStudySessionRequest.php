<?php

namespace App\Http\Requests\Study;

use App\Enums\TimerMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudySessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'study_subject_id' => [
                'nullable',
                'integer',
                Rule::exists('study_subjects', 'id')->where(
                    fn($q) => $q->where(fn($q2) => $q2->whereNull('user_id')->orWhere('user_id', $this->user()->id))
                ),
            ],
            'timer_mode' => ['required', Rule::enum(TimerMode::class)],
            'planned_duration_seconds' => ['required_if:timer_mode,count_down', 'nullable', 'integer', 'min:60'],
            'pomodoro_work_minutes' => ['required_if:timer_mode,pomodoro', 'nullable', 'integer', 'min:1', 'max:120'],
            'pomodoro_short_break_minutes' => ['required_if:timer_mode,pomodoro', 'nullable', 'integer', 'min:1', 'max:60'],
            'pomodoro_long_break_minutes' => ['required_if:timer_mode,pomodoro', 'nullable', 'integer', 'min:1', 'max:60'],
            'pomodoro_cycles' => ['required_if:timer_mode,pomodoro', 'nullable', 'integer', 'min:1', 'max:20'],
        ];
    }
}
