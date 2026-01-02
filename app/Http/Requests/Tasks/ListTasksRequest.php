<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListTasksRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'task_category_id' => ['required', 'integer', Rule::exists('task_categories', 'id')->where('user_id', $this->user()->id)]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
