<?php

use App\Models\User;

test('giriş ekranı görüntülenebilir', function () {
    $yanit = $this->get('/login');

    $yanit->assertStatus(200);
});

test('kullanıcılar giriş ekranı üzerinden giriş yapabilir', function () {
    $kullanici = User::factory()->create();

    $yanit = $this->post('/login', [
        'email' => $kullanici->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $yanit->assertRedirect(route('dashboard', absolute: false));
});

test('kullanıcılar hatalı şifre ile giriş yapamaz', function () {
    $kullanici = User::factory()->create();

    $this->post('/login', [
        'email' => $kullanici->email,
        'password' => 'yanlis-sifre',
    ]);

    $this->assertGuest();
});

test('kullanıcılar çıkış yapabilir', function () {
    $kullanici = User::factory()->create();

    $yanit = $this->actingAs($kullanici)->post('/logout');

    $this->assertGuest();
    $yanit->assertRedirect('/');
});
