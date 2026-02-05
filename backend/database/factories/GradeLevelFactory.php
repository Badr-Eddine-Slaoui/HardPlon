<?php

namespace Database\Factories;

use App\Models\ScholarYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class GradeLevelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'scholar_year_id' => ScholarYear::inRandomOrder()->value('id'),
            'name' => $this->faker->word(),
            'capacity' => $this->faker->numberBetween(20, 100),
            'description' => $this->faker->sentence(),
        ];
    }
}
