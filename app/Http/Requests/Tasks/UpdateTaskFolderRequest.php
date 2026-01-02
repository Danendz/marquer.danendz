<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskFolderRequest extends FormRequest
{
    /**
     * Get the validation rules for updating a task folder.
     *
     * Validation rules:
     * - `name`: string, maximum 255 characters.
     * - `color`: nullable string, maximum 255 characters.
     *
     * @return array<string, mixed> Validation rules keyed by field name.
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Allow all users to perform this request.
     *
     * @return bool `true` to grant authorization (this implementation always grants authorization).
     */
    public function authorize(): bool
    {
        return true;
    }
}