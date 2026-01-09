<?php

namespace Tests\Feature\Api;

use App\Models\City;
use App\Models\Faculty;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DependentDropdownTest extends TestCase
{
    use RefreshDatabase;

    public function test_sehir_universiteleri_json_doner(): void
    {
        $user = User::factory()->create();

        $city = City::factory()->create();
        $otherCity = City::factory()->create();

        $universityOne = University::factory()->create(['city_id' => $city->id]);
        $universityTwo = University::factory()->create(['city_id' => $city->id]);
        $otherUniversity = University::factory()->create(['city_id' => $otherCity->id]);

        $response = $this->actingAs($user)
            ->getJson("/api/universities/{$city->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $universityOne->id])
            ->assertJsonFragment(['id' => $universityTwo->id])
            ->assertJsonMissing(['id' => $otherUniversity->id]);
    }

    public function test_universite_fakulteleri_json_doner(): void
    {
        $user = User::factory()->create();

        $university = University::factory()->create();
        $otherUniversity = University::factory()->create();

        $facultyOne = Faculty::factory()->create(['university_id' => $university->id]);
        $facultyTwo = Faculty::factory()->create(['university_id' => $university->id]);
        $otherFaculty = Faculty::factory()->create(['university_id' => $otherUniversity->id]);

        $response = $this->actingAs($user)
            ->getJson("/api/faculties/{$university->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $facultyOne->id])
            ->assertJsonFragment(['id' => $facultyTwo->id])
            ->assertJsonMissing(['id' => $otherFaculty->id]);
    }
}
