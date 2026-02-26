<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    /**
     * Get the validation rules for storing a task.
     *
     * @return array An associative array of validation rules:
     *               - `name`: required, string, maximum length 255.
     *               - `task_category_id`: required, integer, and must reference an existing `task_categories.id` that belongs to the authenticated user.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'task_category_id' => ['sometimes', 'nullable', 'integer', Rule::exists('task_categories', 'id')->where('user_id', $this->user()->id)]
        ];
    }

    /**
     * Determine whether the current user is authorized to make this request.
     *
     * @return bool `true` if the request is authorized, `false` otherwise.
     */
    public function authorize(): bool
    {
        return true;
    }
}