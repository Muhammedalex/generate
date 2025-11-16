<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();
        
        return [
            'user_id' => User::factory(),
            'slug' => Str::slug($name),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'website' => fake()->optional()->url(),
            'address' => fake()->optional()->address(),
            'primary_color' => fake()->hexColor(),
            'secondary_color' => fake()->hexColor(),
            'accent_color' => fake()->hexColor(),
            'logo_path' => null,
            'social_links' => [
                'facebook' => fake()->optional()->url(),
                'twitter' => fake()->optional()->url(),
                'instagram' => fake()->optional()->url(),
                'linkedin' => fake()->optional()->url(),
            ],
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the company is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the company is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}

