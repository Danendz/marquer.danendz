<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_folder_id',
        'name',
        'color',
        'user_id'
    ];

    /**
     * Get the tasks that belong to this task category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany The relationship query for Task models using `task_category_id` as the foreign key.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'task_category_id');
    }

     /**
     * Get the TaskFolder that this category belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo Relation to the TaskFolder model.
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(TaskFolder::class, 'task_folder_id');
    }

    /**
     * Resolve a route binding scoped to the authenticated user's task categories.
     *
     * @param  mixed        $value The route parameter value used to locate the TaskCategory (typically the category id).
     * @param  string|null  $field Optional database field to match against the value.
     * @return \App\Models\Tasks\TaskCategory The TaskCategory that belongs to the authenticated user and matches the provided value.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no matching TaskCategory is found.
     */
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