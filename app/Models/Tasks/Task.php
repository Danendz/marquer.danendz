<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Model;

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

    /**
     * Resolve a route binding by retrieving the authenticated user's task with the given identifier.
     *
     * @param mixed $value The route parameter value to match against the task's primary key.
     * @param string|null $field Ignored; present for signature compatibility.
     * @return \App\Models\Tasks\Task|\Illuminate\Database\Eloquent\Model The matching Task model instance.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no matching task is found.
     */
    public function resolveRouteBinding($value, $field = null): Model|Task|null
    {
        $user = request()->user();
        return $this->where([
            ['user_id', $user->id],
            ['id', $value],
        ])->firstOrFail();
    }
}