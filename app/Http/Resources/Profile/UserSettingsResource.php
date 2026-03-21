<?php

namespace App\Http\Resources\Profile;

use App\Models\Profile\UserSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin UserSettings */
class UserSettingsResource extends JsonResource
{
    public function toArray(Request $_request): array
    {
        return [
            'theme' => $this->theme,
            'language' => $this->language,
            'notifications_enabled' => $this->notifications_enabled,
            'notification_study_reminders' => $this->notification_study_reminders,
            'notification_task_reminders' => $this->notification_task_reminders,
            'lab_features' => $this->lab_features,
        ];
    }
}
