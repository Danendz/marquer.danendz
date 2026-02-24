<?php

namespace App\Http\Requests\Tasks;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListTasksRequest extends FormRequest
{
    /**
     * Validation rules for listing tasks filtered by category.
     *
     * Ensures `task_category_id` is present, is an integer, and exists in the `task_categories.id`
     * column for the currently authenticated user (`user_id` equals the request user id).
     *
     * @return array Validation rules for the request.
     */
    public function rules(): array
    {
        return [
            'task_category_id' => ['sometimes', 'nullable', 'integer', Rule::exists('task_categories', 'id')->where('user_id', $this->user()->id)],
            'task_folder_id' => ['sometimes', 'nullable', 'integer', Rule::exists('task_folders', 'id')->where('user_id', $this->user()->id)],
            'status' => ['sometimes', Rule::enum(TaskStatus::class)],
        ];
    }

    /**
     * Determine whether the current request is authorized.
     *
     * @return bool `true` to allow the request; this implementation always returns `true`.
     */
    public function authorize(): bool
    {
        return true;
    }
}