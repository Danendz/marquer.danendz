<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskFolderRequest extends FormRequest
{
    /**
     * Validation rules for creating a task folder.
     *
     * Specifies that the `name` field is required, must be a string, and may not exceed 255 characters.
     *
     * @return array<string, array<int, string>> Associative array of validation rules keyed by request field.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255']
        ];
    }

    /**
     * Determine whether the user is authorized to perform this request.
     *
     * @return bool `true` if the request is authorized, `false` otherwise.
     */
    public function authorize(): bool
    {
        return true;
    }
}