<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'task_category_id' => ['required', 'integer', Rule::exists('task_categories', 'id')->where('user_id', $this->user()->id)]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
