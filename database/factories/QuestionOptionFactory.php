<?php

namespace Database\Factories;

use App\Models\FormQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuestionOption>
 */
class QuestionOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question_id' => FormQuestion::factory(),
            'order' => 1,
        ];
    }
}
