<?php

use App\Models\Tasks\Task;
use App\Models\Tasks\TaskCategory;
use App\Models\Tasks\TaskFolder;

beforeEach(function () {
    actingAsJwtUser();
});

test('creates category with color', function () {
    $folder = TaskFolder::factory()->create(['user_id' => 1]);

    $response = $this->postJson('/api/marquer/task-categories', [
        'name' => 'Urgent',
        'task_folder_id' => $folder->id,
        'color' => '#ff0000',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Urgent')
        ->assertJsonPath('data.color', '#ff0000');
});

test('preserves explicitly provided color on category create', function () {
    $folder = TaskFolder::factory()->create(['user_id' => 1]);

    $response = $this->postJson('/api/marquer/task-categories', [
        'name' => 'Default',
        'task_folder_id' => $folder->id,
        'color' => '#fff',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.color', '#fff');
});

test('validates name and task_folder_id required', function () {
    $response = $this->postJson('/api/marquer/task-categories', []);

    $response->assertUnprocessable()
        ->assertJsonStructure(['data' => ['name', 'task_folder_id']]);
});

test('updates category name and color', function () {
    $folder = TaskFolder::factory()->create(['user_id' => 1]);
    $category = TaskCategory::factory()->create(['task_folder_id' => $folder->id, 'user_id' => 1]);

    $response = $this->putJson("/api/marquer/task-categories/{$category->id}", [
        'name' => 'Renamed',
        'task_folder_id' => $folder->id,
        'color' => '#00ff00',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Renamed')
        ->assertJsonPath('data.color', '#00ff00');
});

test('deletes category and cascades tasks', function () {
    $category = TaskCategory::factory()->create(['user_id' => 1]);
    $task = Task::factory()->create(['task_category_id' => $category->id, 'user_id' => 1]);

    $response = $this->deleteJson("/api/marquer/task-categories/{$category->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('task_categories', ['id' => $category->id]);
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});
