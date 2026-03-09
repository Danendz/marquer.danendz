<?php

namespace App\Models\Calendar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanTask extends Model
{
    protected $fillable = [
        'plan_id',
        'name',
        'sort_order',
        'start_time',
        'end_time',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function completions(): HasMany
    {
        return $this->hasMany(PlanTaskCompletion::class);
    }

    public function resolveRouteBinding($value, $field = null): ?self
    {
        return $this->where('id', $value)
            ->whereHas('plan', fn ($q) => $q->where('user_id', auth()->id()))
            ->firstOrFail();
    }
}
