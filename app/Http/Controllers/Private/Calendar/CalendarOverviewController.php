<?php

namespace App\Http\Controllers\Private\Calendar;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Calendar\CalendarOverviewRequest;
use App\Http\Resources\ApiResponse;
use App\Models\Tasks\Task;
use Illuminate\Http\JsonResponse;

class CalendarOverviewController extends Controller
{
    public function __invoke(CalendarOverviewRequest $request): JsonResponse
    {
        $dates = Task::where('user_id', auth()->id())
            ->whereBetween('date', [$request->validated('from'), $request->validated('to')])
            ->whereNotIn('status', [TaskStatus::Done->value, TaskStatus::Cancelled->value])
            ->pluck('date')
            ->map(fn($date) => $date->toDateString())
            ->unique()
            ->values();

        return ApiResponse::success(['dates_with_incomplete' => $dates]);
    }
}
