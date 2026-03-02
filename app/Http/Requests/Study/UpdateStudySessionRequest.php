<?php

namespace App\Http\Requests\Study;

use App\Enums\StudySessionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudySessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'actual_duration_seconds' => ['sometimes', 'integer', 'min:0'],
            'status' => ['sometimes', Rule::enum(StudySessionStatus::class)->only([StudySessionStatus::Paused, StudySessionStatus::Active])],
            'pomodoro_completed_cycles' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
