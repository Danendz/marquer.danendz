<?php

namespace App\Http\Controllers\Private\Calendar;

use App\Http\Controllers\Controller;
use App\Http\Requests\Calendar\CalendarWeekRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\Calendar\CountdownResource;
use App\Http\Resources\Tasks\TaskResource;
use App\Models\Calendar\Plan;
use App\Models\Countdown;
use App\Models\Tasks\Task;
use App\Services\Calendar\PlanScheduleService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class CalendarWeekController extends Controller
{
    public function __construct(
        private PlanScheduleService $scheduleService,
    ) {}

    public function __invoke(CalendarWeekRequest $request): JsonResponse
    {
        $userId = auth()->id();
        $from = Carbon::parse($request->input('from'))->startOfDay();
        $to = Carbon::parse($request->input('to'))->endOfDay();
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

        $planTasks = [];
        foreach ($plans as $plan) {
            $matchingDates = $this->scheduleService->getMatchingDatesInRange($plan, $from, $to);

            if (empty($matchingDates)) {
                continue;
            }

            // Load completions for these specific dates
            $planTaskIds = $plan->tasks->pluck('id')->toArray();
            $completions = \App\Models\Calendar\PlanTaskCompletion::whereIn('plan_task_id', $planTaskIds)
                ->whereIn('completed_date', $matchingDates)
                ->get()
                ->groupBy('plan_task_id');

            foreach ($matchingDates as $date) {
                if (!isset($planTasks[$date])) {
                    $planTasks[$date] = [];
                }

                foreach ($plan->tasks->sortBy('sort_order') as $task) {
                    $isCompleted = isset($completions[$task->id]) &&
                        $completions[$task->id]->contains(
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
                    ];
                }
            }
        }

        // Countdowns in range
        $countdowns = Countdown::where('user_id', $userId)
            ->whereBetween('target_date', [$fromDate, $toDate])
            ->get();

        return ApiResponse::success([
            'tasks' => $tasks,
            'plan_tasks' => $planTasks,
            'countdowns' => CountdownResource::collection($countdowns)->resolve(),
        ]);
    }
}
