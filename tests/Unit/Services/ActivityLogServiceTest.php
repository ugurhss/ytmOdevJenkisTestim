<?php

namespace Tests\Unit\Services;

use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\GroupAnnouncement;
use App\Models\User;
use App\Services\Activity\ActivityLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function grup_olusturuldugunda_aktivite_loglanir(): void
    {
        $user = User::factory()->create(['name' => 'Test Kullanıcı']);
        $group = Group::factory()->create([
            'groups_name' => 'Test Grubu',
            'user_id' => $user->id,
        ]);

        $service = new ActivityLogService();
        $service->logGroupCreated($group, $user);

        $this->assertDatabaseHas('activity_logs', [
            'event' => 'group_created',
            'actor_id' => $user->id,
            'actor_name' => 'Test Kullanıcı',
            'subject_type' => Group::class,
            'subject_id' => $group->id,
        ]);

        $log = ActivityLog::where('event', 'group_created')->first();
        $this->assertStringContainsString('Test Kullanıcı', $log->description);
        $this->assertStringContainsString('Test Grubu', $log->description);
    }

    /** @test */
    public function grup_olusturuldugunda_aktör_yoksa_sistem_olarak_loglanir(): void
    {
        $group = Group::factory()->create(['groups_name' => 'Test Grubu']);

        $service = new ActivityLogService();
        $service->logGroupCreated($group, null);

        $this->assertDatabaseHas('activity_logs', [
            'event' => 'group_created',
            'actor_id' => null,
            'actor_name' => 'Sistem',
        ]);
    }

    /** @test */
    public function ogrenci_gruba_eklendiginde_aktivite_loglanir(): void
    {
        $user = User::factory()->create(['name' => 'Admin']);
        $student = User::factory()->create([
            'name' => 'Öğrenci Adı',
            'no' => '12345',
        ]);
        $groupId = 1;

        $service = new ActivityLogService();
        $service->logStudentAddedToGroup($groupId, $student, $user);

        $this->assertDatabaseHas('activity_logs', [
            'event' => 'student_added_to_group',
            'actor_id' => $user->id,
            'actor_name' => 'Admin',
            'subject_type' => User::class,
            'subject_id' => $student->id,
        ]);

        $log = ActivityLog::where('event', 'student_added_to_group')->first();
        $this->assertStringContainsString('12345', $log->description);
        $this->assertStringContainsString('Öğrenci Adı', $log->description);
    }

    /** @test */
    public function duyuru_olusturuldugunda_aktivite_loglanir(): void
    {
        $user = User::factory()->create(['name' => 'Duyuru Oluşturan']);
        $group = Group::factory()->create();
        $announcement = GroupAnnouncement::factory()->create([
            'title' => 'Test Duyurusu',
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        $service = new ActivityLogService();
        $service->logAnnouncementCreated($announcement, $user);

        $this->assertDatabaseHas('activity_logs', [
            'event' => 'announcement_created',
            'actor_id' => $user->id,
            'actor_name' => 'Duyuru Oluşturan',
            'subject_type' => GroupAnnouncement::class,
            'subject_id' => $announcement->id,
        ]);

        $log = ActivityLog::where('event', 'announcement_created')->first();
        $this->assertStringContainsString('Test Duyurusu', $log->description);
    }

    /** @test */
    public function yetkisiz_grup_erisimi_loglanir(): void
    {
        $user = User::factory()->create(['name' => 'Yetkisiz Kullanıcı']);
        $group = Group::factory()->create(['groups_name' => 'Test Grubu']);

        $service = new ActivityLogService();
        $service->logUnauthorizedGroupAccess($user, $group, 'güncelleme');

        $this->assertDatabaseHas('activity_logs', [
            'event' => 'unauthorized_group_access',
            'actor_id' => $user->id,
            'actor_name' => 'Yetkisiz Kullanıcı',
            'subject_type' => Group::class,
            'subject_id' => $group->id,
        ]);

        $log = ActivityLog::where('event', 'unauthorized_group_access')->first();
        $this->assertStringContainsString('güncelleme', $log->description);
        $this->assertStringContainsString('Test Grubu', $log->description);
    }

    /** @test */
    public function yetkisiz_duyuru_erisimi_loglanir(): void
    {
        $user = User::factory()->create(['name' => 'Yetkisiz Kullanıcı']);
        $announcement = GroupAnnouncement::factory()->create(['title' => 'Test Duyurusu']);

        $service = new ActivityLogService();
        $service->logUnauthorizedAnnouncementAccess($user, $announcement, 'silme');

        $this->assertDatabaseHas('activity_logs', [
            'event' => 'unauthorized_announcement_access',
            'actor_id' => $user->id,
            'actor_name' => 'Yetkisiz Kullanıcı',
            'subject_type' => GroupAnnouncement::class,
            'subject_id' => $announcement->id,
        ]);

        $log = ActivityLog::where('event', 'unauthorized_announcement_access')->first();
        $this->assertStringContainsString('silme', $log->description);
        $this->assertStringContainsString('Test Duyurusu', $log->description);
    }
}
