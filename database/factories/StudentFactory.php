<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'class_name' => fake()->randomElement(['1', '2', '3', '4', '5']),
            'section' => fake()->randomElement(['A', 'B', 'C']),
            'gender' => fake()->randomElement(['male', 'female']),
            'date_of_birth' => fake()->date(),
            'address' => fake()->address(),
            'guardian_name' => fake()->name(),
            'guardian_phone' => fake()->phoneNumber(),
            'status' => 'active',
        ];
    }
}
