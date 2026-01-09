<?php

namespace Database\Factories;

use App\Models\ClassModel;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassModelFactory extends Factory
{
    protected $model = ClassModel::class;

    public function definition(): array
    {
        return [
            'name' => (string) fake()->numberBetween(1, 4),

            'department_id' => Department::factory(),

        ];
    }
}
