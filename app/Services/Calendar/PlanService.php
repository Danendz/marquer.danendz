<?php

namespace App\Services\Calendar;

use App\Models\Calendar\Plan;
use App\Models\Calendar\PlanTask;
use App\Models\Calendar\PlanTaskCompletion;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

readonly class PlanService
{
    public function __construct(
        private PlanScheduleService $scheduleService,
    ) {}

    public function list(int $userId): Collection
    {
        return Plan::where('user_id', $userId)
            ->orderBy('created_at')
            ->with(['tasks'])
            ->get();
    }

    public function create(int $userId, array $data): Plan
    {
        return DB::transaction(function () use ($userId, $data) {
            $plan = Plan::create([
                'user_id' => $userId,
                'name' => $data['name'],
                'schedule' => $data['schedule'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'] ?? null,
                'is_active' => true,
            ]);

            foreach ($data['tasks'] as $taskData) {
                $plan->tasks()->create([
                    'name' => $taskData['name'],
                    'sort_order' => $taskData['sort_order'] ?? 0,
                    'start_time' => $taskData['start_time'] ?? null,
                    'end_time' => $taskData['end_time'] ?? null,
                ]);
            }

            return $plan->load('tasks');
        });
    }

    public function update(Plan $plan, array $data): Plan
    {
        return DB::transaction(function () use ($plan, $data) {
            $plan->update([
                'name' => $data['name'],
                'schedule' => $data['schedule'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'] ?? null,
                'is_active' => $data['is_active'] ?? $plan->is_active,
            ]);

            $incomingTasks = $data['tasks'] ?? [];
            $incomingIds = collect($incomingTasks)->pluck('id')->filter()->all();

            $plan->tasks()->whereNotIn('id', $incomingIds)->delete();

            foreach ($incomingTasks as $taskData) {
                if (!empty($taskData['id'])) {
                    $plan->tasks()->where('id', $taskData['id'])->update([
                        'name' => $taskData['name'],
                        'sort_order' => $taskData['sort_order'] ?? 0,
                        'start_time' => $taskData['start_time'] ?? null,
                        'end_time' => $taskData['end_time'] ?? null,
                    ]);
                } else {
                    $plan->tasks()->create([
                        'name' => $taskData['name'],
                        'sort_order' => $taskData['sort_order'] ?? 0,
                        'start_time' => $taskData['start_time'] ?? null,
                        'end_time' => $taskData['end_time'] ?? null,
                    ]);
                }
            }

            return $plan->load('tasks');
        });
    }

    public function delete(Plan $plan): void
    {
        $plan->delete();
    }

    public function getPlansForDate(int $userId, string $date): Collection
    {
        $carbon = Carbon::parse($date)->startOfDay();

        return Plan::where('user_id', $userId)
            ->where('is_active', true)
            ->with(['tasks' => function ($q) use ($date) {
                $q->orderBy('sort_order')
                    ->with(['completions' => function ($q) use ($date) {
                        $q->where('completed_date', $date);
                    }]);
            }])
            ->get()
            ->filter(function (Plan $plan) use ($carbon) {
                return $this->scheduleService->matchesDate(
                    $plan->schedule,
                    $plan->start_date,
                    $plan->end_date,
                    $carbon->copy(),
                );
            })
            ->values();
    }

    public function toggleTaskCompletion(PlanTask $planTask, string $date): bool
    {
        $existing = PlanTaskCompletion::where('plan_task_id', $planTask->id)
            ->where('completed_date', $date)
            ->first();

        if ($existing) {
            $existing->delete();
            return false;
        }

        PlanTaskCompletion::create([
            'plan_task_id' => $planTask->id,
            'completed_date' => $date,
        ]);

        return true;
    }
}
