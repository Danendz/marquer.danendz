<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskFolderRequest extends FormRequest
{
    /**
     * Get the validation rules for creating a task folder.
     *
     * @return array<string, array|string> Validation rules keyed by field name.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255']
        ];
    }

    /**
     * Determine whether the user is authorized to make this request.
     *
     * @return bool `true` if the request is authorized, `false` otherwise.
     */
    public function authorize(): bool
    {
        return true;
    }
}