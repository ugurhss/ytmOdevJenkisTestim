<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;


    /**
     * Test: Giriş sayfası yüklenir.
     */
    public function test_giris_sayfasi_yuklenir(): void
    {
        $this->browse(function (Browser $tarayici) {
            $tarayici->visit('/login')
                    ->assertSee('Log in')
                    ->assertPresent('input[name="email"]')
                    ->assertPresent('input[name="password"]');
        });
    }

    /**
     * Test: Kullanıcı giriş yapabilir.
     */
    public function test_kullanici_giris_yapabilir(): void
    {
        $kullanici = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $tarayici) use ($kullanici) {
            $tarayici->visit('/login')
                    ->type('email', $kullanici->email)
                    ->type('password', 'password')
                    ->press('Log in')
                    ->waitForLocation('/dashboard', 5)
                    ->assertPathIs('/dashboard');
        });
    }

    /**
     * Test: Kullanıcı geçersiz kimlik bilgileriyle giriş yapamaz.
     */
    public function test_kullanici_gecersiz_kimlik_bilgileriyle_giris_yapamaz(): void
    {
        $this->browse(function (Browser $tarayici) {
            $tarayici->visit('/login')
                    ->type('email', 'yanlis@example.com')
                    ->type('password', 'yanlis-sifre')
                    ->press('Log in')
                    ->pause(1000)
                    ->assertPathIs('/login')
                    ->assertSee('These credentials do not match our records');
        });
    }
}
