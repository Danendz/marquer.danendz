<?php

namespace App\Http\Resources\Calendar;

use App\Http\Resources\Tasks\TaskResource;

class CalendarWeekResponse
{
    public static function from(array $data): array
    {
        $data['tasks'] = $data['tasks']
            ->map(fn ($group) => TaskResource::collection($group)->resolve())
            ->toArray();

        $data['countdowns'] = $data['countdowns']
            ->map(fn ($group) => CountdownResource::collection($group)->resolve())
            ->toArray();

        return $data;
    }
}
