<?php

namespace App\Http\Resources\Tasks;

use App\Models\Tasks\TaskCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin TaskCategory */
class TaskCategoryResource extends JsonResource
{
    /**
     * Convert the resource into an array suitable for JSON responses.
     *
     * Includes `id`, `name`, `color`, `created_at`, `updated_at`, and `tasks_count`
     * (the latter is included only when the `tasks` relationship has been counted).
     *
     * @param Request $request
     * @return array<string,mixed> Array with keys:
     *   - `id` (int)
     *   - `name` (string)
     *   - `color` (string|null)
     *   - `tasks_count` (int|null) present only when `tasks` is counted
     *   - `created_at` (mixed) timestamp of creation
     *   - `updated_at` (mixed) timestamp of last update
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