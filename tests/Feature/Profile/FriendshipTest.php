<?php

use App\Models\Profile\Friendship;
use App\Models\Profile\UserProfile;

beforeEach(function () {
    actingAsJwtUser();
    // Create profiles for test users
    UserProfile::create(['user_id' => 1, 'username' => 'user1']);
    UserProfile::create(['user_id' => 2, 'username' => 'user2']);
});

test('send friend request creates pending friendship', function () {
    $response = $this->postJson('/api/marquer/friends/request', [
        'friend_id' => 2,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.status', 'pending');

    $this->assertDatabaseHas('friendships', [
        'user_id' => 1,
        'friend_id' => 2,
        'status' => 'pending',
    ]);
});

test('cannot send friend request to self', function () {
    $response = $this->postJson('/api/marquer/friends/request', [
        'friend_id' => 1,
    ]);

    $response->assertStatus(422);
});

test('cannot send duplicate friend request', function () {
    Friendship::create(['user_id' => 1, 'friend_id' => 2, 'status' => 'pending']);

    $response = $this->postJson('/api/marquer/friends/request', [
        'friend_id' => 2,
    ]);

    $response->assertStatus(422);
});

test('accept friend request changes status to accepted', function () {
    $friendship = Friendship::create([
        'user_id' => 2,
        'friend_id' => 1,
        'status' => 'pending',
    ]);

    $response = $this->putJson("/api/marquer/friends/request/{$friendship->id}", [
        'action' => 'accept',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.status', 'accepted');

    $this->assertDatabaseHas('friendships', [
        'id' => $friendship->id,
        'status' => 'accepted',
    ]);
});

test('reject friend request deletes it', function () {
    $friendship = Friendship::create([
        'user_id' => 2,
        'friend_id' => 1,
        'status' => 'pending',
    ]);

    $response = $this->putJson("/api/marquer/friends/request/{$friendship->id}", [
        'action' => 'reject',
    ]);

    $response->assertOk();
    $this->assertDatabaseMissing('friendships', ['id' => $friendship->id]);
});

test('list friends returns accepted only', function () {
    Friendship::create(['user_id' => 1, 'friend_id' => 2, 'status' => 'accepted']);

    $response = $this->getJson('/api/marquer/friends');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['username'])->toBe('user2');
});

test('list friends does not include pending', function () {
    Friendship::create(['user_id' => 1, 'friend_id' => 2, 'status' => 'pending']);

    $response = $this->getJson('/api/marquer/friends');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(0);
});

test('pending requests returns incoming only', function () {
    Friendship::create(['user_id' => 2, 'friend_id' => 1, 'status' => 'pending']);

    $response = $this->getJson('/api/marquer/friends/requests');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['user_id'])->toBe(2);
});

test('remove friend deletes the friendship', function () {
    Friendship::create(['user_id' => 1, 'friend_id' => 2, 'status' => 'accepted']);

    $response = $this->deleteJson('/api/marquer/friends/2');

    $response->assertOk();
    $this->assertDatabaseMissing('friendships', ['user_id' => 1, 'friend_id' => 2]);
});

test('cannot accept request not addressed to you', function () {
    $friendship = Friendship::create([
        'user_id' => 1,
        'friend_id' => 2,
        'status' => 'pending',
    ]);

    // User 1 trying to accept a request they sent (friend_id should be them)
    $response = $this->putJson("/api/marquer/friends/request/{$friendship->id}", [
        'action' => 'accept',
    ]);

    $response->assertNotFound();
});

test('requires authentication', function () {
    Auth::forgetGuards();
    $this->getJson('/api/marquer/friends')->assertUnauthorized();
});
