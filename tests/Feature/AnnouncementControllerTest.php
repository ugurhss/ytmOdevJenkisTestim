<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupAnnouncement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AnnouncementControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'student', 'guard_name' => 'web']);
        Role::create(['name' => 'superadmin', 'guard_name' => 'web']);
    }

    /** @test */
    public function misafir_kullanici_duyuru_listesine_ulasamaz(): void
    {
        $this->get(route('announcements.index'))
            ->assertRedirect('/login');
    }

    /** @test */
    public function giris_yapan_kullanici_duyuru_listesini_gorebilir(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get(route('announcements.index'))
            ->assertOk();
    }

    /** @test */
    public function duyuru_olusturma_sayfasi_gosterilebilir(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('announcements.create'))
            ->assertOk();
    }

    /** @test */
    public function duyuru_olusturulabilir(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('announcements.store'), [
                'group_id' => $group->id,
                'title' => 'Test Duyurusu',
                'content' => 'Test içerik',
            ]);

        $response->assertRedirect(route('announcements.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('group_announcements', [
            'title' => 'Test Duyurusu',
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function duyuru_basligi_zorunludur(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('announcements.store'), [
                'group_id' => $group->id,
                'title' => '',
                'content' => 'Test içerik',
            ]);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function duyuru_icerigi_zorunludur(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('announcements.store'), [
                'group_id' => $group->id,
                'title' => 'Test Başlık',
                'content' => '',
            ]);

        $response->assertSessionHasErrors('content');
    }

    /** @test */
    public function duyuru_detay_sayfasi_gosterilebilir(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $group = Group::factory()->create(['user_id' => $user->id]);
        $announcement = GroupAnnouncement::factory()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('announcements.show', $announcement->id))
            ->assertOk();
    }

    /** @test */
    public function duyuru_guncellenebilir(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $group = Group::factory()->create(['user_id' => $user->id]);
        $announcement = GroupAnnouncement::factory()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'title' => 'Eski Başlık',
        ]);

        $response = $this->actingAs($user)
            ->put(route('announcements.update', $announcement->id), [
                'group_id' => $group->id,
                'title' => 'Yeni Başlık',
                'content' => 'Yeni içerik',
            ]);

        $response->assertRedirect(route('announcements.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('group_announcements', [
            'id' => $announcement->id,
            'title' => 'Yeni Başlık',
        ]);
    }

    /** @test */
    public function duyuru_silinebilir(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $group = Group::factory()->create(['user_id' => $user->id]);
        $announcement = GroupAnnouncement::factory()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->delete(route('announcements.destroy', $announcement->id));

        $response->assertRedirect(route('announcements.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('group_announcements', [
            'id' => $announcement->id,
        ]);
    }

    /** @test */
    public function baska_kullanicinin_duyurusu_silinebilir_sadece_yetkisi_varsa(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('admin');
        $otherUser = User::factory()->create();
        $otherUser->assignRole('admin');

        $group = Group::factory()->create(['user_id' => $owner->id]);
        $announcement = GroupAnnouncement::factory()->create([
            'group_id' => $group->id,
            'user_id' => $owner->id,
        ]);

        $response = $this->actingAs($otherUser)
            ->delete(route('announcements.destroy', $announcement->id));

        // Yetki kontrolü policy'de yapılıyor, bu test için beklenen davranışı kontrol et
        $this->assertDatabaseHas('group_announcements', [
            'id' => $announcement->id,
        ]);
    }
}
