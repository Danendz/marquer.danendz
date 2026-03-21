<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class ToggleLabFeatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'feature' => ['required', 'string', 'max:50'],
            'enabled' => ['required', 'boolean'],
        ];
    }
}
