<?php

namespace Database\Factories;

use App\Models\Faculty;
use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacultyFactory extends Factory
{
    protected $model = Faculty::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word() . ' Fakültesi',
            'university_id' => University::factory(),
        ];
    }
}
