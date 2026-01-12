<?php

namespace Tests\Unit\Services;

use App\Models\Group;
use App\Models\GroupAnnouncement;
use App\Models\User;
use App\Repositories\Announcements\AnnouncementsRepository;
use App\Services\Activity\ActivityLogService;
use App\Services\Announcements\AnnouncementService;
use App\Services\Group\GroupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class AnnouncementServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function duyuru_olusturuldugunda_aktivite_loglanir(): void
    {
        $announcementRepo = Mockery::mock(AnnouncementsRepository::class);
        $groupService = Mockery::mock(GroupService::class);
        $activityLogService = Mockery::mock(ActivityLogService::class);

        $user = User::factory()->create();
        $group = Group::factory()->create();

        $data = [
            'group_id' => $group->id,
            'title' => 'Test Duyuru',
            'content' => 'Test içerik',
        ];

        $announcement = new GroupAnnouncement($data);
        $announcement->id = 1;

        $groupService->shouldReceive('getById')
            ->never();

        $announcementRepo->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) use ($data, $user) {
                return $arg['group_id'] === $data['group_id']
                    && $arg['title'] === $data['title']
                    && $arg['user_id'] === $user->id;
            }))
            ->andReturn($announcement);

        $activityLogService->shouldReceive('logAnnouncementCreated')
            ->once()
            ->with($announcement, $user);

        $service = new AnnouncementService($announcementRepo, $groupService, $activityLogService);

        $result = $service->createWithRelations($data, $user);

        $this->assertInstanceOf(GroupAnnouncement::class, $result);
        $this->assertEquals('Test Duyuru', $result->title);
    }

    /** @test */
    public function duyuru_id_ile_bulunabilir(): void
    {
        $announcementRepo = Mockery::mock(AnnouncementsRepository::class);
        $groupService = Mockery::mock(GroupService::class);
        $activityLogService = Mockery::mock(ActivityLogService::class);

        $announcement = new GroupAnnouncement(['title' => 'Test']);
        $announcement->id = 1;

        $announcementRepo->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($announcement);

        $service = new AnnouncementService($announcementRepo, $groupService, $activityLogService);

        $result = $service->findById(1);

        $this->assertInstanceOf(GroupAnnouncement::class, $result);
        $this->assertEquals(1, $result->id);
    }

    /** @test */
    public function duyuru_bulunamadiginda_404_hatasi_firlatir(): void
    {
        $announcementRepo = Mockery::mock(AnnouncementsRepository::class);
        $groupService = Mockery::mock(GroupService::class);
        $activityLogService = Mockery::mock(ActivityLogService::class);

        $announcementRepo->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $service = new AnnouncementService($announcementRepo, $groupService, $activityLogService);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Duyuru bulunamadı.');

        $service->findById(999);
    }

    /** @test */
    public function duyuru_guncellenebilir(): void
    {
        $announcementRepo = Mockery::mock(AnnouncementsRepository::class);
        $groupService = Mockery::mock(GroupService::class);
        $activityLogService = Mockery::mock(ActivityLogService::class);

        $data = [
            'title' => 'Güncellenmiş Başlık',
            'content' => 'Güncellenmiş içerik',
        ];

        $announcementRepo->shouldReceive('update')
            ->once()
            ->with(1, $data)
            ->andReturn(true);

        $service = new AnnouncementService($announcementRepo, $groupService, $activityLogService);

        $result = $service->updateWithRelations(1, $data);

        $this->assertTrue($result);
    }

    /** @test */
    public function duyuru_silinebilir(): void
    {
        $announcementRepo = Mockery::mock(AnnouncementsRepository::class);
        $groupService = Mockery::mock(GroupService::class);
        $activityLogService = Mockery::mock(ActivityLogService::class);

        $announcementRepo->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $service = new AnnouncementService($announcementRepo, $groupService, $activityLogService);

        $result = $service->delete(1);

        $this->assertTrue($result);
    }

    /** @test */
    public function superadmin_tum_gruplari_gorebilir(): void
    {
        $announcementRepo = Mockery::mock(AnnouncementsRepository::class);
        $groupService = Mockery::mock(GroupService::class);
        $activityLogService = Mockery::mock(ActivityLogService::class);

        $user = User::factory()->create();
        $user->assignRole('superadmin');

        $groups = collect([
            Group::factory()->make(['id' => 1]),
            Group::factory()->make(['id' => 2]),
        ]);

        $request = Request::create('/announcements', 'GET');

        $groupService->shouldReceive('getAll')
            ->once()
            ->andReturn($groups);

        $announcementRepo->shouldReceive('getPaginated')
            ->once()
            ->with(Mockery::on(function ($filters) {
                return !isset($filters['group_ids']);
            }), 15)
            ->andReturn(collect([]));

        $service = new AnnouncementService($announcementRepo, $groupService, $activityLogService);

        $result = $service->getIndexData($user, $request);

        $this->assertArrayHasKey('announcements', $result);
        $this->assertArrayHasKey('groups', $result);
        $this->assertArrayHasKey('filters', $result);
    }
}
