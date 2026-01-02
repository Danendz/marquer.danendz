<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'task_category_id',
        'name',
        'status',
        'user_id',
    ];

    protected $attributes = [
        'status' => 'draft'
    ];
}
