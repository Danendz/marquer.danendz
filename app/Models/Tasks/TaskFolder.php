<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskFolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
    ];

    /**
     * Get the task categories that belong to this task folder.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany A HasMany relation for TaskCategory models keyed by `task_folder_id`.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(TaskCategory::class, 'task_folder_id');
    }

    /**
     * Resolve route model binding to a TaskFolder belonging to the authenticated user.
     *
     * @param mixed $value The route parameter value used to identify the folder (typically the folder ID or slug).
     * @param string|null $field The database field to match against the value; defaults to the model's route key.
     * @return \Illuminate\Database\Eloquent\Model|\App\Models\Tasks\TaskFolder The matching TaskFolder model.
     */
    public function resolveRouteBinding($value, $field = null): Model|TaskFolder|null
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