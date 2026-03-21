<?php

beforeEach(function () {
    actingAsJwtUser();
});

test('avatar upload url returns presigned url and public url', function () {
    $response = $this->postJson('/api/marquer/profile/avatar/upload-url', [
        'content_type' => 'image/jpeg',
    ]);

    $response->assertOk();

    $data = $response->json('data');
    expect($data)->toHaveKeys(['upload_url', 'public_url', 'object_key']);
    expect($data['object_key'])->toContain('profiles/1/avatar/');
    expect($data['upload_url'])->toContain('X-Amz-Signature');
});

test('cover upload url returns presigned url and public url', function () {
    $response = $this->postJson('/api/marquer/profile/cover/upload-url', [
        'content_type' => 'image/png',
    ]);

    $response->assertOk();

    $data = $response->json('data');
    expect($data)->toHaveKeys(['upload_url', 'public_url', 'object_key']);
    expect($data['object_key'])->toContain('profiles/1/cover/');
});

test('validates content_type is required', function () {
    $response = $this->postJson('/api/marquer/profile/avatar/upload-url', []);

    $response->assertUnprocessable()
        ->assertJsonStructure(['data' => ['content_type']]);
});

test('validates content_type must be image', function () {
    $response = $this->postJson('/api/marquer/profile/avatar/upload-url', [
        'content_type' => 'application/pdf',
    ]);

    $response->assertUnprocessable()
        ->assertJsonStructure(['data' => ['content_type']]);
});

test('accepts image/jpeg content type', function () {
    $response = $this->postJson('/api/marquer/profile/cover/upload-url', [
        'content_type' => 'image/jpeg',
    ]);

    $response->assertOk();
});

test('accepts image/png content type', function () {
    $response = $this->postJson('/api/marquer/profile/avatar/upload-url', [
        'content_type' => 'image/png',
    ]);

    $response->assertOk();
});

test('requires authentication', function () {
    Auth::forgetGuards();

    $this->postJson('/api/marquer/profile/avatar/upload-url', [
        'content_type' => 'image/jpeg',
    ])->assertUnauthorized();
});
