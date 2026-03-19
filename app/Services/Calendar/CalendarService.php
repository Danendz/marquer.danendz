<?php

namespace App\Services\Calendar;

use App\Enums\TaskStatus;
use App\Models\Calendar\Plan;
use App\Models\Calendar\PlanTaskCompletion;
use App\Models\Countdown;
use App\Models\Tasks\Task;
use Carbon\Carbon;

readonly class CalendarService
{
    public function __construct(
        private PlanScheduleService $scheduleService,
    ) {}

    public function getOverview(int $userId, Carbon $from, Carbon $to): array
    {
        $fromDate = $from->toDateString();
        $toDate = $to->toDateString();

        $tasks = Task::where('user_id', $userId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->whereNotIn('status', [TaskStatus::Done->value, TaskStatus::Cancelled->value])
            ->pluck('date')
            ->map(fn ($date) => $date->toDateString())
            ->unique()
            ->values();

        $planTasks = Plan::where('user_id', $userId)
            ->where('is_active', true)
            ->where('start_date', '<=', $toDate)
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $fromDate))
            ->get()
            ->flatMap(fn (Plan $plan) => $this->scheduleService->getMatchingDatesInRange($plan, $from, $to))
            ->unique()
            ->values();

        return [
            'tasks' => $tasks,
            'plan_tasks' => $planTasks,
        ];
    }

    public function getWeekView(int $userId, Carbon $from, Carbon $to): array
    {
        $fromDate = $from->toDateString();
        $toDate = $to->toDateString();

        $tasks = Task::where('user_id', $userId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->get()
            ->groupBy(fn ($task) => $task->date->toDateString());

        $planTasks = $this->getPlanTasksForRange($userId, $fromDate, $toDate, $from, $to);

        $countdowns = Countdown::where('user_id', $userId)
            ->whereBetween('target_date', [$fromDate, $toDate])
            ->get()
            ->groupBy(fn ($c) => $c->target_date->toDateString());

        return [
            'tasks' => $tasks,
            'plan_tasks' => $planTasks,
            'countdowns' => $countdowns,
        ];
    }

    private function getPlanTasksForRange(int $userId, string $fromDate, string $toDate, Carbon $from, Carbon $to): array
    {
        $plans = Plan::where('user_id', $userId)
            ->where('is_active', true)
            ->where('start_date', '<=', $toDate)
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $fromDate))
            ->with(['tasks'])
            ->get();

        $allTaskIds = [];
        $allMatchingDates = [];
        $planMatchingDates = [];

        foreach ($plans as $plan) {
            $matchingDates = $this->scheduleService->getMatchingDatesInRange($plan, $from, $to);
            if (empty($matchingDates)) {
                continue;
            }
            $planMatchingDates[$plan->id] = $matchingDates;
            $allTaskIds = array_merge($allTaskIds, $plan->tasks->pluck('id')->all());
            $allMatchingDates = array_merge($allMatchingDates, $matchingDates);
        }

        $completionsByTaskId = collect();
        if (!empty($allTaskIds)) {
            $completionsByTaskId = PlanTaskCompletion::whereIn('plan_task_id', array_unique($allTaskIds))
                ->whereIn('completed_date', array_unique($allMatchingDates))
                ->get()
                ->groupBy('plan_task_id');
        }

        $planTasks = [];
        foreach ($plans as $plan) {
            if (!isset($planMatchingDates[$plan->id])) {
                continue;
            }

            foreach ($planMatchingDates[$plan->id] as $date) {
                if (!isset($planTasks[$date])) {
                    $planTasks[$date] = [];
                }

                foreach ($plan->tasks->sortBy('sort_order') as $task) {
                    $isCompleted = isset($completionsByTaskId[$task->id]) &&
                        $completionsByTaskId[$task->id]->contains(
                            fn ($c) => $c->completed_date->toDateString() === $date
                        );

                    $planTasks[$date][] = [
                        'id' => $task->id,
                        'name' => $task->name,
                        'sort_order' => $task->sort_order,
                        'start_time' => $task->start_time,
                        'end_time' => $task->end_time,
                        'is_completed' => $isCompleted,
                        'plan_id' => $plan->id,
                        'plan_name' => $plan->name,
                        'plan_color' => $plan->color,
                    ];
                }
            }
        }

        return $planTasks;
    }
}
