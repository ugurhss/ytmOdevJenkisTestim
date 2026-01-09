<?php

namespace Database\Factories;

use App\Models\ClassModel;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\University;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        // Zincir: University(city_id) -> Faculty(university_id) -> Department(faculty_id) -> ClassModel(department_id)
        $university = University::factory()->create();
        $faculty = Faculty::factory()->create(['university_id' => $university->id]);
        $department = Department::factory()->create(['faculty_id' => $faculty->id]);
        $classModel = ClassModel::factory()->create(['department_id' => $department->id]);

        return [
            'groups_name'     => fake()->words(2, true),
            'user_id'         => User::factory(),
            'city_id'         => $university->city_id,
            'university_id'   => $university->id,
            'faculty_id'      => $faculty->id,
            'department_id'   => $department->id,
            'class_models_id' => $classModel->id,
        ];
    }
}
