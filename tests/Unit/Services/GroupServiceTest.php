<?php

namespace Tests\Unit\Services;

use App\Models\Group;
use App\Models\User;
use App\Repositories\Group\GroupRepository;
use App\Services\Activity\ActivityLogService;
use App\Services\Group\GroupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class GroupServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function tum_gruplar_getirilebilir(): void
    {
        $repo = Mockery::mock(GroupRepository::class);
        $activity = Mockery::mock(ActivityLogService::class);

        $groups = collect([
            Group::factory()->make(['id' => 1]),
            Group::factory()->make(['id' => 2]),
        ]);

        $repo->shouldReceive('all')
            ->once()
            ->andReturn($groups);

        $service = new GroupService($repo, $activity);

        $result = $service->getAll();

        $this->assertCount(2, $result);
    }

    /** @test */
    public function id_ile_grup_getirilebilir(): void
    {
        $repo = Mockery::mock(GroupRepository::class);
        $activity = Mockery::mock(ActivityLogService::class);

        $group = Group::factory()->make(['id' => 1, 'groups_name' => 'Test Grubu']);

        $repo->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($group);

        $service = new GroupService($repo, $activity);

        $result = $service->getById(1);

        $this->assertInstanceOf(Group::class, $result);
        $this->assertEquals('Test Grubu', $result->groups_name);
    }

    /** @test */
    public function grup_guncellenebilir(): void
    {
        $repo = Mockery::mock(GroupRepository::class);
        $activity = Mockery::mock(ActivityLogService::class);

        $data = ['groups_name' => 'Güncellenmiş Grup Adı'];

        $repo->shouldReceive('update')
            ->once()
            ->with(1, $data)
            ->andReturn(true);

        $service = new GroupService($repo, $activity);

        $result = $service->update(1, $data);

        $this->assertTrue($result);
    }

    /** @test */
    public function grup_silinebilir(): void
    {
        $repo = Mockery::mock(GroupRepository::class);
        $activity = Mockery::mock(ActivityLogService::class);

        $repo->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $service = new GroupService($repo, $activity);

        $result = $service->delete(1);

        $this->assertTrue($result);
    }

    /** @test */
    public function kullanici_id_ile_gruplar_getirilebilir(): void
    {
        $repo = Mockery::mock(GroupRepository::class);
        $activity = Mockery::mock(ActivityLogService::class);

        $groups = collect([
            Group::factory()->make(['user_id' => 1]),
            Group::factory()->make(['user_id' => 1]),
        ]);

        $repo->shouldReceive('getGroupsByUserId')
            ->once()
            ->with(1)
            ->andReturn($groups);

        $service = new GroupService($repo, $activity);

        $result = $service->getGroupsByUser(1);

        $this->assertCount(2, $result);
    }

    /** @test */
    public function ogrenci_id_ile_gruplar_getirilebilir(): void
    {
        $repo = Mockery::mock(GroupRepository::class);
        $activity = Mockery::mock(ActivityLogService::class);

        $groups = collect([
            Group::factory()->make(),
            Group::factory()->make(),
        ]);

        $repo->shouldReceive('getGroupsByStudentId')
            ->once()
            ->with(5)
            ->andReturn($groups);

        $service = new GroupService($repo, $activity);

        $result = $service->getGroupsByStudent(5);

        $this->assertCount(2, $result);
    }

    /** @test */
    public function grup_ogrencileri_getirilebilir(): void
    {
        $repo = Mockery::mock(GroupRepository::class);
        $activity = Mockery::mock(ActivityLogService::class);

        $students = collect([
            User::factory()->make(['id' => 1]),
            User::factory()->make(['id' => 2]),
        ]);

        $repo->shouldReceive('getStudents')
            ->once()
            ->with(1)
            ->andReturn($students);

        $service = new GroupService($repo, $activity);

        $result = $service->getStudents(1);

        $this->assertCount(2, $result);
    }
}
