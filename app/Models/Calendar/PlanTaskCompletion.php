<?php

namespace App\Models\Calendar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanTaskCompletion extends Model
{
    protected $fillable = [
        'plan_task_id',
        'completed_date',
    ];

    protected $casts = [
        'completed_date' => 'date',
    ];

    public function planTask(): BelongsTo
    {
        return $this->belongsTo(PlanTask::class);
    }
}
