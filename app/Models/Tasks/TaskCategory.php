<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskCategory extends Model
{
    protected $fillable = [
        'task_folder_id',
        'name',
        'color',
        'user_id'
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'task_category_id');
    }

     public function folder(): BelongsTo
    {
        return $this->belongsTo(TaskFolder::class, 'task_folder_id');
    }

    public function resolveRouteBinding($value, $field = null): Model|TaskCategory|null
    {
        $user = request()->user();

        if (!$user) {
            abort(401, 'Unauthenticated');
        }
        return $this->where([
            ['user_id', $user->id],
            ['id', $value],
        ])->firstOrFail();
    }
}
