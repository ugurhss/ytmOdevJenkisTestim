<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProfilTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test: Profil sayfası görüntülenebilir.
     */
    public function test_profil_sayfasi_goruntulenebilir(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'name' => 'Test Kullanıcı',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->assertPathIs('/profile')
                    ->pause(1000);
        });
    }

    /**
     * Test: Profil bilgileri güncellenebilir.
     */
    public function test_profil_bilgileri_guncellenebilir(): void
    {
        $user = User::factory()->create([
            'email' => 'eski@example.com',
            'password' => bcrypt('password'),
            'name' => 'Eski İsim',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->assertPathIs('/profile')
                    ->pause(1000)
                    ->type('name', 'Yeni İsim')
                    ->type('email', 'yeni@example.com')
                    ->pause(500)
                    ->press('Kaydet')
                    ->pause(2000)
                    ->assertPathIs('/profile');
        });
    }

    /**
     * Test: Misafir kullanıcı profil sayfasına erişemez.
     */
    public function test_misafir_kullanici_profil_sayfasina_erisemez(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/profile')
                    ->assertPathIs('/login');
        });
    }

    /**
     * Test: Profil sayfasında kullanıcı adı görüntülenir.
     */
    public function test_profil_sayfasinda_kullanici_adi_goruntulenir(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'name' => 'Ahmet Yılmaz',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->assertPathIs('/profile')
                    ->pause(1000);
        });
    }
}
