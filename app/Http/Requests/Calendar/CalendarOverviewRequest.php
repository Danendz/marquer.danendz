<?php

namespace App\Http\Requests\Calendar;

use Illuminate\Foundation\Http\FormRequest;

class CalendarOverviewRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
