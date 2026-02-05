<?php

namespace Database\Factories;

use App\Models\BriefSkillLevel;
use App\Models\Correction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CorrectionDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'correction_id' => Correction::inRandomOrder()->value('id'),
            'brief_skill_level_id' => BriefSkillLevel::inRandomOrder()->value('id'),
            'grade' => $this->faker->randomElement(['POOR','AVERAGE','EXCELLENT']),
        ];
    }
}
