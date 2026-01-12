<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

use App\Models\City;
use App\Models\University;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\ClassModel;

class GroupStoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Policy/authorize testte engel olmasın
        Gate::before(fn () => true);
    }

    /**
     * Test: Giriş yapmış kullanıcı geçerli foreign key'lerle grup oluşturabilir.
     */
    public function test_giris_yapmis_kullanici_gecerli_foreign_keylerle_grup_olusturabilir(): void
    {
        $kullanici = User::factory()->create();

        // ✅ exists kuralları için ilgili tabloları dolduruyoruz
        $sehir       = City::factory()->create();
        $universite = University::factory()->create();
        $fakulte    = Faculty::factory()->create();
        $bolum = Department::factory()->create();
        $sinif = ClassModel::factory()->create();

        $veri = [
            'groups_name'     => 'CI Test Grup',
            'city_id'         => $sehir->id,
            'university_id'   => $universite->id,
            'faculty_id'      => $fakulte->id,
            'department_id'   => $bolum->id,
            'class_models_id' => $sinif->id,
        ];

        $yanit = $this->actingAs($kullanici)->post('/groups', $veri);

        $yanit->assertStatus(302);

        $this->assertDatabaseHas('groups', [
            'groups_name' => 'CI Test Grup',
            'user_id'     => $kullanici->id,
            'city_id'     => $sehir->id,
        ]);
    }
}
