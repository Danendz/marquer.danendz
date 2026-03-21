<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'theme' => ['sometimes', 'string', Rule::in(['light', 'dark'])],
            'language' => ['sometimes', 'string', Rule::in(['en', 'fr', 'zh_CN'])],
            'notifications_enabled' => ['sometimes', 'boolean'],
            'notification_study_reminders' => ['sometimes', 'boolean'],
            'notification_task_reminders' => ['sometimes', 'boolean'],
        ];
    }
}
