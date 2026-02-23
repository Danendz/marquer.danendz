<?php

use App\Models\Tasks\Task;
use App\Models\Tasks\TaskCategory;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    actingAsJwtUser();
});

test('returns all tasks for user', function () {
    $category = TaskCategory::factory()->create(['user_id' => 1]);
    Task::factory()->count(3)->create(['task_category_id' => $category->id, 'user_id' => 1]);

    $response = $this->getJson('/api/marquer/tasks');

    $response->assertOk()
        ->assertJsonCount(3, 'data');
});

test('filters tasks by category', function () {
    $cat1 = TaskCategory::factory()->create(['user_id' => 1]);
    $cat2 = TaskCategory::factory()->create(['user_id' => 1]);
    Task::factory()->count(2)->create(['task_category_id' => $cat1->id, 'user_id' => 1]);
    Task::factory()->create(['task_category_id' => $cat2->id, 'user_id' => 1]);

    $response = $this->getJson("/api/marquer/tasks?task_category_id={$cat1->id}");

    $response->assertOk()
        ->assertJsonCount(2, 'data');
});

test('filters tasks by status', function () {
    $category = TaskCategory::factory()->create(['user_id' => 1]);
    Task::factory()->create(['task_category_id' => $category->id, 'user_id' => 1, 'status' => 'draft']);
    Task::factory()->create(['task_category_id' => $category->id, 'user_id' => 1, 'status' => 'cancelled']);

    $response = $this->getJson('/api/marquer/tasks?status=cancelled');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.status', 'cancelled');
});

test('creates task with draft status', function () {
    $category = TaskCategory::factory()->create(['user_id' => 1]);

    $response = $this->postJson('/api/marquer/tasks', [
        'name' => 'Buy milk',
        'task_category_id' => $category->id,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Buy milk')
        ->assertJsonPath('data.status', 'draft');
});

test('validates name and task_category_id required on create', function () {
    $response = $this->postJson('/api/marquer/tasks', []);

    $response->assertUnprocessable()
        ->assertJsonStructure(['data' => ['name', 'task_category_id']]);
});

test('updates task name', function () {
    $task = Task::factory()->create(['user_id' => 1]);

    $response = $this->putJson("/api/marquer/tasks/{$task->id}", [
        'name' => 'Updated task',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Updated task');
});

test('updates task status only', function () {
    $task = Task::factory()->create(['user_id' => 1, 'status' => 'draft']);

    $response = $this->putJson("/api/marquer/tasks/{$task->id}", [
        'status' => 'done',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.status', 'done');
    expect($task->fresh()->name)->toBe($task->name);
});

test('returns 404 for other user task on update', function () {
    $task = Task::factory()->create(['user_id' => 999]);

    $response = $this->putJson("/api/marquer/tasks/{$task->id}", [
        'name' => 'Hacked',
    ]);

    $response->assertNotFound();
});

test('deletes task', function () {
    $task = Task::factory()->create(['user_id' => 1]);

    $response = $this->deleteJson("/api/marquer/tasks/{$task->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('unauthenticated returns 401', function () {
    // Reset the guard to clear the user set by beforeEach
    Auth::forgetGuards();

    $response = $this->getJson('/api/marquer/tasks');

    $response->assertUnauthorized();
});

test('returns task_category_id in response', function () {
    $category = TaskCategory::factory()->create(['user_id' => 1]);
    Task::factory()->create(['task_category_id' => $category->id, 'user_id' => 1]);

    $response = $this->getJson('/api/marquer/tasks');

    $response->assertOk()
        ->assertJsonPath('data.0.task_category_id', $category->id);
});
