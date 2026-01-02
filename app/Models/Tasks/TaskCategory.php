<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskCategory extends Model
{
    protected $fillable = [
        'task_folder_id',
        'name',
        'color',
        'user_id'
    ];

    /**
     * Get the tasks that belong to this category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany Relationship for Task models belonging to this category.
     */
    public function tasks(): HasMany|Task
    {
        return $this->hasMany(Task::class, 'task_category_id');
    }

    /**
     * Resolve a route parameter to the authenticated user's TaskCategory.
     *
     * @param mixed $value The route value (usually the TaskCategory id) to resolve.
     * @param string|null $field The field name to match (ignored; present for signature compatibility).
     * @return \App\Models\Tasks\TaskCategory|Model The resolved TaskCategory model.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no matching TaskCategory is found for the authenticated user.
     */
    public function resolveRouteBinding($value, $field = null): Model|TaskCategory|null
    {
        $user = request()->user();
        return $this->where([
            ['user_id', $user->id],
            ['id', $value],
        ])->firstOrFail();
    }
}