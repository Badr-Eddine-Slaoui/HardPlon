<?php

namespace Database\Factories;

use App\Models\ClassGroup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $role = $this->faker->randomElement(['ADMIN','STUDENT','TEACHER']);
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'age' => $this->faker->numberBetween(18, 50),
            'email' => $this->faker->unique()->safeEmail(),
            'cin' => $this->faker->unique()->bothify('HH######'),
            'phone' => $this->faker->unique()->phoneNumber(),
            'password' => Hash::make('password'),
            'role' => $role,
            'id_class' => $role == 'STUDENT' ? ClassGroup::inRandomOrder()->value('id') : null,
        ];
    }
}
