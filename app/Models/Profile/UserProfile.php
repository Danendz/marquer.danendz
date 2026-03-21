<?php

namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'username',
        'status',
        'location',
        'bio',
        'avatar_url',
        'cover_url',
        'cover_type',
        'invite_code',
    ];
}
