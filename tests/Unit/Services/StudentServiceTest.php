<?php

namespace Tests\Unit\Services;

use App\Repositories\Student\StudentRepository;
use App\Services\Student\StudentService;
use Mockery;
use Tests\TestCase;

class StudentServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function ogrenci_olusturulur_ve_gruba_eklenir(): void
    {
        $repo = Mockery::mock(StudentRepository::class);
        $student = Mockery::mock();

        $payload = [
            'name' => 'Test Ogrenci',
            'no' => '1001',
            'email' => 'ogrenci@example.com',
            'password' => 'password',
        ];

        $repo->shouldReceive('createAndAttach')
            ->once()
            ->with($payload, 10)
            ->andReturn($student);

        $student->shouldReceive('assignRole')
            ->once()
            ->with('Student');

        $service = new StudentService($repo);

        $result = $service->createAndAttachToGroup($payload, 10);

        $this->assertSame($student, $result);
    }
}
