<?php

namespace App\Http\Resources\Tasks;

use App\Models\Tasks\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Task */
class TaskResource extends JsonResource
{
    /**
     * Transform the Task model into an associative array for JSON responses.
     *
     * The returned array contains the Task's `id`, `name`, `status`, `created_at`, and `updated_at` attributes.
     *
     * @param \Illuminate\Http\Request $request The incoming request (unused).
     * @return array Associative array with keys `id`, `name`, `status`, `created_at`, and `updated_at`.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'task_category_id' => $this->task_category_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}