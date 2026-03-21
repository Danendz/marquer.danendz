<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendMailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipient_id' => ['required', 'integer'],
            'template_type' => ['required', Rule::in(['postcard', 'letter'])],
            'template_key' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:2000'],
        ];
    }
}
