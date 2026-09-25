<?php

test('health check endpoint returns 200 without token', function () {
    $response = $this->get('/health');
    $response->assertStatus(200);
    $response->assertJsonStructure(['healthy', 'timestamp', 'version']);
    $response->assertJsonMissing(['checks']);
});

test('health check endpoint returns detailed response with valid token', function () {
    config(['alhikmah.health_key' => 'secret-test-key-2026']);

    $response = $this->get('/health', ['X-Health-Key' => 'secret-test-key-2026']);

    $response->assertStatus(200);
    $response->assertJsonStructure(['healthy', 'timestamp', 'version', 'checks']);
});

test('health check endpoint ignores invalid token and returns public response only', function () {
    config(['alhikmah.health_key' => 'secret-test-key-2026']);

    $response = $this->get('/health', ['X-Health-Key' => 'wrong-token']);

    $response->assertStatus(200);
    $response->assertJsonMissing(['checks']);
});
