<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskFolderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
