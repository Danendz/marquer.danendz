<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskFolderRequest extends FormRequest
{
    /**
     * Validation rules for updating a task folder request.
     *
     * @return array<string, array|string> Validation rules keyed by field name. The `name` field must be a string with a maximum length of 255 characters.
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
        ];
    }

    /**
     * Allow all users to perform this update task folder request.
     *
     * @return bool Always `true`, indicating the request is authorized for any user.
     */
    public function authorize(): bool
    {
        return true;
    }
}