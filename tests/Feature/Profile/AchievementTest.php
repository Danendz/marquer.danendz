<?php

use App\Models\Profile\Achievement;
use App\Models\Profile\UserAchievement;
use App\Services\Profile\AchievementService;
use Database\Seeders\AchievementSeeder;

beforeEach(function () {
    actingAsJwtUser();
    (new AchievementSeeder())->run();
});

test('list returns all achievements with unlock status', function () {
    $response = $this->getJson('/api/marquer/achievements');

    $response->assertOk();

    $data = $response->json('data');
    expect($data)->toBeArray();
    expect(count($data))->toBe(12);

    // All should be locked by default
    foreach ($data as $a) {
        expect($a['unlocked'])->toBeFalse();
        expect($a['unlocked_at'])->toBeNull();
    }
});

test('unlocked achievements show unlocked status', function () {
    $achievement = Achievement::where('key', 'first_note')->first();
    UserAchievement::create([
        'user_id' => 1,
        'achievement_id' => $achievement->id,
        'unlocked_at' => now(),
    ]);

    $response = $this->getJson('/api/marquer/achievements');

    $data = collect($response->json('data'));
    $firstNote = $data->firstWhere('key', 'first_note');

    expect($firstNote['unlocked'])->toBeTrue();
    expect($firstNote['unlocked_at'])->not->toBeNull();
});

test('checkAndUnlock unlocks matching achievements', function () {
    $service = app(AchievementService::class);
    $service->checkAndUnlock(1, 'notes_count', 10);

    // Should unlock first_note (1) and note_collector (10)
    $unlocked = UserAchievement::where('user_id', 1)->pluck('achievement_id')->toArray();
    $unlockedKeys = Achievement::whereIn('id', $unlocked)->pluck('key')->toArray();

    expect($unlockedKeys)->toContain('first_note');
    expect($unlockedKeys)->toContain('note_collector');
    expect($unlockedKeys)->not->toContain('prolific_writer');
});

test('checkAndUnlock does not create duplicate unlock', function () {
    $service = app(AchievementService::class);
    $service->checkAndUnlock(1, 'notes_count', 1);
    $service->checkAndUnlock(1, 'notes_count', 1);

    $count = UserAchievement::where('user_id', 1)->count();
    expect($count)->toBe(1);
});

test('other users unlocks do not appear', function () {
    $achievement = Achievement::where('key', 'first_note')->first();
    UserAchievement::create([
        'user_id' => 999,
        'achievement_id' => $achievement->id,
        'unlocked_at' => now(),
    ]);

    $response = $this->getJson('/api/marquer/achievements');

    $data = collect($response->json('data'));
    $firstNote = $data->firstWhere('key', 'first_note');

    expect($firstNote['unlocked'])->toBeFalse();
});

test('requires authentication', function () {
    Auth::forgetGuards();
    $this->getJson('/api/marquer/achievements')->assertUnauthorized();
});
