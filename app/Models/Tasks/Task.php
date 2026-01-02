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

    public function resolveRouteBinding($value, $field = null): Model|Task|null
    {
        $user = request()->user();
        return $this->where([
            ['user_id', $user->id],
            ['id', $value],
        ])->firstOrFail();
    }
}
