<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_misafir_kullanici_ogrenci_listesine_ulasamaz(): void
    {
        $group = Group::factory()->create();

        $this->get("/groups/{$group->id}/students")
            ->assertRedirect('/login');
    }

    public function test_giris_yapan_kullanici_ogrenci_listesini_gorebilir(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get("/groups/{$group->id}/students")
            ->assertOk();
    }

    public function test_manual_eklemede_ad_alani_zorunludur(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post("/groups/{$group->id}/students", [
            'import_type' => 'manual',
            'name' => '',
            'no' => '1001',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_excel_ile_ogrenci_eklenebilir(): void
    {
        Role::create(['name' => 'Student', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        Excel::shouldReceive('toArray')
            ->once()
            ->andReturn([[
                ['name', 'no'],
                ['Ali Veli', '2001'],
                ['Ayse Demir', '2002'],
            ]]);

        $file = UploadedFile::fake()->create(
            'ogrenciler.xlsx',
            10,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        $response = $this->actingAs($user)->post("/groups/{$group->id}/students", [
            'import_type' => 'excel',
            'file' => $file,
        ]);

        $response->assertRedirect(route('students.index', $group->id));

        $this->assertDatabaseHas('users', [
            'no' => '2001',
            'email' => '2001@dgn.com',
        ]);

        $student = User::where('no', '2001')->first();

        $this->assertDatabaseHas('group_user', [
            'group_id' => $group->id,
            'user_id' => $student->id,
        ]);
    }

    public function test_ogrenci_gruptan_cikarilabilir(): void
    {
        Role::create(['name' => 'admin']);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $group = Group::factory()->create(['user_id' => $user->id]);
        $student = User::factory()->create();

        $group->students()->attach($student->id);

        $response = $this->actingAs($user)
            ->delete("/groups/{$group->id}/students/{$student->id}");

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('group_user', [
            'group_id' => $group->id,
            'user_id' => $student->id,
        ]);
    }
}
