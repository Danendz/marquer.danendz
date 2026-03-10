<?php

namespace App\Http\Requests\Calendar;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCountdownRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'target_date' => ['sometimes', 'date', 'after_or_equal:today'],
            'is_pinned' => ['sometimes', 'boolean'],
            'bg_image' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
