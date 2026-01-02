<?php

namespace App\Http\Requests\Tasks;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Validation rules for updating a task request.
     *
     * @return array<string, array|string> Mapping of field names to their validation rules.
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'status' => [Rule::enum(TaskStatus::class)],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool true if the user is authorized to make this request, false otherwise.
     */
    public function authorize(): bool
    {
        return true;
    }
}