<?php

namespace App\Models\Calendar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'schedule',
        'start_date',
        'end_date',
        'is_active',
        'color',
    ];

    protected $casts = [
        'schedule' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(PlanTask::class)->orderBy('sort_order');
    }

    public function resolveRouteBinding($value, $field = null): ?self
    {
        return $this->where('id', $value)->where('user_id', auth()->id())->firstOrFail();
    }
}
