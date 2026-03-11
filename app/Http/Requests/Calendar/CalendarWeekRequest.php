<?php

namespace App\Http\Requests\Calendar;

use Illuminate\Foundation\Http\FormRequest;

class CalendarWeekRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $from = $this->input('from');
            $to = $this->input('to');

            if ($from && $to) {
                $diff = \Carbon\Carbon::parse($from)->diffInDays(\Carbon\Carbon::parse($to));
                if ($diff > 6) {
                    $validator->errors()->add('to', 'Date range must not exceed 7 days.');
                }
            }
        });
    }
}
