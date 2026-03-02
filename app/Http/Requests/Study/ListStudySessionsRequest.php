<?php

namespace App\Http\Requests\Study;

use App\Enums\StudySessionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListStudySessionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date_from' => ['sometimes', 'nullable', 'date'],
            'date_to' => ['sometimes', 'nullable', 'date', 'after_or_equal:date_from'],
            'study_subject_id' => [
                'sometimes',
                'nullable',
                Rule::exists('study_subjects', 'id')->where(
                    fn($q) => $q->where(fn($q2) => $q2->whereNull('user_id')->orWhere('user_id', $this->user()->id))
                ),
            ],
            'status' => ['sometimes', 'nullable', Rule::enum(StudySessionStatus::class)],
        ];
    }
}
