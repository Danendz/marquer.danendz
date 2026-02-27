<?php

namespace App\Http\Requests\Study;

use Illuminate\Foundation\Http\FormRequest;

class CompleteStudySessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'actual_duration_seconds' => ['required', 'integer', 'min:0'],
            'pomodoro_completed_cycles' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
