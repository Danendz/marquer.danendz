<?php

namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    protected $fillable = [
        'user_id',
        'friend_id',
        'status',
    ];
}
