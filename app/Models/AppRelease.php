<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppRelease extends Model
{
    protected $fillable = [
        'platform', 'channel',
        'version', 'build_number', 'version_full',
        'git_sha',
        'bucket', 'object_key_latest', 'object_key_commit',
        'released_at',
    ];

    protected $casts = [
        'released_at' => 'datetime',
    ];
}
