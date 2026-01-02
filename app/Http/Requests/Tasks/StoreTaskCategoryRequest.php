<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskCategoryRequest extends FormRequest
{
    /**
     * Validation rules for creating a task category.
     *
     * Ensures `name` is present and under 255 characters, `color` is optional and under 255 characters, and `task_folder_id` is an existing `task_folders.id` that belongs to the authenticated user.
     *
     * @return array<string, mixed> The validation rules keyed by field name.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'task_folder_id' => ['required', 'integer', Rule::exists('task_folders', 'id')->where('user_id', $this->user()->id)]
        ];
    }

    /**
     * Authorize the incoming request and allow all users to proceed.
     *
     * @return bool `true` to allow the request, `false` otherwise.
     */
    public function authorize(): bool
    {
        return true;
    }
}