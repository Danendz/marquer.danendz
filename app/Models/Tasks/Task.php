<?php

namespace App\Models\Tasks;

use App\Enums\TaskStatus;
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

    protected $casts = [
        'status' => TaskStatus::class,
    ];

    public function resolveRouteBinding($value, $field = null): Model|Task|null
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }
}
