<?php

namespace App\Http\Requests\Wishes;

use Illuminate\Foundation\Http\FormRequest;

class StoreWishRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'wish' => ['required', 'string'],
            'signature' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string']
        ];
    }
}
