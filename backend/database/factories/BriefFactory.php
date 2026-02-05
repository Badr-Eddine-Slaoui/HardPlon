<?php

namespace Database\Factories;

use App\Models\ClassGroup;
use App\Models\Sprint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BriefFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sprint_id' => Sprint::inRandomOrder()->value('id'),
            'class_group_id' => ClassGroup::inRandomOrder()->value('id'),
            'teacher_id' => User::where('role', 'TEACHER')->inRandomOrder()->value('id'),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(3),
            'is_collective' => $this->faker->boolean(),
            'start_date' => now(),
            'end_date' => now()->addWeek(),
        ];
    }
}
