<?php

use App\Models\Profile\UserSettings;

beforeEach(function () {
    actingAsJwtUser();
});

test('get returns defaults when no settings exist', function () {
    $response = $this->getJson('/api/marquer/settings');

    $response->assertOk()
        ->assertJsonPath('data.theme', 'light')
        ->assertJsonPath('data.language', 'en')
        ->assertJsonPath('data.notifications_enabled', true)
        ->assertJsonPath('data.notification_study_reminders', true)
        ->assertJsonPath('data.notification_task_reminders', true);

    $this->assertDatabaseHas('user_settings', ['user_id' => 1]);
});

test('upsert creates settings for first time', function () {
    $response = $this->putJson('/api/marquer/settings', [
        'theme' => 'dark',
        'language' => 'fr',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.theme', 'dark')
        ->assertJsonPath('data.language', 'fr');

    $this->assertDatabaseHas('user_settings', [
        'user_id' => 1,
        'theme' => 'dark',
        'language' => 'fr',
    ]);
});

test('upsert updates existing settings', function () {
    UserSettings::create(['user_id' => 1, 'theme' => 'light', 'language' => 'en']);

    $response = $this->putJson('/api/marquer/settings', [
        'theme' => 'dark',
        'notifications_enabled' => false,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.theme', 'dark')
        ->assertJsonPath('data.notifications_enabled', false);

    $this->assertDatabaseCount('user_settings', 1);
});

test('validates theme enum', function () {
    $this->putJson('/api/marquer/settings', ['theme' => 'rainbow'])
        ->assertUnprocessable()
        ->assertJsonStructure(['data' => ['theme']]);
});

test('validates language enum', function () {
    $this->putJson('/api/marquer/settings', ['language' => 'klingon'])
        ->assertUnprocessable()
        ->assertJsonStructure(['data' => ['language']]);
});

test('accepts valid languages', function () {
    foreach (['en', 'fr', 'zh_CN'] as $lang) {
        $this->putJson('/api/marquer/settings', ['language' => $lang])
            ->assertOk();
    }
});

test('upsert with empty body succeeds', function () {
    $this->putJson('/api/marquer/settings', [])->assertOk();
});

test('user can only read own settings', function () {
    UserSettings::create(['user_id' => 999, 'theme' => 'dark']);

    $response = $this->getJson('/api/marquer/settings');
    $response->assertOk()->assertJsonPath('data.theme', 'light');
});

test('requires authentication', function () {
    Auth::forgetGuards();

    $this->getJson('/api/marquer/settings')->assertUnauthorized();
    $this->putJson('/api/marquer/settings', [])->assertUnauthorized();
});
