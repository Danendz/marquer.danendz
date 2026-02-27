<?php

namespace App\Models\Study;

use Illuminate\Database\Eloquent\Model;

class UserStudySettings extends Model
{
    protected $fillable = [
        'user_id',
        'default_work_minutes',
        'default_short_break_minutes',
        'default_long_break_minutes',
        'default_cycles',
    ];
}
