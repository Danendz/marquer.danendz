<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'task_folder_id' => ['required', 'integer', Rule::exists('task_folders', 'id')->where('user_id', $this->user()->id)]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
