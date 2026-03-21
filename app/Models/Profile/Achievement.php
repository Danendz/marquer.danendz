<?php

namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'icon',
        'category',
        'target_type',
        'target_value',
    ];
}
