<?php

namespace App\Http\Resources\Study;

use App\Models\Study\StudySession;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin StudySession */
class StudySessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'study_subject_id' => $this->study_subject_id,
            'name' => $this->name,
            'timer_mode' => $this->timer_mode,
            'status' => $this->status,
            'planned_duration_seconds' => $this->planned_duration_seconds,
            'actual_duration_seconds' => $this->actual_duration_seconds,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
            'pomodoro_work_minutes' => $this->pomodoro_work_minutes,
            'pomodoro_short_break_minutes' => $this->pomodoro_short_break_minutes,
            'pomodoro_long_break_minutes' => $this->pomodoro_long_break_minutes,
            'pomodoro_cycles' => $this->pomodoro_cycles,
            'pomodoro_completed_cycles' => $this->pomodoro_completed_cycles,
            'subject' => new StudySubjectResource($this->whenLoaded('subject')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
