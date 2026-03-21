<?php

use App\Models\Profile\UserSettings;

beforeEach(function () {
    actingAsJwtUser();
});

test('get returns empty lab features by default', function () {
    $response = $this->getJson('/api/marquer/laboratory');

    $response->assertOk()
        ->assertJsonPath('data', []);
});

test('get returns existing lab features', function () {
    UserSettings::create([
        'user_id' => 1,
        'lab_features' => ['experimental_ui' => true, 'beta_search' => false],
    ]);

    $response = $this->getJson('/api/marquer/laboratory');

    $response->assertOk()
        ->assertJsonPath('data.experimental_ui', true)
        ->assertJsonPath('data.beta_search', false);
});

test('toggle enables a feature', function () {
    $response = $this->putJson('/api/marquer/laboratory', [
        'feature' => 'experimental_ui',
        'enabled' => true,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.experimental_ui', true);
});

test('toggle disables a feature', function () {
    UserSettings::create([
        'user_id' => 1,
        'lab_features' => ['experimental_ui' => true],
    ]);

    $response = $this->putJson('/api/marquer/laboratory', [
        'feature' => 'experimental_ui',
        'enabled' => false,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.experimental_ui', false);
});

test('validates feature required', function () {
    $this->putJson('/api/marquer/laboratory', ['enabled' => true])
        ->assertUnprocessable();
});

test('validates enabled required', function () {
    $this->putJson('/api/marquer/laboratory', ['feature' => 'test'])
        ->assertUnprocessable();
});

test('requires authentication', function () {
    Auth::forgetGuards();

    $this->getJson('/api/marquer/laboratory')->assertUnauthorized();
});
