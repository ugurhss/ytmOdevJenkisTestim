<?php

use App\Models\User;

test('giriş ekranı görüntülenebilir', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('kullanıcılar giriş ekranı üzerinden giriş yapabilir', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('kullanıcılar hatalı şifre ile giriş yapamaz', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('kullanıcılar çıkış yapabilir', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
