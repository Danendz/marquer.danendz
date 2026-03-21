<?php

use App\Models\Profile\UserProfile;

beforeEach(function () {
    actingAsJwtUser();
});

test('get returns default empty profile when none exists', function () {
    $response = $this->getJson('/api/marquer/profile');

    $response->assertOk()
        ->assertJsonPath('data.username', null)
        ->assertJsonPath('data.status', null)
        ->assertJsonPath('data.location', null)
        ->assertJsonPath('data.bio', null)
        ->assertJsonPath('data.avatar_url', null)
        ->assertJsonPath('data.cover_url', null)
        ->assertJsonPath('data.cover_type', 'static');

    $this->assertDatabaseHas('user_profiles', ['user_id' => 1]);
});

test('get returns existing profile', function () {
    UserProfile::create([
        'user_id' => 1,
        'username' => 'testuser',
        'status' => 'Studying',
        'location' => 'Tokyo',
        'bio' => 'Hello world',
    ]);

    $response = $this->getJson('/api/marquer/profile');

    $response->assertOk()
        ->assertJsonPath('data.username', 'testuser')
        ->assertJsonPath('data.status', 'Studying')
        ->assertJsonPath('data.location', 'Tokyo')
        ->assertJsonPath('data.bio', 'Hello world');
});

test('upsert creates profile for first time', function () {
    $response = $this->putJson('/api/marquer/profile', [
        'username' => 'newuser',
        'status' => 'Hello',
        'location' => 'Berlin',
        'bio' => 'My bio',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.username', 'newuser')
        ->assertJsonPath('data.status', 'Hello')
        ->assertJsonPath('data.location', 'Berlin')
        ->assertJsonPath('data.bio', 'My bio');

    $this->assertDatabaseHas('user_profiles', [
        'user_id' => 1,
        'username' => 'newuser',
    ]);
});

test('upsert updates existing profile', function () {
    UserProfile::create([
        'user_id' => 1,
        'username' => 'oldname',
        'status' => 'Old status',
    ]);

    $response = $this->putJson('/api/marquer/profile', [
        'username' => 'newname',
        'status' => 'New status',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.username', 'newname')
        ->assertJsonPath('data.status', 'New status');

    $this->assertDatabaseCount('user_profiles', 1);
    $this->assertDatabaseHas('user_profiles', [
        'user_id' => 1,
        'username' => 'newname',
    ]);
});

test('upsert allows partial update', function () {
    UserProfile::create([
        'user_id' => 1,
        'username' => 'keepme',
        'status' => 'Old status',
        'location' => 'Paris',
    ]);

    $response = $this->putJson('/api/marquer/profile', [
        'status' => 'Updated status',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.status', 'Updated status');

    $this->assertDatabaseHas('user_profiles', [
        'user_id' => 1,
        'username' => 'keepme',
        'location' => 'Paris',
    ]);
});

test('upsert validates username format: alphanumeric and underscores only', function () {
    $response = $this->putJson('/api/marquer/profile', [
        'username' => 'invalid user!',
    ]);

    $response->assertUnprocessable()
        ->assertJsonStructure(['data' => ['username']]);
});

test('upsert validates username min length', function () {
    $response = $this->putJson('/api/marquer/profile', [
        'username' => 'ab',
    ]);

    $response->assertUnprocessable()
        ->assertJsonStructure(['data' => ['username']]);
});

test('upsert validates username max length', function () {
    $response = $this->putJson('/api/marquer/profile', [
        'username' => str_repeat('a', 21),
    ]);

    $response->assertUnprocessable()
        ->assertJsonStructure(['data' => ['username']]);
});

test('upsert validates username uniqueness', function () {
    UserProfile::create([
        'user_id' => 999,
        'username' => 'taken',
    ]);

    $response = $this->putJson('/api/marquer/profile', [
        'username' => 'taken',
    ]);

    $response->assertUnprocessable()
        ->assertJsonStructure(['data' => ['username']]);
});

test('upsert allows keeping own username', function () {
    UserProfile::create([
        'user_id' => 1,
        'username' => 'myname',
    ]);

    $response = $this->putJson('/api/marquer/profile', [
        'username' => 'myname',
        'status' => 'Updated',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.username', 'myname')
        ->assertJsonPath('data.status', 'Updated');
});

test('upsert validates status max length', function () {
    $response = $this->putJson('/api/marquer/profile', [
        'status' => str_repeat('a', 101),
    ]);

    $response->assertUnprocessable()
        ->assertJsonStructure(['data' => ['status']]);
});

test('upsert validates location max length', function () {
    $response = $this->putJson('/api/marquer/profile', [
        'location' => str_repeat('a', 101),
    ]);

    $response->assertUnprocessable()
        ->assertJsonStructure(['data' => ['location']]);
});

test('upsert validates bio max length', function () {
    $response = $this->putJson('/api/marquer/profile', [
        'bio' => str_repeat('a', 501),
    ]);

    $response->assertUnprocessable()
        ->assertJsonStructure(['data' => ['bio']]);
});

test('upsert validates cover_type enum', function () {
    $response = $this->putJson('/api/marquer/profile', [
        'cover_type' => 'invalid',
    ]);

    $response->assertUnprocessable()
        ->assertJsonStructure(['data' => ['cover_type']]);
});

test('user cannot read another users profile', function () {
    UserProfile::create([
        'user_id' => 999,
        'username' => 'otheruser',
        'status' => 'Secret',
    ]);

    $response = $this->getJson('/api/marquer/profile');

    $response->assertOk()
        ->assertJsonPath('data.username', null);

    $this->assertDatabaseMissing('user_profiles', [
        'user_id' => 1,
        'username' => 'otheruser',
    ]);
});

test('upsert with empty body succeeds with defaults', function () {
    $response = $this->putJson('/api/marquer/profile', []);

    $response->assertOk();

    $this->assertDatabaseHas('user_profiles', ['user_id' => 1]);
});

test('requires authentication', function () {
    Auth::forgetGuards();

    $this->getJson('/api/marquer/profile')->assertUnauthorized();
    $this->putJson('/api/marquer/profile', [])->assertUnauthorized();
});
