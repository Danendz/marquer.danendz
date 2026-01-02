<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskCategoryRequest extends FormRequest
{
    /**
     * Validation rules for updating a task category.
     *
     * @return array<string, mixed> Associative array of validation rules: 'name' must be a string with a maximum length of 255 characters; 'color' may be null or a string with a maximum length of 255 characters.
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Determine whether the user is authorized to make this request.
     *
     * This request is always authorized.
     *
     * @return bool `true` if the request is authorized, `false` otherwise.
     */
    public function authorize(): bool
    {
        return true;
    }
}