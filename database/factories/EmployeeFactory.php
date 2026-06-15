<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'active' => true,
            'queue_position' => fake()->unique()->numberBetween(1, 1000),
            'hired_at' => fake()->optional()->date(),
            'dismissed_at' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'active' => false,
            'dismissed_at' => now()->toDateString(),
        ]);
    }
}
