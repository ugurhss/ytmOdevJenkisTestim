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
        $kullanici = User::factory()->create();
        $kullanici->assignRole('admin');

        $this->actingAs($kullanici)
            ->get(route('announcements.index'))
            ->assertOk();
    }

    /** @test */
    public function duyuru_olusturma_sayfasi_gosterilebilir(): void
    {
        $kullanici = User::factory()->create();
        $kullanici->assignRole('admin');
        $grup = Group::factory()->create(['user_id' => $kullanici->id]);

        $this->actingAs($kullanici)
            ->get(route('announcements.create'))
            ->assertOk();
    }

    /** @test */
    public function duyuru_olusturulabilir(): void
    {
        $kullanici = User::factory()->create();
        $kullanici->assignRole('admin');
        $grup = Group::factory()->create(['user_id' => $kullanici->id]);

        $yanit = $this->actingAs($kullanici)
            ->post(route('announcements.store'), [
                'group_id' => $grup->id,
                'title' => 'Test Duyurusu',
                'content' => 'Test içerik',
            ]);

        $yanit->assertRedirect(route('announcements.index'));
        $yanit->assertSessionHas('success');

        $this->assertDatabaseHas('group_announcements', [
            'title' => 'Test Duyurusu',
            'group_id' => $grup->id,
            'user_id' => $kullanici->id,
        ]);
    }

    /** @test */
    public function duyuru_basligi_zorunludur(): void
    {
        $kullanici = User::factory()->create();
        $kullanici->assignRole('admin');
        $grup = Group::factory()->create(['user_id' => $kullanici->id]);

        $yanit = $this->actingAs($kullanici)
            ->post(route('announcements.store'), [
                'group_id' => $grup->id,
                'title' => '',
                'content' => 'Test içerik',
            ]);

        $yanit->assertSessionHasErrors('title');
    }

    /** @test */
    public function duyuru_icerigi_zorunludur(): void
    {
        $kullanici = User::factory()->create();
        $kullanici->assignRole('admin');
        $grup = Group::factory()->create(['user_id' => $kullanici->id]);

        $yanit = $this->actingAs($kullanici)
            ->post(route('announcements.store'), [
                'group_id' => $grup->id,
                'title' => 'Test Başlık',
                'content' => '',
            ]);

        $yanit->assertSessionHasErrors('content');
    }

    /** @test */
    public function duyuru_detay_sayfasi_gosterilebilir(): void
    {
        $kullanici = User::factory()->create();
        $kullanici->assignRole('admin');
        $grup = Group::factory()->create(['user_id' => $kullanici->id]);
        $duyuru = GroupAnnouncement::factory()->create([
            'group_id' => $grup->id,
            'user_id' => $kullanici->id,
        ]);

        $this->actingAs($kullanici)
            ->get(route('announcements.show', $duyuru->id))
            ->assertOk();
    }

    /** @test */
    public function duyuru_guncellenebilir(): void
    {
        $kullanici = User::factory()->create();
        $kullanici->assignRole('admin');
        $grup = Group::factory()->create(['user_id' => $kullanici->id]);
        $duyuru = GroupAnnouncement::factory()->create([
            'group_id' => $grup->id,
            'user_id' => $kullanici->id,
            'title' => 'Eski Başlık',
        ]);

        $yanit = $this->actingAs($kullanici)
            ->put(route('announcements.update', $duyuru->id), [
                'group_id' => $grup->id,
                'title' => 'Yeni Başlık',
                'content' => 'Yeni içerik',
            ]);

        $yanit->assertRedirect(route('announcements.index'));
        $yanit->assertSessionHas('success');

        $this->assertDatabaseHas('group_announcements', [
            'id' => $duyuru->id,
            'title' => 'Yeni Başlık',
        ]);
    }

    /** @test */
    public function duyuru_silinebilir(): void
    {
        $kullanici = User::factory()->create();
        $kullanici->assignRole('admin');
        $grup = Group::factory()->create(['user_id' => $kullanici->id]);
        $duyuru = GroupAnnouncement::factory()->create([
            'group_id' => $grup->id,
            'user_id' => $kullanici->id,
        ]);

        $yanit = $this->actingAs($kullanici)
            ->delete(route('announcements.destroy', $duyuru->id));

        $yanit->assertRedirect(route('announcements.index'));
        $yanit->assertSessionHas('success');

        $this->assertDatabaseMissing('group_announcements', [
            'id' => $duyuru->id,
        ]);
    }

    /** @test */
    public function baska_kullanicinin_duyurusu_silinebilir_sadece_yetkisi_varsa(): void
    {
        $sahip = User::factory()->create();
        $sahip->assignRole('admin');
        $digerKullanici = User::factory()->create();
        $digerKullanici->assignRole('admin');

        $grup = Group::factory()->create(['user_id' => $sahip->id]);
        $duyuru = GroupAnnouncement::factory()->create([
            'group_id' => $grup->id,
            'user_id' => $sahip->id,
        ]);

        $yanit = $this->actingAs($digerKullanici)
            ->delete(route('announcements.destroy', $duyuru->id));

        // Yetki kontrolü policy'de yapılıyor, bu test için beklenen davranışı kontrol et
        $this->assertDatabaseHas('group_announcements', [
            'id' => $duyuru->id,
        ]);
    }
}
