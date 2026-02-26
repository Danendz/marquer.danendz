<?php

use App\Models\Tasks\TaskCategory;
use App\Models\Tasks\TaskFolder;

beforeEach(function () {
    actingAsJwtUser();
});

test('returns user folders with nested categories', function () {
    $folder = TaskFolder::factory()->create(['user_id' => 1]);
    TaskCategory::factory()->create(['task_folder_id' => $folder->id, 'user_id' => 1]);

    $response = $this->getJson('/api/marquer/task-folders');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data')
        ->assertJsonStructure(['data' => [['id', 'name', 'categories', 'created_at', 'updated_at']]]);
});

test('does not return other user folders', function () {
    TaskFolder::factory()->create(['user_id' => 999]);

    $response = $this->getJson('/api/marquer/task-folders');

    $response->assertOk()
        ->assertJsonCount(0, 'data');
});

test('creates folder', function () {
    $response = $this->postJson('/api/marquer/task-folders', [
        'name' => 'Work',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Work');

    $this->assertDatabaseHas('task_folders', ['name' => 'Work', 'user_id' => 1]);
});

test('validates name required on create', function () {
    $response = $this->postJson('/api/marquer/task-folders', []);

    $response->assertUnprocessable()
        ->assertJsonStructure(['data' => ['name']]);
});

test('updates folder name', function () {
    $folder = TaskFolder::factory()->create(['user_id' => 1]);

    $response = $this->putJson("/api/marquer/task-folders/{$folder->id}", [
        'name' => 'Updated',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Updated');
});

test('returns 404 for other user folder on update', function () {
    $folder = TaskFolder::factory()->create(['user_id' => 999]);

    $response = $this->putJson("/api/marquer/task-folders/{$folder->id}", [
        'name' => 'Hacked',
    ]);

    $response->assertNotFound();
});

test('deletes folder and cascades', function () {
    $folder = TaskFolder::factory()->create(['user_id' => 1]);
    TaskCategory::factory()->create(['task_folder_id' => $folder->id, 'user_id' => 1]);

    $response = $this->deleteJson("/api/marquer/task-folders/{$folder->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('task_folders', ['id' => $folder->id]);
    $this->assertDatabaseMissing('task_categories', ['task_folder_id' => $folder->id]);
});
