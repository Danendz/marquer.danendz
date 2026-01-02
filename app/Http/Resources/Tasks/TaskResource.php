<?php

namespace App\Http\Resources\Tasks;

use App\Models\Tasks\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Task */
class TaskResource extends JsonResource
{
    /**
     * Transform the task resource into an array representation.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request (unused).
     * @return array{
     *     id: mixed,
     *     name: mixed,
     *     status: mixed,
     *     created_at: mixed,
     *     updated_at: mixed
     * } An associative array with the task's id, name, status, created_at, and updated_at.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}