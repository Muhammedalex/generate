<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Form>
 */
class FormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'slug' => Str::slug(fake()->sentence(3)),
            'status' => fake()->randomElement(['draft', 'published', 'closed']),
            'settings' => null,
            'appearance' => null,
            'allow_multiple' => fake()->boolean(),
            'require_auth' => fake()->boolean(30),
            'collect_email' => fake()->boolean(70),
            'show_progress' => true,
            'randomize_questions' => false,
            'expires_at' => fake()->optional()->dateTimeBetween('now', '+1 year'),
            'starts_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'redirect_url' => fake()->optional()->url(),
        ];
    }

    /**
     * Indicate that the form is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    /**
     * Indicate that the form is draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }
}
