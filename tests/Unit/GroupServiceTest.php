<?php

namespace Tests\Unit;

use App\Models\Group;
use App\Repositories\Group\GroupRepository;
use App\Services\Activity\ActivityLogService;
use App\Services\Group\GroupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase; // ✅ PHPUnit değil!

class GroupServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_calls_repository_and_logs_activity(): void
    {
        $repo = Mockery::mock(GroupRepository::class);
        $activity = Mockery::mock(ActivityLogService::class);

        $group = new Group([
            'groups_name'     => 'Test Grup',
            'user_id'         => 1,
            'city_id'         => 1,
            'university_id'   => 1,
            'faculty_id'      => 1,
            'department_id'   => 1,
            'class_models_id' => 1,
        ]);

        $payload = $group->toArray();

        $repo->shouldReceive('create')
            ->once()
            ->with($payload)
            ->andReturn($group);

        $activity->shouldReceive('logGroupCreated')
            ->once()
            ->with($group, Mockery::any());

        $service = new GroupService($repo, $activity);

        $result = $service->create($payload);

        $this->assertInstanceOf(Group::class, $result);
        $this->assertSame('Test Grup', $result->groups_name);
    }
}
