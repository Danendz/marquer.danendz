<?php

use App\Models\Countdown;

beforeEach(function () {
    actingAsJwtUser();
});

// Helpers
function makeCountdown(array $attrs = []): Countdown
{
    return Countdown::create(array_merge([
        'user_id' => 1,
        'name' => 'Test Countdown',
        'target_date' => now()->addDays(30)->toDateString(),
        'bg_image' => 'IMG_1157.webp',
        'is_pinned' => false,
    ], $attrs));
}

// --- List ---

test('returns only own countdowns ordered by pinned then target_date', function () {
    makeCountdown(['name' => 'Far', 'target_date' => now()->addDays(60)->toDateString()]);
    makeCountdown(['name' => 'Near', 'target_date' => now()->addDays(10)->toDateString()]);
    makeCountdown(['name' => 'Pinned', 'target_date' => now()->addDays(20)->toDateString(), 'is_pinned' => true]);
    makeCountdown(['user_id' => 2, 'name' => 'Other User']);

    $response = $this->getJson('/api/marquer/calendar/countdowns');

    $response->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('data.0.name', 'Pinned')
        ->assertJsonPath('data.1.name', 'Near')
        ->assertJsonPath('data.2.name', 'Far');
});

test('returns empty list when no countdowns', function () {
    $response = $this->getJson('/api/marquer/calendar/countdowns');

    $response->assertOk()->assertJsonCount(0, 'data');
});

// --- Create ---

test('creates countdown with valid data', function () {
    $response = $this->postJson('/api/marquer/calendar/countdowns', [
        'name' => 'My Exam',
        'target_date' => now()->addDays(30)->toDateString(),
        'bg_image' => 'IMG_1157.webp',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'My Exam')
        ->assertJsonPath('data.is_pinned', false)
        ->assertJsonStructure(['data' => ['id', 'name', 'target_date', 'bg_image', 'is_pinned', 'created_at', 'updated_at']]);

    $this->assertDatabaseHas('countdowns', ['name' => 'My Exam', 'user_id' => 1]);
});

test('stores the bg_image sent by the client on create', function () {
    $response = $this->postJson('/api/marquer/calendar/countdowns', [
        'name' => 'Test',
        'target_date' => now()->addDays(10)->toDateString(),
        'bg_image' => 'IMG_1320.webp',
    ]);

    $response->assertJsonPath('data.bg_image', 'IMG_1320.webp');
    $this->assertDatabaseHas('countdowns', ['name' => 'Test', 'bg_image' => 'IMG_1320.webp']);
});

test('validates name is required on create', function () {
    $response = $this->postJson('/api/marquer/calendar/countdowns', [
        'target_date' => now()->addDays(10)->toDateString(),
    ]);

    $response->assertUnprocessable()->assertJsonStructure(['data' => ['name']]);
});

test('validates target_date is required on create', function () {
    $response = $this->postJson('/api/marquer/calendar/countdowns', [
        'name' => 'Test',
    ]);

    $response->assertUnprocessable()->assertJsonStructure(['data' => ['target_date']]);
});

test('validates target_date must be after today on create', function () {
    $response = $this->postJson('/api/marquer/calendar/countdowns', [
        'name' => 'Test',
        'target_date' => now()->toDateString(),
    ]);

    $response->assertUnprocessable()->assertJsonStructure(['data' => ['target_date']]);
});

// --- Update ---

test('updates countdown name and target_date', function () {
    $countdown = makeCountdown();
    $newDate = now()->addDays(90)->toDateString();

    $response = $this->putJson("/api/marquer/calendar/countdowns/{$countdown->id}", [
        'name' => 'Updated Name',
        'target_date' => $newDate,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Updated Name')
        ->assertJsonPath('data.target_date', $newDate);
});

test('pins a countdown and unpins others', function () {
    $pinned = makeCountdown(['is_pinned' => true]);
    $other = makeCountdown(['name' => 'Other']);

    $response = $this->putJson("/api/marquer/calendar/countdowns/{$other->id}", [
        'is_pinned' => true,
    ]);

    $response->assertOk()->assertJsonPath('data.is_pinned', true);

    $this->assertDatabaseHas('countdowns', ['id' => $other->id, 'is_pinned' => true]);
    $this->assertDatabaseHas('countdowns', ['id' => $pinned->id, 'is_pinned' => false]);
});

test('can unpin a countdown without affecting others', function () {
    $countdown = makeCountdown(['is_pinned' => true]);
    $other = makeCountdown(['name' => 'Other', 'is_pinned' => false]);

    $response = $this->putJson("/api/marquer/calendar/countdowns/{$countdown->id}", [
        'is_pinned' => false,
    ]);

    $response->assertOk()->assertJsonPath('data.is_pinned', false);
    $this->assertDatabaseHas('countdowns', ['id' => $other->id, 'is_pinned' => false]);
});

test('returns 404 when updating another users countdown', function () {
    $countdown = makeCountdown(['user_id' => 2]);

    $response = $this->putJson("/api/marquer/calendar/countdowns/{$countdown->id}", [
        'name' => 'Hacked',
    ]);

    $response->assertNotFound();
});

test('updates bg_image', function () {
    $countdown = makeCountdown(['bg_image' => 'IMG_1157.webp']);

    $response = $this->putJson("/api/marquer/calendar/countdowns/{$countdown->id}", [
        'bg_image' => 'IMG_1170.webp',
    ]);

    $response->assertOk()->assertJsonPath('data.bg_image', 'IMG_1170.webp');
    $this->assertDatabaseHas('countdowns', ['id' => $countdown->id, 'bg_image' => 'IMG_1170.webp']);
});

test('validates target_date must be after today on update', function () {
    $countdown = makeCountdown();

    $response = $this->putJson("/api/marquer/calendar/countdowns/{$countdown->id}", [
        'target_date' => now()->subDay()->toDateString(),
    ]);

    $response->assertUnprocessable()->assertJsonStructure(['data' => ['target_date']]);
});

// --- Delete ---

test('deletes own countdown', function () {
    $countdown = makeCountdown();

    $response = $this->deleteJson("/api/marquer/calendar/countdowns/{$countdown->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('countdowns', ['id' => $countdown->id]);
});

test('returns 404 when deleting another users countdown', function () {
    $countdown = makeCountdown(['user_id' => 2]);

    $response = $this->deleteJson("/api/marquer/calendar/countdowns/{$countdown->id}");

    $response->assertNotFound();
    $this->assertDatabaseHas('countdowns', ['id' => $countdown->id]);
});

