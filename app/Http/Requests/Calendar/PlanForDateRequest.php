<?php

namespace App\Http\Requests\Calendar;

use Illuminate\Foundation\Http\FormRequest;

class PlanForDateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
        ];
    }
}
