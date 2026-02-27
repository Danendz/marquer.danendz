<?php

namespace App\Models\Study;

use App\Enums\StudySessionStatus;
use App\Enums\TimerMode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudySession extends Model
{
    protected $fillable = [
        'user_id',
        'study_subject_id',
        'name',
        'timer_mode',
        'status',
        'planned_duration_seconds',
        'actual_duration_seconds',
        'started_at',
        'ended_at',
        'pomodoro_work_minutes',
        'pomodoro_short_break_minutes',
        'pomodoro_long_break_minutes',
        'pomodoro_cycles',
        'pomodoro_completed_cycles',
    ];

    protected $casts = [
        'timer_mode' => TimerMode::class,
        'status' => StudySessionStatus::class,
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function resolveRouteBinding($value, $field = null): Model|StudySession|null
    {
        $user = request()->user();
        if (!$user) {
            abort(404);
        }

        return $this->where('id', $value)
            ->where('user_id', $user->id)
            ->firstOrFail();
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(StudySubject::class, 'study_subject_id');
    }
}
