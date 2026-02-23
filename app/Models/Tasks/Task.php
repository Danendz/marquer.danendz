<?php

namespace App\Models\Tasks;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_category_id',
        'name',
        'status',
        'user_id',
    ];

    protected $attributes = [
        'status' => 'draft'
    ];

    protected $casts = [
        'status' => TaskStatus::class,
    ];

    /**
     * Resolve a Task route binding scoped to the currently authenticated user.
     *
     * Looks up the Task whose `id` matches `$value` and whose `user_id` matches the authenticated user's id.
     *
     * @param mixed $value The route parameter value used to find the task (typically the task id).
     * @param string|null $field Unused. Present to satisfy the route binding signature.
     * @return \Illuminate\Database\Eloquent\Model|\App\Models\Tasks\Task The matched Task model.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException If no user is authenticated (HTTP 401).
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no matching Task is found.
     */
    public function resolveRouteBinding($value, $field = null): Model|Task|null
    {
        $user = request()->user();
        if (!$user) {
            abort(404);
        }
        return $this->where([
            ['user_id', $user->id],
            ['id', $value],
        ])->firstOrFail();
    }

    /**
     * Get the task's category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The BelongsTo relation for the associated TaskCategory.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }
}