<?php

namespace App\Http\Resources\Study;

use App\Models\Study\StudySubject;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin StudySubject */
class StudySubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'color' => $this->color,
            'is_system' => $this->user_id === null,
            'created_at' => $this->created_at,
        ];
    }
}
