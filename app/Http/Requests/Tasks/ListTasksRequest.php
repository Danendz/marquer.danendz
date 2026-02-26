<?php

namespace App\Http\Requests\Tasks;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListTasksRequest extends FormRequest
{
    /**
     * Validation rules for listing tasks with optional filters.
     *
     * All filters are optional. When provided:
     * - `task_category_id`: integer, must exist in `task_categories.id` for the authenticated user.
     * - `task_folder_id`: integer, must exist in `task_folders.id` for the authenticated user.
     * - `status`: must be a valid TaskStatus enum value.
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