<?php

namespace Database\Factories;

use App\Models\Brief;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SubmittingFactory extends Factory
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
            'squad_id' => null,
            'message' => $this->faker->sentence(),
            'links' => json_encode(['github' => $this->faker->url()]),
        ];
    }
}
