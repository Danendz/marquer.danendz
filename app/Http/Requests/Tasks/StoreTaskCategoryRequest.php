<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskCategoryRequest extends FormRequest
{
    /**
     * Define validation rules for creating a task category.
     *
     * Returns an array mapping request fields to their validation rules:
     * - `name`: required string up to 255 characters.
     * - `color`: optional string up to 255 characters.
     * - `task_folder_id`: required integer that must exist in `task_folders.id` belonging to the authenticated user.
     *
     * @return array<string, array<int, mixed>> Validation rules for the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'task_folder_id' => ['required', 'integer', Rule::exists('task_folders', 'id')->where('user_id', $this->user()->id)]
        ];
    }

    /**
     * Allow the request for all users.
     *
     * @return bool `true` if the request is authorized, `false` otherwise.
     */
    public function authorize(): bool
    {
        return true;
    }
}