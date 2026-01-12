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
        $user = User::factory()->create();
        $user->assignRole('superadmin');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $student = User::factory()->create();
        $student->assignRole('student');

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('SuperAdmin/Dashboard')
            ->has('stats')
            ->has('groups')
            ->has('logs')
        );
    }

    /** @test */
    public function admin_dashboard_gorebilir(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Dashboard')
            ->has('stats')
            ->has('groups')
        );
    }

    /** @test */
    public function ogrenci_dashboard_gorebilir(): void
    {
        $user = User::factory()->create();
        $user->assignRole('student');
        $group = Group::factory()->create();
        $group->students()->attach($user->id);

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('User/Dashboard')
            ->has('user')
            ->has('groups')
        );
    }

    /** @test */
    public function superadmin_dashboard_istatistikleri_gosterir(): void
    {
        $user = User::factory()->create();
        $user->assignRole('superadmin');

        User::factory()->count(3)->create()->each(fn ($u) => $u->assignRole('admin'));
        User::factory()->count(5)->create()->each(fn ($u) => $u->assignRole('student'));

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
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

        $otherAdmin = User::factory()->create();
        $otherAdmin->assignRole('admin');

        $ownGroup = Group::factory()->create(['user_id' => $admin->id]);
        $otherGroup = Group::factory()->create(['user_id' => $otherAdmin->id]);

        $response = $this->actingAs($admin)
            ->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('groups', 1)
            ->where('groups.0.id', $ownGroup->id)
        );
    }
}
