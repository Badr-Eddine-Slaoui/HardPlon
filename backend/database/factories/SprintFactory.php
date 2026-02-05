<?php

namespace Database\Factories;

use App\Models\Formation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SprintFactory extends Factory
{
    protected static $sprintCount = 1;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'formation_id' => Formation::inRandomOrder()->value('id'),
            'name' => 'Sprint ' . self::$sprintCount++,
            'description' => $this->faker->paragraph(),
            'start_date' => now(),
            'end_date' => now()->addWeeks(2),
        ];
    }
}
