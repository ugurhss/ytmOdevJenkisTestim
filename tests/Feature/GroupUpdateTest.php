<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

use App\Models\City;
use App\Models\University;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\ClassModel;

class GroupUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::before(fn () => true);
    }

    public function test_user_can_update_group_name(): void
    {
        $user = User::factory()->create();

        // group create için zorunlu FK’ları hazırlıyoruz
        $group = Group::factory()->create([
            'user_id'         => $user->id,
            'city_id'         => City::factory()->create()->id,
            'university_id'   => University::factory()->create()->id,
            'faculty_id'      => Faculty::factory()->create()->id,
            'department_id'   => Department::factory()->create()->id,
            'class_models_id' => ClassModel::factory()->create()->id,
            'groups_name'     => 'Eski Ad',
        ]);

        $response = $this->actingAs($user)->put("/groups/{$group->id}", [
            'groups_name' => 'Yeni Ad',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'groups_name' => 'Yeni Ad',
        ]);
    }
}
