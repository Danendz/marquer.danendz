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

    /**
     * Get the task categories that belong to this folder.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany A has-many relation for TaskCategory models keyed by `task_folder_id`.
     */
    public function categories(): HasMany|TaskFolder
    {
        return $this->hasMany(TaskCategory::class, 'task_folder_id');
    }

    /**
     * Resolve route-model binding by locating the TaskFolder that belongs to the current authenticated user and matches the provided value.
     *
     * @param mixed $value The route parameter value to match against the folder's identifier.
     * @param string|null $field The database column to match (unused; route key is matched against `id`).
     * @return \Illuminate\Database\Eloquent\Model|\App\Models\Tasks\TaskFolder|null The matching TaskFolder instance.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no matching TaskFolder is found.
     */
    public function resolveRouteBinding($value, $field = null): Model|TaskFolder|null
    {
        $user = request()->user();
        return $this->where([
            ['user_id', $user->id],
            ['id', $value],
        ])->firstOrFail();
    }
}