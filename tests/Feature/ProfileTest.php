<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Profil sayfası görüntülenebilir.
     */
    public function test_profil_sayfasi_goruntulenebilir(): void
    {
        $kullanici = User::factory()->create();

        $yanit = $this
            ->actingAs($kullanici)
            ->get('/profile');

        $yanit->assertOk();
    }

    /**
     * Test: Profil bilgileri güncellenebilir.
     */
    public function test_profil_bilgileri_guncellenebilir(): void
    {
        $kullanici = User::factory()->create();

        $yanit = $this
            ->actingAs($kullanici)
            ->patch('/profile', [
                'name' => 'Test Kullanıcı',
                'email' => 'test@example.com',
            ]);

        $yanit
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $kullanici->refresh();

        $this->assertSame('Test Kullanıcı', $kullanici->name);
        $this->assertSame('test@example.com', $kullanici->email);
        $this->assertNull($kullanici->email_verified_at);
    }

    /**
     * Test: E-posta adresi değişmediğinde doğrulama durumu değişmez.
     */
    public function test_eposta_adresi_degismediginde_dogrulama_durumu_degismez(): void
    {
        $kullanici = User::factory()->create();

        $yanit = $this
            ->actingAs($kullanici)
            ->patch('/profile', [
                'name' => 'Test Kullanıcı',
                'email' => $kullanici->email,
            ]);

        $yanit
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($kullanici->refresh()->email_verified_at);
    }

    /**
     * Test: Kullanıcı hesabını silebilir.
     */
    public function test_kullanici_hesabini_silebilir(): void
    {
        $kullanici = User::factory()->create();

        $yanit = $this
            ->actingAs($kullanici)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $yanit
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($kullanici->fresh());
    }

    /**
     * Test: Hesap silmek için doğru şifre girilmelidir.
     */
    public function test_hesap_silmek_icin_dogru_sifre_girilmelidir(): void
    {
        $kullanici = User::factory()->create();

        $yanit = $this
            ->actingAs($kullanici)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'yanlis-sifre',
            ]);

        $yanit
            ->assertSessionHasErrors('password')
            ->assertRedirect('/profile');

        $this->assertNotNull($kullanici->fresh());
    }
}
