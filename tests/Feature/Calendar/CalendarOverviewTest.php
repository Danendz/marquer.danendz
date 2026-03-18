<?php

use App\Enums\TaskStatus;
use App\Models\Calendar\Plan;
use App\Models\Tasks\Task;

beforeEach(function () {
    actingAsJwtUser();
});

describe('CalendarOverviewController', function () {
    it('returns dates with incomplete tasks', function () {
        Task::create(['user_id' => 1, 'name' => 'Draft', 'status' => 'draft', 'date' => '2026-03-09']);
        Task::create(['user_id' => 1, 'name' => 'InProgress', 'status' => 'progress', 'date' => '2026-03-10']);
        Task::create(['user_id' => 1, 'name' => 'Done', 'status' => 'done', 'date' => '2026-03-11']);
        Task::create(['user_id' => 1, 'name' => 'Cancelled', 'status' => 'cancelled', 'date' => '2026-03-12']);

        $response = $this->getJson('/api/marquer/calendar/overview?from=2026-03-09&to=2026-03-15');

        $response->assertOk();
        $tasks = $response->json('data.tasks');
        expect($tasks)->toContain('2026-03-09')
            ->toContain('2026-03-10')
            ->not->toContain('2026-03-11')
            ->not->toContain('2026-03-12');
    });

    it('returns empty tasks when all are done or cancelled', function () {
        Task::create(['user_id' => 1, 'name' => 'Done', 'status' => 'done', 'date' => '2026-03-09']);
        Task::create(['user_id' => 1, 'name' => 'Cancelled', 'status' => 'cancelled', 'date' => '2026-03-10']);

        $response = $this->getJson('/api/marquer/calendar/overview?from=2026-03-09&to=2026-03-15');

        $response->assertOk();
        expect($response->json('data.tasks'))->toBeEmpty();
    });

    it('returns dates with active plans', function () {
        Plan::create([
            'user_id' => 1,
            'name' => 'Daily Plan',
            'schedule' => ['type' => 'daily'],
            'start_date' => '2026-03-01',
            'end_date' => null,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/marquer/calendar/overview?from=2026-03-09&to=2026-03-11');

        $response->assertOk();
        $planTasks = $response->json('data.plan_tasks');
        expect($planTasks)->toContain('2026-03-09')
            ->toContain('2026-03-10')
            ->toContain('2026-03-11');
    });

    it('does not include inactive plans', function () {
        Plan::create([
            'user_id' => 1,
            'name' => 'Inactive',
            'schedule' => ['type' => 'daily'],
            'start_date' => '2026-03-01',
            'end_date' => null,
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/marquer/calendar/overview?from=2026-03-09&to=2026-03-15');

        $response->assertOk();
        expect($response->json('data.plan_tasks'))->toBeEmpty();
    });

    it('returns plan_tasks for weekly schedule', function () {
        Plan::create([
            'user_id' => 1,
            'name' => 'Monday Plan',
            'schedule' => ['type' => 'weekly', 'days' => [0]],
            'start_date' => '2026-03-01',
            'end_date' => null,
            'is_active' => true,
        ]);

        // 2026-03-09 is Monday, 2026-03-15 is Sunday
        $response = $this->getJson('/api/marquer/calendar/overview?from=2026-03-09&to=2026-03-15');

        $response->assertOk();
        $planTasks = $response->json('data.plan_tasks');
        expect($planTasks)->toContain('2026-03-09')
            ->not->toContain('2026-03-10')
            ->not->toContain('2026-03-11');
    });

    it('does not include other users data', function () {
        Task::create(['user_id' => 2, 'name' => 'Other', 'status' => 'draft', 'date' => '2026-03-09']);
        Plan::create([
            'user_id' => 2,
            'name' => 'Other Plan',
            'schedule' => ['type' => 'daily'],
            'start_date' => '2026-03-01',
            'end_date' => null,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/marquer/calendar/overview?from=2026-03-09&to=2026-03-15');

        $response->assertOk();
        expect($response->json('data.tasks'))->toBeEmpty();
        expect($response->json('data.plan_tasks'))->toBeEmpty();
    });

    it('requires from and to params', function () {
        $this->getJson('/api/marquer/calendar/overview')->assertUnprocessable();
    });
});
