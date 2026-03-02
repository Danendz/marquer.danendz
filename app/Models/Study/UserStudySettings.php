<?php

namespace App\Models\Study;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class UserStudySettings extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'user_id',
        'default_work_minutes',
        'default_short_break_minutes',
        'default_long_break_minutes',
        'default_cycles',
    ];
}
