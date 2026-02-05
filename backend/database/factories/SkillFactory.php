<?php

namespace Database\Factories;

use App\Models\Formation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'formation_id' => Formation::inRandomOrder()->value('id'),
            'code' => strtoupper($this->faker->bothify('C-###')),
            'title' => $this->faker->sentence(2),
            'description' => $this->faker->paragraph(),
        ];
    }
}
