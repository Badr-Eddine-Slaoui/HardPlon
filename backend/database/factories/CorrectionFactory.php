<?php

namespace Database\Factories;

use App\Models\Brief;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CorrectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brief_id' => Brief::inRandomOrder()->value('id'),
            'student_id' => User::where('role', 'STUDENT')->inRandomOrder()->value('id'),
            'teacher_id' => User::where('role', 'TEACHER')->inRandomOrder()->value('id'),
            'message' => $this->faker->paragraph(),
        ];
    }
}
