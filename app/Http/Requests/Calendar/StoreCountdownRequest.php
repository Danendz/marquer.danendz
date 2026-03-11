<?php

namespace App\Http\Requests\Calendar;

use Illuminate\Foundation\Http\FormRequest;

class StoreCountdownRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'target_date' => ['required', 'date', 'after:today'],
        ];
    }
}
