<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskCategoryRequest extends FormRequest
{
    /**
     * Validation rules for updating a task category.
     *
     * @return array<string, mixed> An array mapping input field names to their validation rules; includes
     *                             `'name'` as a string with a maximum length of 255 characters.
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255']
        ];
    }

    /**
     * Permit any user to perform this request.
     *
     * @return bool `true` if the request is authorized, `false` otherwise.
     */
    public function authorize(): bool
    {
        return true;
    }
}