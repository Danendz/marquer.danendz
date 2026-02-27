<?php

use App\Models\Study\UserStudySettings;

beforeEach(function () {
    actingAsJwtUser();
});

test('get returns defaults when no settings exist', function () {
    $response = $this->getJson('/api/marquer/study/settings');

    $response->assertOk()
        ->assertJsonPath('data.default_work_minutes', 25)
        ->assertJsonPath('data.default_short_break_minutes', 5)
        ->assertJsonPath('data.default_long_break_minutes', 15)
        ->assertJsonPath('data.default_cycles', 4);
});

test('upsert creates settings for first time', function () {
    $response = $this->putJson('/api/marquer/study/settings', [
        'default_work_minutes' => 30,
        'default_short_break_minutes' => 10,
        'default_long_break_minutes' => 20,
        'default_cycles' => 3,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.default_work_minutes', 30)
        ->assertJsonPath('data.default_short_break_minutes', 10)
        ->assertJsonPath('data.default_long_break_minutes', 20)
        ->assertJsonPath('data.default_cycles', 3);

    $this->assertDatabaseHas('user_study_settings', [
        'user_id' => 1,
        'default_work_minutes' => 30,
    ]);
});

test('upsert updates existing settings', function () {
    UserStudySettings::create([
        'user_id' => 1,
        'default_work_minutes' => 25,
        'default_short_break_minutes' => 5,
        'default_long_break_minutes' => 15,
        'default_cycles' => 4,
    ]);

    $response = $this->putJson('/api/marquer/study/settings', [
        'default_work_minutes' => 45,
        'default_short_break_minutes' => 10,
        'default_long_break_minutes' => 20,
        'default_cycles' => 2,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.default_work_minutes', 45);

    $this->assertDatabaseCount('user_study_settings', 1);
    $this->assertDatabaseHas('user_study_settings', [
        'user_id' => 1,
        'default_work_minutes' => 45,
    ]);
});

test('upsert validates required fields', function () {
    $response = $this->putJson('/api/marquer/study/settings', []);

    $response->assertUnprocessable()
        ->assertJsonStructure(['data' => [
            'default_work_minutes',
            'default_short_break_minutes',
            'default_long_break_minutes',
            'default_cycles',
        ]]);
});

test('upsert validates max values', function () {
    $response = $this->putJson('/api/marquer/study/settings', [
        'default_work_minutes' => 200,
        'default_short_break_minutes' => 5,
        'default_long_break_minutes' => 15,
        'default_cycles' => 4,
    ]);

    $response->assertUnprocessable()
        ->assertJsonStructure(['data' => ['default_work_minutes']]);
});
