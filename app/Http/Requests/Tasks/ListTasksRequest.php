<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListTasksRequest extends FormRequest
{
    /**
     * Validation rules for listing tasks.
     *
     * The `task_category_id` field is required, must be an integer, and must reference an existing
     * `id` in the `task_categories` table where `user_id` equals the authenticated user's id.
     *
     * @return array<string, mixed> Validation rules keyed by request field.
     */
    public function rules(): array
    {
        return [
            'task_category_id' => ['required', 'integer', Rule::exists('task_categories', 'id')->where('user_id', $this->user()->id)]
        ];
    }

    /**
     * Allow any user to make this request.
     *
     * @return bool `true` if the request is authorized, `false` otherwise.
     */
    public function authorize(): bool
    {
        return true;
    }
}