<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    /**
     * Get the validation rules for storing a task.
     *
     * @return array Validation rules:
     *               - `name`: required, string, max 255.
     *               - `task_category_id`: optional (sometimes), nullable, integer, must reference an existing task_categories.id belonging to the authenticated user.
     *               - `date`: optional (sometimes), nullable, valid date.
     *               - `start_time`: optional (sometimes), nullable, format H:i.
     *               - `end_time`: optional (sometimes), nullable, format H:i.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'task_category_id' => ['sometimes', 'nullable', 'integer', Rule::exists('task_categories', 'id')->where('user_id', $this->user()->id)],
            'date' => ['sometimes', 'nullable', 'date'],
            'start_time' => ['sometimes', 'nullable', 'date_format:H:i'],
            'end_time' => ['sometimes', 'nullable', 'date_format:H:i'],
            'color' => ['sometimes', 'nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
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