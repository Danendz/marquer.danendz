<?php

namespace App\Http\Controllers\Private\Calendar;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Calendar\CalendarOverviewRequest;
use App\Http\Resources\ApiResponse;
use App\Models\Calendar\Plan;
use App\Models\Tasks\Task;
use App\Services\Calendar\PlanScheduleService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class CalendarOverviewController extends Controller
{
    public function __construct(
        private PlanScheduleService $scheduleService,
    ) {}

    public function __invoke(CalendarOverviewRequest $request): JsonResponse
    {
        $from = Carbon::parse($request->validated('from'));
        $to = Carbon::parse($request->validated('to'));
        $userId = auth()->id();

        $dates = Task::where('user_id', $userId)
            ->whereBetween('date', [$request->validated('from'), $request->validated('to')])
            ->whereNotIn('status', [TaskStatus::Done->value, TaskStatus::Cancelled->value])
            ->pluck('date')
            ->map(fn ($date) => $date->toDateString())
            ->unique()
            ->values();

        $planDates = Plan::where('user_id', $userId)
            ->where('is_active', true)
            ->get()
            ->flatMap(fn (Plan $plan) => $this->scheduleService->getMatchingDatesInRange($plan, $from, $to))
            ->unique()
            ->values();

        return ApiResponse::success([
            'dates_with_incomplete' => $dates,
            'dates_with_plans' => $planDates,
        ]);
    }
}
