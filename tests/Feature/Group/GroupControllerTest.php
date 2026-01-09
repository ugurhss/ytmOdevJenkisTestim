<?php

namespace Tests\Feature\Group;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_create_group()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('groups.store'), [
            'groups_name'     => 'Integration Group',
            'city_id'         => 1,
            'university_id'   => 1,
            'faculty_id'      => 1,
            'department_id'   => 1,
            'class_models_id' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('groups', [
            'groups_name' => 'Integration Group',
            'user_id'     => $user->id,
        ]);
    }

    /** @test */
    public function guest_cannot_create_group()
    {
        $response = $this->post(route('groups.store'), []);

        $response->assertRedirect('/login');
    }

    /** @test */
    public function group_name_is_required()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('groups.store'), [
            'groups_name' => '',
        ]);

        $response->assertSessionHasErrors('groups_name');
    }
}
