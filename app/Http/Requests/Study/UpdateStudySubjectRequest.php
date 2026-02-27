<?php

namespace App\Http\Requests\Study;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudySubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:100'],
            'color' => ['sometimes', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }
}
