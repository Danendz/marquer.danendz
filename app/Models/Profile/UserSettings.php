<?php

namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
    protected $fillable = [
        'user_id',
        'theme',
        'language',
        'notifications_enabled',
        'notification_study_reminders',
        'notification_task_reminders',
        'lab_features',
    ];

    protected function casts(): array
    {
        return [
            'notifications_enabled' => 'boolean',
            'notification_study_reminders' => 'boolean',
            'notification_task_reminders' => 'boolean',
            'lab_features' => 'array',
        ];
    }
}
