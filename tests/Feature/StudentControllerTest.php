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

    /**
     * Test: Misafir kullanıcı öğrenci listesine ulaşamaz.
     */
    public function test_misafir_kullanici_ogrenci_listesine_ulasamaz(): void
    {
        $grup = Group::factory()->create();

        $this->get("/groups/{$grup->id}/students")
            ->assertRedirect('/login');
    }

    /**
     * Test: Giriş yapan kullanıcı öğrenci listesini görebilir.
     */
    public function test_giris_yapan_kullanici_ogrenci_listesini_gorebilir(): void
    {
        $kullanici = User::factory()->create();
        $grup = Group::factory()->create(['user_id' => $kullanici->id]);

        $this->actingAs($kullanici)
            ->get("/groups/{$grup->id}/students")
            ->assertOk();
    }

    /**
     * Test: Manuel eklemede ad alanı zorunludur.
     */
    public function test_manuel_eklemede_ad_alani_zorunludur(): void
    {
        $kullanici = User::factory()->create();
        $grup = Group::factory()->create(['user_id' => $kullanici->id]);

        $yanit = $this->actingAs($kullanici)->post("/groups/{$grup->id}/students", [
            'import_type' => 'manual',
            'name' => '',
            'no' => '1001',
        ]);

        $yanit->assertSessionHasErrors('name');
    }

    /**
     * Test: Excel ile öğrenci eklenebilir.
     */
    public function test_excel_ile_ogrenci_eklenebilir(): void
    {
        Role::create(['name' => 'Student', 'guard_name' => 'web']);

        $kullanici = User::factory()->create();
        $grup = Group::factory()->create(['user_id' => $kullanici->id]);

        Excel::shouldReceive('toArray')
            ->once()
            ->andReturn([[
                ['name', 'no'],
                ['Ali Veli', '2001'],
                ['Ayşe Demir', '2002'],
            ]]);

        $dosya = UploadedFile::fake()->create(
            'ogrenciler.xlsx',
            10,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        $yanit = $this->actingAs($kullanici)->post("/groups/{$grup->id}/students", [
            'import_type' => 'excel',
            'file' => $dosya,
        ]);

        $yanit->assertRedirect(route('students.index', $grup->id));

        $this->assertDatabaseHas('users', [
            'no' => '2001',
            'email' => '2001@dgn.com',
        ]);

        $ogrenci = User::where('no', '2001')->first();

        $this->assertDatabaseHas('group_user', [
            'group_id' => $grup->id,
            'user_id' => $ogrenci->id,
        ]);
    }

    /**
     * Test: Öğrenci gruptan çıkarılabilir.
     */
    public function test_ogrenci_gruptan_cikarilabilir(): void
    {
        Role::create(['name' => 'admin']);

        $kullanici = User::factory()->create();
        $kullanici->assignRole('admin');

        $grup = Group::factory()->create(['user_id' => $kullanici->id]);
        $ogrenci = User::factory()->create();

        $grup->students()->attach($ogrenci->id);

        $yanit = $this->actingAs($kullanici)
            ->delete("/groups/{$grup->id}/students/{$ogrenci->id}");

        $yanit->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('group_user', [
            'group_id' => $grup->id,
            'user_id' => $ogrenci->id,
        ]);
    }
}
