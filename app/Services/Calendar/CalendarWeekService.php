<?php

namespace App\Services\Calendar;

use App\Http\Resources\Calendar\CountdownResource;
use App\Http\Resources\Tasks\TaskResource;
use App\Models\Calendar\Plan;
use App\Models\Calendar\PlanTaskCompletion;
use App\Models\Countdown;
use App\Models\Tasks\Task;
use Carbon\Carbon;

readonly class CalendarWeekService
{
    public function __construct(
        private PlanScheduleService $scheduleService,
    ) {}

    public function assembleWeekView(int $userId, Carbon $from, Carbon $to): array
    {
        $fromDate = $from->toDateString();
        $toDate = $to->toDateString();

        // Tasks grouped by date
        $tasks = Task::where('user_id', $userId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->get()
            ->groupBy(fn ($task) => $task->date->toDateString())
            ->map(fn ($group) => TaskResource::collection($group)->resolve())
            ->toArray();

        // Plan tasks grouped by date
        $plans = Plan::where('user_id', $userId)
            ->where('is_active', true)
            ->with(['tasks'])
            ->get();

        // Collect all task IDs and matching dates from all plans first
        $allTaskIds = [];
        $allMatchingDates = [];
        $planMatchingDates = []; // plan->id => array of date strings

        foreach ($plans as $plan) {
            $matchingDates = $this->scheduleService->getMatchingDatesInRange($plan, $from, $to);
            if (empty($matchingDates)) {
                continue;
            }
            $planMatchingDates[$plan->id] = $matchingDates;
            $allTaskIds = array_merge($allTaskIds, $plan->tasks->pluck('id')->all());
            $allMatchingDates = array_merge($allMatchingDates, $matchingDates);
        }

        // Single query for all completions
        $completionsByTaskId = collect();
        if (!empty($allTaskIds)) {
            $completionsByTaskId = PlanTaskCompletion::whereIn('plan_task_id', array_unique($allTaskIds))
                ->whereIn('completed_date', array_unique($allMatchingDates))
                ->get()
                ->groupBy('plan_task_id');
        }

        // Build $planTasks using $planMatchingDates and $completionsByTaskId
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

        // Countdowns grouped by date
        $countdowns = Countdown::where('user_id', $userId)
            ->whereBetween('target_date', [$fromDate, $toDate])
            ->get()
            ->groupBy(fn ($c) => $c->target_date->toDateString())
            ->map(fn ($group) => CountdownResource::collection($group)->resolve())
            ->toArray();

        return [
            'tasks' => $tasks,
            'plan_tasks' => $planTasks,
            'countdowns' => $countdowns,
        ];
    }
}
