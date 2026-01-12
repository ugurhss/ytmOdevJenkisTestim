<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DashboardTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test: Giriş yaptıktan sonra dashboard erişilebilir.
     */
    public function test_giris_yaptiktan_sonra_dashboard_erisilebilir(): void
    {
        $kullanici = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $tarayici) use ($kullanici) {
            $tarayici->loginAs($kullanici)
                    ->visit('/dashboard')
                    ->assertPathIs('/dashboard')
                    ->assertSee('Dashboard');
        });
    }

    /**
     * Test: Dashboard kimlik doğrulama gerektirir.
     */
    public function test_dashboard_kimlik_dogrulama_gerektirir(): void
    {
        $this->browse(function (Browser $tarayici) {
            $tarayici->visit('/dashboard')
                    ->assertPathIs('/login');
        });
    }
}
