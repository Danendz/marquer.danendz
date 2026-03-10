<?php

use App\Models\Calendar\Plan;
use App\Models\Calendar\PlanTaskCompletion;
use App\Models\Countdown;
use App\Models\Tasks\Task;

beforeEach(function () {
    actingAsJwtUser();
});

// ---------------------------------------------------------------------------
// GET /marquer/calendar/week
// ---------------------------------------------------------------------------

describe('CalendarWeekController', function () {
    it('returns tasks grouped by date', function () {
        Task::create(['user_id' => 1, 'name' => 'Mon Task', 'status' => 'draft', 'date' => '2026-03-09']);
        Task::create(['user_id' => 1, 'name' => 'Tue Task', 'status' => 'draft', 'date' => '2026-03-10']);
        Task::create(['user_id' => 1, 'name' => 'Other', 'status' => 'draft', 'date' => '2026-03-16']); // outside range

        $response = $this->getJson('/api/marquer/calendar/week?from=2026-03-09&to=2026-03-15');

        $response->assertOk();
        $data = $response->json('data');
        expect($data['tasks'])->toHaveKey('2026-03-09');
        expect($data['tasks'])->toHaveKey('2026-03-10');
        expect($data['tasks'])->not->toHaveKey('2026-03-16');
        expect($data['tasks']['2026-03-09'])->toHaveCount(1);
        expect($data['tasks']['2026-03-09'][0]['name'])->toBe('Mon Task');
    });

    it('includes start_time and end_time on tasks', function () {
        Task::create(['user_id' => 1, 'name' => 'Timed', 'status' => 'draft', 'date' => '2026-03-09', 'start_time' => '10:00', 'end_time' => '11:30']);

        $response = $this->getJson('/api/marquer/calendar/week?from=2026-03-09&to=2026-03-15');

        $task = $response->json('data.tasks.2026-03-09.0');
        expect($task['start_time'])->toBe('10:00');
        expect($task['end_time'])->toBe('11:30');
    });

    it('returns plan tasks grouped by date with completion state', function () {
        $plan = Plan::create([
            'user_id' => 1,
            'name' => 'Daily Plan',
            'schedule' => ['type' => 'daily'],
            'start_date' => '2026-03-01',
            'end_date' => null,
            'is_active' => true,
        ]);

        $task = $plan->tasks()->create(['name' => 'Morning stretch', 'sort_order' => 0]);

        // Mark completed on 2026-03-09 only
        PlanTaskCompletion::create(['plan_task_id' => $task->id, 'completed_date' => '2026-03-09']);

        $response = $this->getJson('/api/marquer/calendar/week?from=2026-03-09&to=2026-03-10');

        $response->assertOk();
        $planTasks = $response->json('data.plan_tasks');

        expect($planTasks)->toHaveKey('2026-03-09');
        expect($planTasks)->toHaveKey('2026-03-10');

        $mon = collect($planTasks['2026-03-09'])->firstWhere('name', 'Morning stretch');
        $tue = collect($planTasks['2026-03-10'])->firstWhere('name', 'Morning stretch');

        expect($mon['is_completed'])->toBeTrue();
        expect($tue['is_completed'])->toBeFalse();
        expect($mon['plan_id'])->toBe($plan->id);
    });

    it('includes start_time and end_time on plan tasks', function () {
        $plan = Plan::create([
            'user_id' => 1,
            'name' => 'Timed Plan',
            'schedule' => ['type' => 'daily'],
            'start_date' => '2026-03-01',
            'end_date' => null,
            'is_active' => true,
        ]);

        $plan->tasks()->create(['name' => 'Timed Task', 'sort_order' => 0, 'start_time' => '09:00', 'end_time' => '10:00']);

        $response = $this->getJson('/api/marquer/calendar/week?from=2026-03-09&to=2026-03-09');

        $task = $response->json('data.plan_tasks.2026-03-09.0');
        expect($task['start_time'])->toBe('09:00');
        expect($task['end_time'])->toBe('10:00');
    });

    it('returns countdowns in range', function () {
        Countdown::create(['user_id' => 1, 'name' => 'Birthday', 'target_date' => '2026-03-11', 'bg_image' => '', 'is_pinned' => false]);
        Countdown::create(['user_id' => 1, 'name' => 'Future', 'target_date' => '2026-04-01', 'bg_image' => '', 'is_pinned' => false]); // outside

        $response = $this->getJson('/api/marquer/calendar/week?from=2026-03-09&to=2026-03-15');

        $countdowns = $response->json('data.countdowns');
        expect($countdowns)->toHaveKey('2026-03-11');
        expect($countdowns['2026-03-11'])->toHaveCount(1);
        expect($countdowns['2026-03-11'][0]['name'])->toBe('Birthday');
    });

    it('rejects range exceeding 7 days', function () {
        $response = $this->getJson('/api/marquer/calendar/week?from=2026-03-09&to=2026-03-17');

        $response->assertUnprocessable();
    });

    it('requires from and to params', function () {
        $response = $this->getJson('/api/marquer/calendar/week');
        $response->assertUnprocessable();
    });

    it('does not return tasks for other users', function () {
        Task::create(['user_id' => 2, 'name' => 'Other user', 'status' => 'draft', 'date' => '2026-03-09']);

        $response = $this->getJson('/api/marquer/calendar/week?from=2026-03-09&to=2026-03-15');

        $data = $response->json('data');
        expect($data['tasks'])->toBeEmpty();
    });

    it('does not return inactive plans', function () {
        $plan = Plan::create([
            'user_id' => 1,
            'name' => 'Inactive Plan',
            'schedule' => ['type' => 'daily'],
            'start_date' => '2026-03-01',
            'end_date' => null,
            'is_active' => false,
        ]);

        $plan->tasks()->create(['name' => 'Hidden', 'sort_order' => 0]);

        $response = $this->getJson('/api/marquer/calendar/week?from=2026-03-09&to=2026-03-15');

        $planTasks = $response->json('data.plan_tasks');
        expect($planTasks)->toBeEmpty();
    });
});
