<?php

test('registration screen cannot be rendered because public registration is disabled', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
});

test('public registration endpoint is disabled', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(404);
    $this->assertGuest();
});
