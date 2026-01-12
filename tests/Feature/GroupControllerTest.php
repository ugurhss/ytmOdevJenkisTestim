<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
    }

    /** @test */
    public function grup_listesi_gosterilebilir(): void
    {
        $kullanici = User::factory()->create();
        $kullanici->assignRole('admin');
        $grup = Group::factory()->create(['user_id' => $kullanici->id]);

        $this->actingAs($kullanici)
            ->get(route('groups.index'))
            ->assertOk();
    }

    /** @test */
    public function grup_detay_sayfasi_gosterilebilir(): void
    {
        $kullanici = User::factory()->create();
        $kullanici->assignRole('admin');
        $grup = Group::factory()->create(['user_id' => $kullanici->id]);

        $this->actingAs($kullanici)
            ->get(route('groups.show', $grup->id))
            ->assertOk();
    }

    /** @test */
    public function grup_olusturma_sayfasi_gosterilebilir(): void
    {
        $kullanici = User::factory()->create();
        $kullanici->assignRole('admin');

        $this->actingAs($kullanici)
            ->get(route('groups.create'))
            ->assertOk();
    }

    /** @test */
    public function grup_guncelleme_sayfasi_gosterilebilir(): void
    {
        $kullanici = User::factory()->create();
        $kullanici->assignRole('admin');
        $grup = Group::factory()->create(['user_id' => $kullanici->id]);

        $this->actingAs($kullanici)
            ->get(route('groups.edit', $grup->id))
            ->assertOk();
    }

    /** @test */
    public function grup_guncellenebilir(): void
    {
        $kullanici = User::factory()->create();
        $kullanici->assignRole('admin');
        $grup = Group::factory()->create([
            'user_id' => $kullanici->id,
            'groups_name' => 'Eski Grup Adı',
        ]);

        $yanit = $this->actingAs($kullanici)
            ->put(route('groups.update', $grup->id), [
                'groups_name' => 'Yeni Grup Adı',
                'city_id' => $grup->city_id,
                'university_id' => $grup->university_id,
                'faculty_id' => $grup->faculty_id,
                'department_id' => $grup->department_id,
                'class_models_id' => $grup->class_models_id,
            ]);

        $yanit->assertRedirect();
        $this->assertDatabaseHas('groups', [
            'id' => $grup->id,
            'groups_name' => 'Yeni Grup Adı',
        ]);
    }

    /** @test */
    public function grup_silinebilir(): void
    {
        $kullanici = User::factory()->create();
        $kullanici->assignRole('admin');
        $grup = Group::factory()->create(['user_id' => $kullanici->id]);

        $yanit = $this->actingAs($kullanici)
            ->delete(route('groups.destroy', $grup->id));

        $yanit->assertRedirect();
        $this->assertDatabaseMissing('groups', [
            'id' => $grup->id,
        ]);
    }

    /** @test */
    public function baska_kullanicinin_grubu_silinebilir_sadece_yetkisi_varsa(): void
    {
        $sahip = User::factory()->create();
        $sahip->assignRole('admin');
        $digerKullanici = User::factory()->create();
        $digerKullanici->assignRole('admin');

        $grup = Group::factory()->create(['user_id' => $sahip->id]);

        $yanit = $this->actingAs($digerKullanici)
            ->delete(route('groups.destroy', $grup->id));

        // Policy kontrolü yapılıyor, bu test için beklenen davranışı kontrol et
        $this->assertDatabaseHas('groups', [
            'id' => $grup->id,
        ]);
    }

    /** @test */
    public function grup_adi_guncellemede_zorunludur(): void
    {
        $kullanici = User::factory()->create();
        $kullanici->assignRole('admin');
        $grup = Group::factory()->create(['user_id' => $kullanici->id]);

        $yanit = $this->actingAs($kullanici)
            ->put(route('groups.update', $grup->id), [
                'groups_name' => '',
                'city_id' => $grup->city_id,
                'university_id' => $grup->university_id,
                'faculty_id' => $grup->faculty_id,
                'department_id' => $grup->department_id,
                'class_models_id' => $grup->class_models_id,
            ]);

        $yanit->assertSessionHasErrors('groups_name');
    }
}
