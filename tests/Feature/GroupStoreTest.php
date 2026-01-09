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

    public function test_authenticated_user_can_create_group_with_valid_foreign_keys(): void
    {
        $user = User::factory()->create();

        // ✅ exists kuralları için ilgili tabloları dolduruyoruz
        $city       = City::factory()->create();
        $university = University::factory()->create();
        $faculty    = Faculty::factory()->create();
        $department = Department::factory()->create();
        $classModel = ClassModel::factory()->create();

        $payload = [
            'groups_name'     => 'CI Test Grup',
            'city_id'         => $city->id,
            'university_id'   => $university->id,
            'faculty_id'      => $faculty->id,
            'department_id'   => $department->id,
            'class_models_id' => $classModel->id,
        ];

        $response = $this->actingAs($user)->post('/groups', $payload);

        $response->assertStatus(302);

        $this->assertDatabaseHas('groups', [
            'groups_name' => 'CI Test Grup',
            'user_id'     => $user->id,
            'city_id'     => $city->id,
        ]);
    }
}
