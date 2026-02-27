<?php

namespace App\Http\Resources\Study;

use App\Models\Study\UserStudySettings;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin UserStudySettings */
class UserStudySettingsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'default_work_minutes' => $this->default_work_minutes,
            'default_short_break_minutes' => $this->default_short_break_minutes,
            'default_long_break_minutes' => $this->default_long_break_minutes,
            'default_cycles' => $this->default_cycles,
        ];
    }
}
