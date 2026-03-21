<?php

use App\Models\Profile\Friendship;
use App\Models\Profile\MailItem;
use App\Models\Profile\UserProfile;

beforeEach(function () {
    actingAsJwtUser();
    UserProfile::create(['user_id' => 1, 'username' => 'user1']);
    UserProfile::create(['user_id' => 2, 'username' => 'user2']);
});

test('send mail to friend succeeds', function () {
    Friendship::create(['user_id' => 1, 'friend_id' => 2, 'status' => 'accepted']);

    $response = $this->postJson('/api/marquer/mail', [
        'recipient_id' => 2,
        'template_type' => 'postcard',
        'template_key' => 'sunset',
        'message' => 'Hello friend!',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.template_type', 'postcard');

    $this->assertDatabaseHas('mail_items', [
        'sender_id' => 1,
        'recipient_id' => 2,
        'template_type' => 'postcard',
        'message' => 'Hello friend!',
    ]);
});

test('send mail to non-friend returns 403', function () {
    $response = $this->postJson('/api/marquer/mail', [
        'recipient_id' => 2,
        'template_type' => 'letter',
        'template_key' => 'classic',
        'message' => 'Should not work',
    ]);

    $response->assertForbidden();
});

test('send mail validates required fields', function () {
    $this->postJson('/api/marquer/mail', [])
        ->assertUnprocessable()
        ->assertJsonStructure(['data' => ['recipient_id', 'template_type', 'template_key', 'message']]);
});

test('send mail validates template_type enum', function () {
    Friendship::create(['user_id' => 1, 'friend_id' => 2, 'status' => 'accepted']);

    $this->postJson('/api/marquer/mail', [
        'recipient_id' => 2,
        'template_type' => 'telegram',
        'template_key' => 'test',
        'message' => 'Test',
    ])->assertUnprocessable();
});

test('list received mail returns items with sender info', function () {
    Friendship::create(['user_id' => 1, 'friend_id' => 2, 'status' => 'accepted']);

    MailItem::create([
        'sender_id' => 2,
        'recipient_id' => 1,
        'template_type' => 'postcard',
        'template_key' => 'sunset',
        'message' => 'From user 2',
    ]);

    $response = $this->getJson('/api/marquer/mail');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['sender_username'])->toBe('user2');
    expect($data[0]['message'])->toBe('From user 2');
});

test('list sent mail returns items', function () {
    MailItem::create([
        'sender_id' => 1,
        'recipient_id' => 2,
        'template_type' => 'letter',
        'template_key' => 'classic',
        'message' => 'Dear friend',
    ]);

    $response = $this->getJson('/api/marquer/mail/sent');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

test('received mail does not include sent items', function () {
    MailItem::create([
        'sender_id' => 1,
        'recipient_id' => 2,
        'template_type' => 'postcard',
        'template_key' => 'test',
        'message' => 'Outgoing',
    ]);

    $response = $this->getJson('/api/marquer/mail');
    expect($response->json('data'))->toHaveCount(0);
});

test('requires authentication', function () {
    Auth::forgetGuards();
    $this->getJson('/api/marquer/mail')->assertUnauthorized();
    $this->postJson('/api/marquer/mail', [])->assertUnauthorized();
});
