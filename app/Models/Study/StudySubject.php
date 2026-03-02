<?php

namespace App\Models\Study;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudySubject extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'color',
    ];

    public function resolveRouteBinding($value, $field = null): Model|StudySubject|null
    {
        $user = request()->user();
        if (!$user) {
            abort(404);
        }

        return $this->where('id', $value)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereNull('user_id');
            })
            ->firstOrFail();
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(StudySession::class, 'study_subject_id');
    }
}
