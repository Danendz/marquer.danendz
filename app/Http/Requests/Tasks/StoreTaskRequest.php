<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    /**
     * Validation rules for storing a Task.
     *
     * Provides field-specific validation: `name` must be present, a string, and at most 255 characters;
     * `task_category_id` must be present, an integer, and reference an existing task_categories.id that belongs to the authenticated user.
     *
     * @return array<string, mixed> Validation rules keyed by request field name.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'task_category_id' => ['required', 'integer', Rule::exists('task_categories', 'id')->where('user_id', $this->user()->id)]
        ];
    }

    /**
     * Indicates whether the current request is authorized.
     *
     * This implementation always grants authorization for any user.
     *
     * @return bool `true` if the request is authorized, `false` otherwise.
     */
    public function authorize(): bool
    {
        return true;
    }
}