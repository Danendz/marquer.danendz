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
     * Resolve a Note scoped to the authenticated user by the provided route value.
     *
     * @param mixed $value The route binding value — expected to be the Note's id.
     * @param string|null $field Optional field name (not used by this implementation).
     * @return \Illuminate\Database\Eloquent\Model|\App\Models\Note|null The Note instance belonging to the authenticated user.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException If no user is authenticated (HTTP 401).
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If a matching Note is not found.
     */
    public function resolveRouteBinding($value, $field = null): Model|Note|null
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