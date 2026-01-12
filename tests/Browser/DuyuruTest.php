<?php

namespace Tests\Browser;

use App\Models\Group;
use App\Models\GroupAnnouncement;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Spatie\Permission\Models\Role;
use Tests\DuskTestCase;

class DuyuruTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'student', 'guard_name' => 'web']);
    }

    /**
     * Test: Duyuru listesi sayfası görüntülenebilir.
     */
    public function test_duyuru_listesi_sayfasi_goruntulenebilir(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/announcements')
                    ->assertPathIs('/announcements')
                    ->pause(1000);
        });
    }

    /**
     * Test: Duyuru oluşturma sayfası görüntülenebilir.
     */
    public function test_duyuru_olusturma_sayfasi_goruntulenebilir(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('admin');
        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/announcements/create')
                    ->assertPathIs('/announcements/create')
                    ->pause(1000);
        });
    }

    /**
     * Test: Misafir kullanıcı duyuru sayfasına erişemez.
     */
    public function test_misafir_kullanici_duyuru_sayfasina_erisemez(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/announcements')
                    ->assertPathIs('/login');
        });
    }

    /**
     * Test: Duyuru detay sayfası görüntülenebilir.
     */
    public function test_duyuru_detay_sayfasi_goruntulenebilir(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('admin');
        $group = Group::factory()->create(['user_id' => $user->id]);
        $announcement = GroupAnnouncement::factory()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'title' => 'Test Duyurusu',
        ]);

        $this->browse(function (Browser $browser) use ($user, $announcement) {
            $browser->loginAs($user)
                    ->visit("/announcements/{$announcement->id}")
                    ->assertPathIs("/announcements/{$announcement->id}")
                    ->pause(1000);
        });
    }

    /**
     * Test: Duyuru oluşturulabilir.
     */
    public function test_duyuru_olusturulabilir(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('admin');
        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->browse(function (Browser $browser) use ($user, $group) {
            $browser->loginAs($user)
                    ->visit('/announcements/create')
                    ->assertPathIs('/announcements/create')
                    ->pause(1000)
                    ->select('group_id', (string)$group->id)
                    ->type('title', 'Yeni Duyuru Başlığı')
                    ->type('content', 'Bu bir test duyurusudur.')
                    ->pause(500)
                    ->press('Oluştur')
                    ->pause(2000)
                    ->assertPathIs('/announcements');
        });
    }
}
