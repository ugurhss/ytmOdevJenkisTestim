<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\GroupAnnouncement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupAnnouncementFactory extends Factory
{
    protected $model = GroupAnnouncement::class;

    public function definition(): array
    {
        return [
            'group_id' => Group::factory(),
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'content' => fake()->paragraph(3),
        ];
    }
}
