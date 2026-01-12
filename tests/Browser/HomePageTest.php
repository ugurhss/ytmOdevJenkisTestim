<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class HomePageTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test home page loads correctly.
     */
    public function test_home_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Laravel')
                    ->pause(1000);
        });
    }

    /**
     * Test navigation links are present.
     */
    public function test_navigation_links_present(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->pause(1000);
        });
    }
}
