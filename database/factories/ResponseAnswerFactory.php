<?php

namespace Database\Factories;

use App\Models\FormResponse;
use App\Models\FormQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ResponseAnswer>
 */
class ResponseAnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'response_id' => FormResponse::factory(),
            'question_id' => FormQuestion::factory(),
            'answer_text' => fake()->sentence(),
            'answer_number' => null,
            'answer_boolean' => null,
            'answer_date' => null,
            'answer_json' => null,
            'file_path' => null,
        ];
    }

    /**
     * Set answer as text.
     */
    public function text(string $text = null): static
    {
        return $this->state(fn (array $attributes) => [
            'answer_text' => $text ?? fake()->sentence(),
            'answer_number' => null,
            'answer_boolean' => null,
            'answer_date' => null,
            'answer_json' => null,
            'file_path' => null,
        ]);
    }

    /**
     * Set answer as number.
     */
    public function number(float $number = null): static
    {
        return $this->state(fn (array $attributes) => [
            'answer_text' => null,
            'answer_number' => $number ?? fake()->randomFloat(2, 1, 100),
            'answer_boolean' => null,
            'answer_date' => null,
            'answer_json' => null,
            'file_path' => null,
        ]);
    }

    /**
     * Set answer as boolean.
     */
    public function boolean(bool $value = null): static
    {
        return $this->state(fn (array $attributes) => [
            'answer_text' => null,
            'answer_number' => null,
            'answer_boolean' => $value ?? fake()->boolean(),
            'answer_date' => null,
            'answer_json' => null,
            'file_path' => null,
        ]);
    }
}
