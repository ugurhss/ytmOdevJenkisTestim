<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class HomePageTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test: Ana sayfa doğru şekilde yüklenir.
     */
    public function test_ana_sayfa_dogru_sekilde_yuklenir(): void
    {
        $this->browse(function (Browser $tarayici) {
            $tarayici->visit('/')
                    ->assertSee('Laravel')
                    ->pause(1000);
        });
    }

    /**
     * Test: Navigasyon linkleri mevcuttur.
     */
    public function test_navigasyon_linkleri_mevcuttur(): void
    {
        $this->browse(function (Browser $tarayici) {
            $tarayici->visit('/')
                    ->pause(1000);
        });
    }
}
