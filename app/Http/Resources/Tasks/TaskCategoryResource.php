<?php

namespace App\Http\Resources\Tasks;

use App\Models\Tasks\TaskCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin TaskCategory */
class TaskCategoryResource extends JsonResource
{
    /**
     * Transform the task category resource into an array suitable for JSON responses.
     *
     * @param Request $request The incoming HTTP request instance.
     * @return array{
     *     id:int,
     *     name:string,
     *     color:?string,
     *     tasks_count?:int|null,
     *     created_at:\Illuminate\Support\Carbon|null,
     *     updated_at:\Illuminate\Support\Carbon|null
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'color' => $this->color,
            'tasks_count' => $this->whenCounted('tasks'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}