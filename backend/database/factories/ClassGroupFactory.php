<?php

namespace Database\Factories;

use App\Models\Formation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ClassGroupFactory extends Factory
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
            'name' => 'Class ' . $this->faker->randomLetter(),
            'image_url' => "https://wallpapers-clan.com/wp-content/uploads/2025/06/one-piece-luffy-fashionable-pirate-desktop-wallpaper-preview.jpg",
            'capacity' => $this->faker->numberBetween(10, 40),
            'description' => $this->faker->sentence(),
        ];
    }
}
