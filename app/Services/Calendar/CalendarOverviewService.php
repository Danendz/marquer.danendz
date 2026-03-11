<?php

namespace App\Services\Calendar;

use App\Enums\TaskStatus;
use App\Models\Calendar\Plan;
use App\Models\Tasks\Task;
use Carbon\Carbon;

readonly class CalendarOverviewService
{
    public function __construct(
        private PlanScheduleService $scheduleService,
    ) {}

    public function getOverview(int $userId, Carbon $from, Carbon $to): array
    {
        $fromDate = $from->toDateString();
        $toDate = $to->toDateString();

        $dates = Task::where('user_id', $userId)
            ->whereBetween('date', [$fromDate, $toDate])
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

        return [
            'dates_with_incomplete' => $dates,
            'dates_with_plans' => $planDates,
        ];
    }
}
