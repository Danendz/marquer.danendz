<?php

use App\Models\Profile\Friendship;
use App\Models\Profile\UserProfile;

beforeEach(function () {
    actingAsJwtUser();
    UserProfile::create(['user_id' => 1, 'username' => 'currentuser']);
    UserProfile::create(['user_id' => 2, 'username' => 'alice']);
    UserProfile::create(['user_id' => 3, 'username' => 'bob']);
    UserProfile::create(['user_id' => 4, 'username' => 'alice_wonder']);
});

test('search by username returns matching profiles', function () {
    $response = $this->getJson('/api/marquer/friends/search?username=alice');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(2); // alice and alice_wonder
});

test('search excludes self', function () {
    $response = $this->getJson('/api/marquer/friends/search?username=currentuser');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(0);
});

test('search excludes existing friends', function () {
    Friendship::create(['user_id' => 1, 'friend_id' => 2, 'status' => 'accepted']);

    $response = $this->getJson('/api/marquer/friends/search?username=alice');

    $response->assertOk();
    $data = $response->json('data');
    // alice (user 2) excluded, only alice_wonder (user 4) returned
    expect($data)->toHaveCount(1);
    expect($data[0]['username'])->toBe('alice_wonder');
});

test('search is case insensitive', function () {
    $response = $this->getJson('/api/marquer/friends/search?username=ALICE');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(2);
});

test('search requires username param', function () {
    $this->getJson('/api/marquer/friends/search')->assertUnprocessable();
});

test('generate invite code returns code', function () {
    $response = $this->postJson('/api/marquer/friends/invite');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveKey('invite_code');
    expect(strlen($data['invite_code']))->toBeGreaterThan(5);
});

test('redeem invite code sends friend request', function () {
    // User 2 generates invite
    actingAsJwtUser(2);
    $inviteResp = $this->postJson('/api/marquer/friends/invite');
    $code = $inviteResp->json('data.invite_code');

    // User 1 redeems it
    actingAsJwtUser(1);
    $response = $this->postJson('/api/marquer/friends/redeem', [
        'invite_code' => $code,
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('friendships', [
        'user_id' => 2,
        'friend_id' => 1,
        'status' => 'pending',
    ]);
});

test('requires authentication', function () {
    Auth::forgetGuards();
    $this->getJson('/api/marquer/friends/search?username=test')->assertUnauthorized();
});
