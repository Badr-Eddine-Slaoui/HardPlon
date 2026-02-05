<?php

namespace Database\Factories;

use App\Models\Sprint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SquadFactory extends Factory
{
    protected static $squadCount = 1;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sprint_id' => Sprint::inRandomOrder()->value('id'),
            'name' => 'Squad' . self::$squadCount++,
            'member_count' => $this->faker->numberBetween(5, 6),
        ];
    }
}
