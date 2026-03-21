<?php

use App\Models\Profile\Friendship;
use App\Models\Profile\MailItem;
use App\Models\Profile\UserProfile;

beforeEach(function () {
    actingAsJwtUser();
    UserProfile::create(['user_id' => 1, 'username' => 'user1']);
});

test('register token succeeds', function () {
    $response = $this->postJson('/api/marquer/notifications/token', [
        'token' => 'fcm_test_token_123',
        'platform' => 'android',
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('device_tokens', [
        'user_id' => 1,
        'token' => 'fcm_test_token_123',
        'platform' => 'android',
    ]);
});

test('register token updates platform on duplicate', function () {
    $this->postJson('/api/marquer/notifications/token', [
        'token' => 'same_token',
        'platform' => 'android',
    ]);

    $this->postJson('/api/marquer/notifications/token', [
        'token' => 'same_token',
        'platform' => 'ios',
    ]);

    $this->assertDatabaseCount('device_tokens', 1);
    $this->assertDatabaseHas('device_tokens', [
        'token' => 'same_token',
        'platform' => 'ios',
    ]);
});

test('validates token required', function () {
    $this->postJson('/api/marquer/notifications/token', ['platform' => 'android'])
        ->assertUnprocessable();
});

test('validates platform enum', function () {
    $this->postJson('/api/marquer/notifications/token', [
        'token' => 'test',
        'platform' => 'windows',
    ])->assertUnprocessable();
});

test('unread count returns zeros when empty', function () {
    $response = $this->getJson('/api/marquer/notifications/unread-count');

    $response->assertOk()
        ->assertJsonPath('data.pending_requests', 0)
        ->assertJsonPath('data.unread_mail', 0)
        ->assertJsonPath('data.total', 0);
});

test('unread count includes pending friend requests', function () {
    Friendship::create(['user_id' => 2, 'friend_id' => 1, 'status' => 'pending']);
    Friendship::create(['user_id' => 3, 'friend_id' => 1, 'status' => 'pending']);

    $response = $this->getJson('/api/marquer/notifications/unread-count');

    $response->assertOk()
        ->assertJsonPath('data.pending_requests', 2)
        ->assertJsonPath('data.total', 2);
});

test('unread count includes unread mail', function () {
    MailItem::create([
        'sender_id' => 2,
        'recipient_id' => 1,
        'template_type' => 'postcard',
        'template_key' => 'test',
        'message' => 'Hello',
    ]);

    $response = $this->getJson('/api/marquer/notifications/unread-count');

    $response->assertOk()
        ->assertJsonPath('data.unread_mail', 1)
        ->assertJsonPath('data.total', 1);
});

test('unread count excludes read mail', function () {
    MailItem::create([
        'sender_id' => 2,
        'recipient_id' => 1,
        'template_type' => 'postcard',
        'template_key' => 'test',
        'message' => 'Hello',
        'read_at' => now(),
    ]);

    $response = $this->getJson('/api/marquer/notifications/unread-count');

    $response->assertOk()
        ->assertJsonPath('data.unread_mail', 0);
});

test('requires authentication', function () {
    Auth::forgetGuards();
    $this->postJson('/api/marquer/notifications/token', [])->assertUnauthorized();
    $this->getJson('/api/marquer/notifications/unread-count')->assertUnauthorized();
});
