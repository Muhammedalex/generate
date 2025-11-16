<?php

namespace Database\Factories;

use App\Models\Form;
use App\Models\FormSection;
use App\Models\FormQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormQuestion>
 */
class FormQuestionFactory extends Factory
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
            'section_id' => null,
            'type' => FormQuestion::TYPE_SHORT_TEXT,
            'order' => 1,
            'is_required' => fake()->boolean(50),
            'settings' => null,
            'conditional_logic' => null,
        ];
    }

    /**
     * Indicate that the question is required.
     */
    public function required(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_required' => true,
        ]);
    }

    /**
     * Indicate that the question is optional.
     */
    public function optional(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_required' => false,
        ]);
    }

    /**
     * Set question type.
     */
    public function type(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
        ]);
    }
}
