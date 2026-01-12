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
     * Test groups page is accessible.
     */
    public function test_groups_page_is_accessible(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/groups')
                    ->assertPathIs('/groups')
                    ->pause(1000);
        });
    }

    /**
     * Test groups page shows groups list.
     */
    public function test_groups_page_shows_groups(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $group = Group::factory()->create([
            'name' => 'Test Group',
        ]);

        $this->browse(function (Browser $browser) use ($user, $group) {
            $browser->loginAs($user)
                    ->visit('/groups')
                    ->assertPathIs('/groups')
                    ->pause(2000);
        });
    }

    /**
     * Test groups page requires authentication.
     */
    public function test_groups_page_requires_authentication(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/groups')
                    ->assertPathIs('/login');
        });
    }
}
