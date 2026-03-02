<?php

namespace App\Http\Requests\Study;

use Illuminate\Foundation\Http\FormRequest;

class UpsertUserStudySettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'default_work_minutes' => ['required', 'integer', 'min:1', 'max:120'],
            'default_short_break_minutes' => ['required', 'integer', 'min:1', 'max:60'],
            'default_long_break_minutes' => ['required', 'integer', 'min:1', 'max:60'],
            'default_cycles' => ['required', 'integer', 'min:1', 'max:20'],
        ];
    }
}
