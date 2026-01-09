<?php

namespace Tests\Unit\Services;

use App\Models\Group;
use App\Models\User;
use App\Services\Group\GroupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
//unit

class GroupServiceTest extends TestCase
{
    use RefreshDatabase;

    protected GroupService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(GroupService::class);
    }

    /** @test */
    public function grup_olusturulur()
    {
        $user = User::factory()->create();

        $data = [
            'user_id'         => $user->id,
            'groups_name'     => 'Test Group',
            'city_id'         => 1,
            'university_id'   => 1,
            'faculty_id'      => 1,
            'department_id'   => 1,
            'class_models_id' => 1,
        ];

        $group = $this->service->create($data);

        $this->assertInstanceOf(Group::class, $group);
        $this->assertDatabaseHas('groups', [
            'groups_name' => 'Test Group',
        ]);
    }

    /** @test */
    public function id_ile_grup_getirilir()
    {
        $group = Group::factory()->create();

        $found = $this->service->getById($group->id);

        $this->assertEquals($group->id, $found->id);
    }

    /** @test */
    public function grup_silinir()
    {
        $group = Group::factory()->create();

        $this->service->delete($group->id);

        $this->assertDatabaseMissing('groups', [
            'id' => $group->id,
        ]);
    }
}
