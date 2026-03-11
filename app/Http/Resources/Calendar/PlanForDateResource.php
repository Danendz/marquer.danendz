<?php

namespace App\Http\Resources\Calendar;

use App\Models\Calendar\Plan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Plan */
class PlanForDateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'color' => $this->color,
            'tasks' => $this->tasks->map(fn ($task) => [
                'id' => $task->id,
                'name' => $task->name,
                'sort_order' => $task->sort_order,
                'start_time' => $task->start_time,
                'end_time' => $task->end_time,
                'is_completed' => $task->completions->isNotEmpty(),
            ]),
        ];
    }
}
