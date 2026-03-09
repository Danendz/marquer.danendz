<?php

namespace App\Http\Resources\Calendar;

use App\Models\Countdown;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Countdown */
class CountdownResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'target_date' => $this->target_date->toDateString(),
            'bg_image' => $this->bg_image,
            'is_pinned' => $this->is_pinned,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
