<?php

use App\Models\Study\StudySubject;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    actingAsJwtUser();
});

test('authenticated user can list subjects including system and own', function () {
    // System subject (user_id null)
    StudySubject::create(['user_id' => null, 'name' => 'Mathematics', 'color' => '#4285F4']);
    // Own subject
    StudySubject::create(['user_id' => 1, 'name' => 'My Subject', 'color' => '#FF0000']);
    // Another user's subject
    StudySubject::create(['user_id' => 2, 'name' => 'Other Subject', 'color' => '#00FF00']);

    $response = $this->getJson('/api/marquer/study/subjects');

    $response->assertOk()
        ->assertJsonCount(2, 'data');
});

test('user can create subject', function () {
    $response = $this->postJson('/api/marquer/study/subjects', [
        'name' => 'Physics',
        'color' => '#9C27B0',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Physics')
        ->assertJsonPath('data.color', '#9C27B0')
        ->assertJsonPath('data.is_system', false);

    $this->assertDatabaseHas('study_subjects', ['name' => 'Physics', 'user_id' => 1]);
});

test('user cannot edit system subject returns 403', function () {
    $subject = StudySubject::create(['user_id' => null, 'name' => 'Mathematics', 'color' => '#4285F4']);

    $response = $this->putJson("/api/marquer/study/subjects/{$subject->id}", [
        'name' => 'Hacked',
    ]);

    $response->assertStatus(403);
});

test('user cannot delete system subject returns 403', function () {
    $subject = StudySubject::create(['user_id' => null, 'name' => 'Science', 'color' => '#34A853']);

    $response = $this->deleteJson("/api/marquer/study/subjects/{$subject->id}");

    $response->assertStatus(403);
    $this->assertDatabaseHas('study_subjects', ['id' => $subject->id]);
});

test('user can edit own subject', function () {
    $subject = StudySubject::create(['user_id' => 1, 'name' => 'Old Name', 'color' => '#FF0000']);

    $response = $this->putJson("/api/marquer/study/subjects/{$subject->id}", [
        'name' => 'New Name',
        'color' => '#00FF00',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'New Name')
        ->assertJsonPath('data.color', '#00FF00');
});

test('user can delete own subject', function () {
    $subject = StudySubject::create(['user_id' => 1, 'name' => 'To Delete', 'color' => '#FF0000']);

    $response = $this->deleteJson("/api/marquer/study/subjects/{$subject->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('study_subjects', ['id' => $subject->id]);
});

test('user cannot access another users subject', function () {
    $subject = StudySubject::create(['user_id' => 2, 'name' => 'Another Subject', 'color' => '#FF0000']);

    $response = $this->putJson("/api/marquer/study/subjects/{$subject->id}", [
        'name' => 'Hacked',
    ]);

    $response->assertNotFound();
});

test('validates name and color required on create', function () {
    $response = $this->postJson('/api/marquer/study/subjects', []);

    $response->assertUnprocessable()
        ->assertJsonStructure(['data' => ['name', 'color']]);
});

test('validates hex color format', function () {
    $response = $this->postJson('/api/marquer/study/subjects', [
        'name' => 'Test',
        'color' => 'not-a-color',
    ]);

    $response->assertUnprocessable()
        ->assertJsonStructure(['data' => ['color']]);
});
