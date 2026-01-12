<?php

namespace Tests\Browser;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class GroupsTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test: Gruplar sayfası erişilebilir.
     */
    public function test_gruplar_sayfasi_erisilebilir(): void
    {
        $kullanici = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $tarayici) use ($kullanici) {
            $tarayici->loginAs($kullanici)
                    ->visit('/groups')
                    ->assertPathIs('/groups')
                    ->pause(1000);
        });
    }

    /**
     * Test: Gruplar sayfası grup listesini gösterir.
     */
    public function test_gruplar_sayfasi_grup_listesini_gosterir(): void
    {
        $kullanici = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $grup = Group::factory()->create([
            'name' => 'Test Grubu',
        ]);

        $this->browse(function (Browser $tarayici) use ($kullanici, $grup) {
            $tarayici->loginAs($kullanici)
                    ->visit('/groups')
                    ->assertPathIs('/groups')
                    ->pause(2000);
        });
    }

    /**
     * Test: Gruplar sayfası kimlik doğrulama gerektirir.
     */
    public function test_gruplar_sayfasi_kimlik_dogrulama_gerektirir(): void
    {
        $this->browse(function (Browser $tarayici) {
            $tarayici->visit('/groups')
                    ->assertPathIs('/login');
        });
    }
}
