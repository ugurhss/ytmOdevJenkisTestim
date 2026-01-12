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
        $user = User::factory()->create();
        $user->assignRole('admin');
        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('groups.index'))
            ->assertOk();
    }

    /** @test */
    public function grup_detay_sayfasi_gosterilebilir(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('groups.show', $group->id))
            ->assertOk();
    }

    /** @test */
    public function grup_olusturma_sayfasi_gosterilebilir(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get(route('groups.create'))
            ->assertOk();
    }

    /** @test */
    public function grup_guncelleme_sayfasi_gosterilebilir(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('groups.edit', $group->id))
            ->assertOk();
    }

    /** @test */
    public function grup_guncellenebilir(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $group = Group::factory()->create([
            'user_id' => $user->id,
            'groups_name' => 'Eski Grup Adı',
        ]);

        $response = $this->actingAs($user)
            ->put(route('groups.update', $group->id), [
                'groups_name' => 'Yeni Grup Adı',
                'city_id' => $group->city_id,
                'university_id' => $group->university_id,
                'faculty_id' => $group->faculty_id,
                'department_id' => $group->department_id,
                'class_models_id' => $group->class_models_id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'groups_name' => 'Yeni Grup Adı',
        ]);
    }

    /** @test */
    public function grup_silinebilir(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->delete(route('groups.destroy', $group->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('groups', [
            'id' => $group->id,
        ]);
    }

    /** @test */
    public function baska_kullanicinin_grubu_silinebilir_sadece_yetkisi_varsa(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('admin');
        $otherUser = User::factory()->create();
        $otherUser->assignRole('admin');

        $group = Group::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)
            ->delete(route('groups.destroy', $group->id));

        // Policy kontrolü yapılıyor, bu test için beklenen davranışı kontrol et
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
        ]);
    }

    /** @test */
    public function grup_adi_guncellemede_zorunludur(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->put(route('groups.update', $group->id), [
                'groups_name' => '',
                'city_id' => $group->city_id,
                'university_id' => $group->university_id,
                'faculty_id' => $group->faculty_id,
                'department_id' => $group->department_id,
                'class_models_id' => $group->class_models_id,
            ]);

        $response->assertSessionHasErrors('groups_name');
    }
}
