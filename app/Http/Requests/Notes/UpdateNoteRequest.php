<?php

namespace App\Http\Requests\Notes;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for an update note request.
     *
     * - title: must be a string with a maximum length of 255 characters.
     * - content: must be a string when present.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> An associative array mapping field names to their validation rules.
     */
    public function rules(): array
    {
        return [
            'title' => ['string', 'max:255'],
            'content' => ['string']
        ];
    }
}