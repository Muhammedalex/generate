<?php

namespace Database\Factories;

use App\Models\Form;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormResponse>
 */
class FormResponseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_id' => Form::factory(),
            'user_id' => fake()->optional()->randomElement([User::factory(), null]),
            'email' => fake()->optional()->email(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'status' => fake()->randomElement(['completed', 'partial', 'abandoned']),
            'submitted_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the response is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'submitted_at' => now(),
        ]);
    }
}
