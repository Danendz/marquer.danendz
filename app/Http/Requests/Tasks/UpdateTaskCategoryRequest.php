<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
