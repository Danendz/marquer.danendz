<?php

namespace App\Http\Resources\Calendar;

use App\Models\Calendar\PlanTask;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin PlanTask */
class PlanTaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sort_order' => $this->sort_order,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ];
    }
}
