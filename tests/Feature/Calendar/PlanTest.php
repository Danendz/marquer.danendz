<?php

use App\Models\Calendar\Plan;
use App\Models\Calendar\PlanTask;
use App\Models\Calendar\PlanTaskCompletion;
use App\Services\Calendar\PlanScheduleService;
use Carbon\Carbon;

beforeEach(function () {
    actingAsJwtUser();
});

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function makePlan(array $attrs = []): Plan
{
    $plan = Plan::create(array_merge([
        'user_id' => 1,
        'name' => 'Test Plan',
        'schedule' => ['type' => 'daily'],
        'start_date' => '2026-01-01',
        'end_date' => null,
        'is_active' => true,
    ], $attrs));

    return $plan;
}

function makePlanWithTasks(array $planAttrs = [], array $tasks = []): Plan
{
    $plan = makePlan($planAttrs);

    if (empty($tasks)) {
        $tasks = [['name' => 'Task A', 'sort_order' => 0]];
    }

    foreach ($tasks as $task) {
        $plan->tasks()->create($task);
    }

    return $plan->load('tasks');
}

// ---------------------------------------------------------------------------
// Schedule matching — unit tests
// ---------------------------------------------------------------------------

describe('PlanScheduleService::matchesDate', function () {
    $svc = fn () => new PlanScheduleService();

    test('daily matches any date within range', function () use ($svc) {
        $start = Carbon::parse('2026-01-01');
        expect($svc()->matchesDate(['type' => 'daily'], $start, null, Carbon::parse('2026-06-15')))->toBeTrue();
        expect($svc()->matchesDate(['type' => 'daily'], $start, null, Carbon::parse('2026-01-01')))->toBeTrue();
    });

    test('daily does not match before start_date', function () use ($svc) {
        $start = Carbon::parse('2026-03-01');
        expect($svc()->matchesDate(['type' => 'daily'], $start, null, Carbon::parse('2026-02-28')))->toBeFalse();
    });

    test('daily does not match after end_date', function () use ($svc) {
        $start = Carbon::parse('2026-01-01');
        $end = Carbon::parse('2026-01-31');
        expect($svc()->matchesDate(['type' => 'daily'], $start, $end, Carbon::parse('2026-02-01')))->toBeFalse();
        expect($svc()->matchesDate(['type' => 'daily'], $start, $end, Carbon::parse('2026-01-31')))->toBeTrue();
    });

    test('weekly matches correct ISO days (0=Mon..6=Sun)', function () use ($svc) {
        $start = Carbon::parse('2026-01-01');
        // 2026-01-05 is a Monday (dayOfWeekIso=1 → index 0)
        expect($svc()->matchesDate(['type' => 'weekly', 'days' => [0, 2]], $start, null, Carbon::parse('2026-01-05')))->toBeTrue();
        // 2026-01-06 is a Tuesday (index 1) — not in [0,2]
        expect($svc()->matchesDate(['type' => 'weekly', 'days' => [0, 2]], $start, null, Carbon::parse('2026-01-06')))->toBeFalse();
        // 2026-01-07 is a Wednesday (index 2)
        expect($svc()->matchesDate(['type' => 'weekly', 'days' => [0, 2]], $start, null, Carbon::parse('2026-01-07')))->toBeTrue();
    });

    test('interval matches every N days from start', function () use ($svc) {
        $start = Carbon::parse('2026-01-01');
        expect($svc()->matchesDate(['type' => 'interval', 'every' => 3], $start, null, Carbon::parse('2026-01-01')))->toBeTrue();
        expect($svc()->matchesDate(['type' => 'interval', 'every' => 3], $start, null, Carbon::parse('2026-01-04')))->toBeTrue();
        expect($svc()->matchesDate(['type' => 'interval', 'every' => 3], $start, null, Carbon::parse('2026-01-07')))->toBeTrue();
        expect($svc()->matchesDate(['type' => 'interval', 'every' => 3], $start, null, Carbon::parse('2026-01-02')))->toBeFalse();
        expect($svc()->matchesDate(['type' => 'interval', 'every' => 3], $start, null, Carbon::parse('2026-01-03')))->toBeFalse();
    });

    test('monthly_dates matches specific days of month', function () use ($svc) {
        $start = Carbon::parse('2026-01-01');
        expect($svc()->matchesDate(['type' => 'monthly_dates', 'days' => [1, 15]], $start, null, Carbon::parse('2026-01-01')))->toBeTrue();
        expect($svc()->matchesDate(['type' => 'monthly_dates', 'days' => [1, 15]], $start, null, Carbon::parse('2026-02-15')))->toBeTrue();
        expect($svc()->matchesDate(['type' => 'monthly_dates', 'days' => [1, 15]], $start, null, Carbon::parse('2026-03-10')))->toBeFalse();
    });

    test('monthly_weekday matches first/last weekday of month', function () use ($svc) {
        $start = Carbon::parse('2026-01-01');
        // First Thursday of March 2026 = March 5 (dayOfWeekIso=4, index 3)
        expect($svc()->matchesDate(['type' => 'monthly_weekday', 'week' => 1, 'weekday' => 3], $start, null, Carbon::parse('2026-03-05')))->toBeTrue();
        expect($svc()->matchesDate(['type' => 'monthly_weekday', 'week' => 1, 'weekday' => 3], $start, null, Carbon::parse('2026-03-12')))->toBeFalse();

        // Last Thursday of March 2026 = March 26
        expect($svc()->matchesDate(['type' => 'monthly_weekday', 'week' => -1, 'weekday' => 3], $start, null, Carbon::parse('2026-03-26')))->toBeTrue();
        expect($svc()->matchesDate(['type' => 'monthly_weekday', 'week' => -1, 'weekday' => 3], $start, null, Carbon::parse('2026-03-19')))->toBeFalse();
    });
});

// ---------------------------------------------------------------------------
// CRUD
// ---------------------------------------------------------------------------

test('lists only own plans with tasks', function () {
    makePlanWithTasks(['name' => 'My Plan']);
    makePlanWithTasks(['user_id' => 2, 'name' => 'Other Plan']);

    $response = $this->getJson('/api/marquer/calendar/plans');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'My Plan')
        ->assertJsonStructure(['data' => [['id', 'name', 'schedule', 'start_date', 'end_date', 'is_active', 'tasks']]]);
});

test('creates plan with tasks', function () {
    $response = $this->postJson('/api/marquer/calendar/plans', [
        'name' => 'Morning Routine',
        'schedule' => ['type' => 'daily'],
        'start_date' => '2026-01-01',
        'end_date' => null,
        'tasks' => [
            ['name' => 'Meditation', 'sort_order' => 0],
            ['name' => 'Exercise', 'sort_order' => 1],
        ],
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Morning Routine')
        ->assertJsonPath('data.is_active', true)
        ->assertJsonCount(2, 'data.tasks');

    $this->assertDatabaseHas('plans', ['name' => 'Morning Routine', 'user_id' => 1]);
    $this->assertDatabaseCount('plan_tasks', 2);
});

test('validates name required on create', function () {
    $response = $this->postJson('/api/marquer/calendar/plans', [
        'schedule' => ['type' => 'daily'],
        'start_date' => '2026-01-01',
        'tasks' => [['name' => 'Task', 'sort_order' => 0]],
    ]);

    $response->assertUnprocessable()->assertJsonStructure(['data' => ['name']]);
});

test('validates tasks required on create', function () {
    $response = $this->postJson('/api/marquer/calendar/plans', [
        'name' => 'Plan',
        'schedule' => ['type' => 'daily'],
        'start_date' => '2026-01-01',
        'tasks' => [],
    ]);

    $response->assertUnprocessable()->assertJsonStructure(['data' => ['tasks']]);
});

test('shows a single plan', function () {
    $plan = makePlanWithTasks(['name' => 'Focus Plan']);

    $response = $this->getJson("/api/marquer/calendar/plans/{$plan->id}");

    $response->assertOk()->assertJsonPath('data.name', 'Focus Plan');
});

test('returns 404 for another users plan', function () {
    $plan = makePlanWithTasks(['user_id' => 2]);

    $this->getJson("/api/marquer/calendar/plans/{$plan->id}")->assertNotFound();
    $this->putJson("/api/marquer/calendar/plans/{$plan->id}", [
        'name' => 'x', 'schedule' => ['type' => 'daily'], 'start_date' => '2026-01-01',
        'tasks' => [['name' => 'T', 'sort_order' => 0]],
    ])->assertNotFound();
    $this->deleteJson("/api/marquer/calendar/plans/{$plan->id}")->assertNotFound();
});

test('updates plan name and syncs tasks', function () {
    $plan = makePlanWithTasks(tasks: [
        ['name' => 'Old Task', 'sort_order' => 0],
        ['name' => 'Keep Task', 'sort_order' => 1],
    ]);
    $keepId = $plan->tasks->last()->id;

    $response = $this->putJson("/api/marquer/calendar/plans/{$plan->id}", [
        'name' => 'Updated Plan',
        'schedule' => ['type' => 'daily'],
        'start_date' => '2026-01-01',
        'tasks' => [
            ['id' => $keepId, 'name' => 'Keep Task', 'sort_order' => 0],
            ['name' => 'New Task', 'sort_order' => 1],
        ],
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Updated Plan')
        ->assertJsonCount(2, 'data.tasks');

    $this->assertDatabaseMissing('plan_tasks', ['name' => 'Old Task']);
    $this->assertDatabaseHas('plan_tasks', ['name' => 'New Task']);
    $this->assertDatabaseHas('plan_tasks', ['id' => $keepId, 'name' => 'Keep Task']);
});

test('deletes own plan and cascades tasks', function () {
    $plan = makePlanWithTasks();
    $taskId = $plan->tasks->first()->id;

    $this->deleteJson("/api/marquer/calendar/plans/{$plan->id}")->assertOk();

    $this->assertDatabaseMissing('plans', ['id' => $plan->id]);
    $this->assertDatabaseMissing('plan_tasks', ['id' => $taskId]);
});

// ---------------------------------------------------------------------------
// forDate
// ---------------------------------------------------------------------------

test('for-date returns plans matching the date', function () {
    // daily plan starting 2026-01-01
    makePlanWithTasks(['name' => 'Daily Plan', 'schedule' => ['type' => 'daily'], 'start_date' => '2026-01-01']);
    // weekly on Monday (index 0); 2026-03-09 is a Monday
    makePlanWithTasks(['name' => 'Monday Plan', 'schedule' => ['type' => 'weekly', 'days' => [0]], 'start_date' => '2026-01-01']);
    // weekly on Tuesday — should NOT appear on Monday
    makePlanWithTasks(['name' => 'Tuesday Plan', 'schedule' => ['type' => 'weekly', 'days' => [1]], 'start_date' => '2026-01-01']);

    $response = $this->getJson('/api/marquer/calendar/plans/for-date?date=2026-03-09');

    $response->assertOk()->assertJsonCount(2, 'data');
    $names = collect($response->json('data'))->pluck('name')->all();
    expect($names)->toContain('Daily Plan')->toContain('Monday Plan');
});

test('for-date includes is_completed per task', function () {
    $plan = makePlanWithTasks(tasks: [
        ['name' => 'Task A', 'sort_order' => 0],
        ['name' => 'Task B', 'sort_order' => 1],
    ]);
    $taskA = $plan->tasks->firstWhere('name', 'Task A');

    PlanTaskCompletion::create(['plan_task_id' => $taskA->id, 'completed_date' => '2026-03-09']);

    $response = $this->getJson('/api/marquer/calendar/plans/for-date?date=2026-03-09');

    $response->assertOk();
    $tasks = collect($response->json('data.0.tasks'));
    expect($tasks->firstWhere('name', 'Task A')['is_completed'])->toBeTrue();
    expect($tasks->firstWhere('name', 'Task B')['is_completed'])->toBeFalse();
});

test('for-date does not include inactive plans', function () {
    makePlanWithTasks(['name' => 'Active', 'is_active' => true]);
    makePlanWithTasks(['name' => 'Inactive', 'is_active' => false]);

    $response = $this->getJson('/api/marquer/calendar/plans/for-date?date=2026-03-09');

    $response->assertOk()->assertJsonCount(1, 'data');
    expect($response->json('data.0.name'))->toBe('Active');
});

// ---------------------------------------------------------------------------
// Toggle completion
// ---------------------------------------------------------------------------

test('toggle creates completion and returns is_completed=true', function () {
    $plan = makePlanWithTasks();
    $task = $plan->tasks->first();

    $response = $this->postJson("/api/marquer/calendar/plan-tasks/{$task->id}/toggle?date=2026-03-09");

    $response->assertOk()->assertJsonPath('data.is_completed', true);
    $this->assertDatabaseHas('plan_task_completions', ['plan_task_id' => $task->id, 'completed_date' => '2026-03-09']);
});

test('toggle deletes existing completion and returns is_completed=false', function () {
    $plan = makePlanWithTasks();
    $task = $plan->tasks->first();
    PlanTaskCompletion::create(['plan_task_id' => $task->id, 'completed_date' => '2026-03-09']);

    $response = $this->postJson("/api/marquer/calendar/plan-tasks/{$task->id}/toggle?date=2026-03-09");

    $response->assertOk()->assertJsonPath('data.is_completed', false);
    $this->assertDatabaseMissing('plan_task_completions', ['plan_task_id' => $task->id, 'completed_date' => '2026-03-09']);
});

test('toggle returns 404 for task belonging to another user', function () {
    $plan = makePlanWithTasks(['user_id' => 2]);
    $task = $plan->tasks->first();

    $this->postJson("/api/marquer/calendar/plan-tasks/{$task->id}/toggle?date=2026-03-09")->assertNotFound();
});

test('completions on different dates are independent', function () {
    $plan = makePlanWithTasks();
    $task = $plan->tasks->first();

    $this->postJson("/api/marquer/calendar/plan-tasks/{$task->id}/toggle?date=2026-03-09")->assertOk();
    $this->postJson("/api/marquer/calendar/plan-tasks/{$task->id}/toggle?date=2026-03-10")->assertOk();

    $this->assertDatabaseHas('plan_task_completions', ['plan_task_id' => $task->id, 'completed_date' => '2026-03-09']);
    $this->assertDatabaseHas('plan_task_completions', ['plan_task_id' => $task->id, 'completed_date' => '2026-03-10']);
});

// ---------------------------------------------------------------------------
// Overview includes plan dates
// ---------------------------------------------------------------------------

test('calendar overview includes plan_tasks', function () {
    makePlanWithTasks([
        'schedule' => ['type' => 'weekly', 'days' => [0]], // Mondays
        'start_date' => '2026-03-01',
    ]);

    $response = $this->getJson('/api/marquer/calendar/overview?from=2026-03-09&to=2026-03-15');

    $response->assertOk()->assertJsonStructure(['data' => ['tasks', 'plan_tasks']]);
    $datesWithPlans = $response->json('data.plan_tasks');
    expect($datesWithPlans)->toContain('2026-03-09'); // Monday
    expect($datesWithPlans)->not->toContain('2026-03-10'); // Tuesday
});
