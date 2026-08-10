<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('admin can authenticate and redirect to admin dashboard', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->post('/login', [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('admin.dashboard'));
});

test('mentor can authenticate and redirect to mentor dashboard', function () {
    $mentor = User::factory()->mentor()->create();

    $response = $this->post('/login', [
        'email' => $mentor->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('mentor.dashboard'));
});

test('parent can authenticate and redirect to parent dashboard', function () {
    $parent = User::factory()->parent()->create();

    $response = $this->post('/login', [
        'email' => $parent->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('parent.dashboard'));
});

test('student can authenticate and redirect to student dashboard', function () {
    $student = User::factory()->student()->create();

    $response = $this->post('/login', [
        'email' => $student->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('student.dashboard'));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
