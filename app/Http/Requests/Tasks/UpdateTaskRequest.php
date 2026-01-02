<?php

namespace App\Http\Requests\Tasks;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Define the validation rules for updating a task.
     *
     * Returns an associative array mapping request field names to their validation constraints.
     *
     * @return array<string, array<int, mixed>> Validation rules for the request fields.
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'status' => [Rule::enum(TaskStatus::class)],
        ];
    }

    /**
     * Determine whether the user is authorized to make this request.
     *
     * This implementation always authorizes the request.
     *
     * @return bool `true` if the request is authorized, `false` otherwise.
     */
    public function authorize(): bool
    {
        return true;
    }
}