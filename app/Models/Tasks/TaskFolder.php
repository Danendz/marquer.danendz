<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskFolder extends Model
{
    protected $fillable = [
        'name',
        'user_id',
    ];

    public function categories(): HasMany|TaskFolder
    {
        return $this->hasMany(TaskCategory::class, 'task_folder_id');
    }
}
