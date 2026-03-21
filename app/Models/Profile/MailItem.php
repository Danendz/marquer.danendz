<?php

namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Model;

class MailItem extends Model
{
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'template_type',
        'template_key',
        'message',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }
}
