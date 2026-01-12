<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
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
    public function misafir_kullanici_dashboarda_ulasamaz(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect('/login');
    }

    /** @test */
    public function superadmin_dashboard_gorebilir(): void
    {
        $kullanici = User::factory()->create();
        $kullanici->assignRole('superadmin');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $ogrenci = User::factory()->create();
        $ogrenci->assignRole('student');

        $yanit = $this->actingAs($kullanici)
            ->get(route('dashboard'));

        $yanit->assertOk();
        $yanit->assertInertia(fn ($sayfa) => $sayfa
            ->component('SuperAdmin/Dashboard')
            ->has('stats')
            ->has('groups')
            ->has('logs')
        );
    }

    /** @test */
    public function admin_dashboard_gorebilir(): void
    {
        $kullanici = User::factory()->create();
        $kullanici->assignRole('admin');
        $grup = Group::factory()->create(['user_id' => $kullanici->id]);

        $yanit = $this->actingAs($kullanici)
            ->get(route('dashboard'));

        $yanit->assertOk();
        $yanit->assertInertia(fn ($sayfa) => $sayfa
            ->component('Admin/Dashboard')
            ->has('stats')
            ->has('groups')
        );
    }

    /** @test */
    public function ogrenci_dashboard_gorebilir(): void
    {
        $kullanici = User::factory()->create();
        $kullanici->assignRole('student');
        $grup = Group::factory()->create();
        $grup->students()->attach($kullanici->id);

        $yanit = $this->actingAs($kullanici)
            ->get(route('dashboard'));

        $yanit->assertOk();
        $yanit->assertInertia(fn ($sayfa) => $sayfa
            ->component('User/Dashboard')
            ->has('user')
            ->has('groups')
        );
    }

    /** @test */
    public function superadmin_dashboard_istatistikleri_gosterir(): void
    {
        $kullanici = User::factory()->create();
        $kullanici->assignRole('superadmin');

        User::factory()->count(3)->create()->each(fn ($k) => $k->assignRole('admin'));
        User::factory()->count(5)->create()->each(fn ($k) => $k->assignRole('student'));

        $yanit = $this->actingAs($kullanici)
            ->get(route('dashboard'));

        $yanit->assertOk();
        $yanit->assertInertia(fn ($sayfa) => $sayfa
            ->where('stats.total_users', 9) // 1 superadmin + 3 admin + 5 student
            ->where('stats.total_admins', 3)
            ->where('stats.total_students', 5)
        );
    }

    /** @test */
    public function admin_sadece_kendi_gruplarini_gorebilir(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $digerAdmin = User::factory()->create();
        $digerAdmin->assignRole('admin');

        $kendiGrubu = Group::factory()->create(['user_id' => $admin->id]);
        $digerGrup = Group::factory()->create(['user_id' => $digerAdmin->id]);

        $yanit = $this->actingAs($admin)
            ->get(route('dashboard'));

        $yanit->assertOk();
        $yanit->assertInertia(fn ($sayfa) => $sayfa
            ->has('groups', 1)
            ->where('groups.0.id', $kendiGrubu->id)
        );
    }
}
