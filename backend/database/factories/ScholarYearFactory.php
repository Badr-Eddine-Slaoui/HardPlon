<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ScholarYearFactory extends Factory
{
    protected static $start = 2024;
    public function definition(): array
    {
        $start = self::$start++;

        return [
            'start_year' => $start,
            'end_year' => $start + 1,
            'capacity' => $this->faker->numberBetween(50, 300),
        ];
    }
}
