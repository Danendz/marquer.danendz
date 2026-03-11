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
     * @param \Illuminate\Http\Request $request The incoming request (unused).
     * @return array Associative array with keys `id`, `name`, `status`, `date`, `start_time`, `end_time`, `task_category_id`, `color`, `created_at`, and `updated_at`.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'date' => $this->date?->toDateString(),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'task_category_id' => $this->task_category_id,
            'color' => $this->color,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}