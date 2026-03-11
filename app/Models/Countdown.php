<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Countdown extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'target_date',
        'bg_image',
        'is_pinned',
    ];

    protected $casts = [
        'target_date' => 'date',
        'is_pinned' => 'boolean',
    ];

    public function resolveRouteBinding($value, $field = null): ?self
    {
        return $this->where('id', $value)->where('user_id', auth()->id())->firstOrFail();
    }
}
