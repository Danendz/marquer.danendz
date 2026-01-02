<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'title'
    ];

    /**
     * Resolve a route binding to a Note belonging to the currently authenticated user.
     *
     * @param mixed $value The route parameter value to match (typically the note id).
     * @param string|null $field The database column to query against; defaults to the model's key when null.
     * @return Model|Note|null The matched Note model instance.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no matching note is found.
     */
    public function resolveRouteBinding($value, $field = null): Model|Note|null
    {
        $user = request()->user();
        return $this->where([
            ['user_id', $user->id],
            ['id', $value],
        ])->firstOrFail();
    }
}