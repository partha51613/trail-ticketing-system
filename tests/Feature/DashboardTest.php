<?php

use App\Models\User;

test('authenticated admin can view dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('Tickets Requiring Attention');
});

test('authenticated manager can view dashboard', function () {
    $manager = User::factory()->create(['role' => 'manager']);

    $response = $this->actingAs($manager)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('Tickets Requiring Attention');
});

test('authenticated user can view dashboard', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('Tickets Requiring Attention');
});
