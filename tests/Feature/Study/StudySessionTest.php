<?php

use App\Models\Study\StudySession;
use App\Models\Study\StudySubject;

beforeEach(function () {
    actingAsJwtUser();
});

test('user can create a count up session', function () {
    $response = $this->postJson('/api/marquer/study/sessions', [
        'name' => 'Morning Study',
        'timer_mode' => 'count_up',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Morning Study')
        ->assertJsonPath('data.timer_mode', 'count_up')
        ->assertJsonPath('data.status', 'active');

    $this->assertDatabaseHas('study_sessions', ['name' => 'Morning Study', 'user_id' => 1]);
});

test('user can create a pomodoro session', function () {
    $response = $this->postJson('/api/marquer/study/sessions', [
        'name' => 'Pomodoro Session',
        'timer_mode' => 'pomodoro',
        'pomodoro_work_minutes' => 25,
        'pomodoro_short_break_minutes' => 5,
        'pomodoro_long_break_minutes' => 15,
        'pomodoro_cycles' => 4,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.timer_mode', 'pomodoro')
        ->assertJsonPath('data.pomodoro_work_minutes', 25)
        ->assertJsonPath('data.pomodoro_cycles', 4);
});

test('creating second session while one is active returns 422', function () {
    StudySession::create([
        'user_id' => 1,
        'name' => 'First Session',
        'timer_mode' => 'count_up',
        'status' => 'active',
        'started_at' => now(),
    ]);

    $response = $this->postJson('/api/marquer/study/sessions', [
        'name' => 'Second Session',
        'timer_mode' => 'count_up',
    ]);

    $response->assertStatus(422);
});

test('user can pause session', function () {
    $session = StudySession::create([
        'user_id' => 1,
        'name' => 'My Session',
        'timer_mode' => 'count_up',
        'status' => 'active',
        'started_at' => now(),
    ]);

    $response = $this->putJson("/api/marquer/study/sessions/{$session->id}", [
        'status' => 'paused',
        'actual_duration_seconds' => 300,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.status', 'paused');
});

test('user can complete session', function () {
    $session = StudySession::create([
        'user_id' => 1,
        'name' => 'My Session',
        'timer_mode' => 'count_up',
        'status' => 'active',
        'started_at' => now(),
    ]);

    $response = $this->postJson("/api/marquer/study/sessions/{$session->id}/complete", [
        'actual_duration_seconds' => 1800,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.status', 'completed')
        ->assertJsonPath('data.actual_duration_seconds', 1800);

    $this->assertDatabaseHas('study_sessions', [
        'id' => $session->id,
        'status' => 'completed',
        'actual_duration_seconds' => 1800,
    ]);
});

test('user can cancel session', function () {
    $session = StudySession::create([
        'user_id' => 1,
        'name' => 'My Session',
        'timer_mode' => 'count_up',
        'status' => 'active',
        'started_at' => now(),
    ]);

    $response = $this->postJson("/api/marquer/study/sessions/{$session->id}/cancel");

    $response->assertOk()
        ->assertJsonPath('data.status', 'cancelled');
});

test('stats returns today total seconds and sessions list', function () {
    StudySession::create([
        'user_id' => 1,
        'name' => 'Completed Session',
        'timer_mode' => 'count_up',
        'status' => 'completed',
        'started_at' => now(),
        'ended_at' => now(),
        'actual_duration_seconds' => 3600,
    ]);

    $response = $this->getJson('/api/marquer/study/sessions/stats');

    $response->assertOk()
        ->assertJsonPath('data.today_total_seconds', 3600)
        ->assertJsonStructure(['data' => ['today_total_seconds', 'sessions']]);
});

test('date filter works on session list', function () {
    StudySession::create([
        'user_id' => 1,
        'name' => 'Old Session',
        'timer_mode' => 'count_up',
        'status' => 'completed',
        'started_at' => '2026-01-01 10:00:00',
        'ended_at' => '2026-01-01 11:00:00',
    ]);

    StudySession::create([
        'user_id' => 1,
        'name' => 'Recent Session',
        'timer_mode' => 'count_up',
        'status' => 'completed',
        'started_at' => now(),
        'ended_at' => now(),
    ]);

    $response = $this->getJson('/api/marquer/study/sessions?date_from=2026-02-01');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Recent Session');
});

test('session list returns sessions for authenticated user only', function () {
    StudySession::create([
        'user_id' => 1,
        'name' => 'My Session',
        'timer_mode' => 'count_up',
        'status' => 'active',
        'started_at' => now(),
    ]);

    StudySession::create([
        'user_id' => 2,
        'name' => 'Other Session',
        'timer_mode' => 'count_up',
        'status' => 'active',
        'started_at' => now(),
    ]);

    $response = $this->getJson('/api/marquer/study/sessions');

    $response->assertOk()
        ->assertJsonCount(1, 'data');
});

test('user cannot access another users session', function () {
    $session = StudySession::create([
        'user_id' => 2,
        'name' => 'Other Session',
        'timer_mode' => 'count_up',
        'status' => 'active',
        'started_at' => now(),
    ]);

    $response = $this->putJson("/api/marquer/study/sessions/{$session->id}", [
        'status' => 'paused',
    ]);

    $response->assertNotFound();
});

test('session store validates pomodoro fields required when mode is pomodoro', function () {
    $response = $this->postJson('/api/marquer/study/sessions', [
        'name' => 'Pomodoro Session',
        'timer_mode' => 'pomodoro',
    ]);

    $response->assertUnprocessable();
});

test('session can be linked to a subject', function () {
    $subject = StudySubject::create(['user_id' => 1, 'name' => 'Math', 'color' => '#4285F4']);

    $response = $this->postJson('/api/marquer/study/sessions', [
        'name' => 'Math Study',
        'timer_mode' => 'count_up',
        'study_subject_id' => $subject->id,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.study_subject_id', $subject->id);
});
