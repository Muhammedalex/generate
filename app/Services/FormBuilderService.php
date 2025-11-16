<?php

namespace App\Services;

use App\Models\Form;
use App\Models\FormSection;
use App\Models\FormQuestion;
use App\Models\QuestionOption;
use Illuminate\Support\Facades\DB;

class FormBuilderService
{
    /**
     * Add a section to a form.
     */
    public function addSection(Form $form, array $data): FormSection
    {
        $data['form_id'] = $form->id;

        // Set order if not provided
        if (!isset($data['order'])) {
            $maxOrder = FormSection::where('form_id', $form->id)->max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
        }

        // Extract translations
        $translations = [];
        $translatableFields = ['title', 'description'];
        
        foreach ($translatableFields as $field) {
            if (isset($data[$field . '_translations']) && is_array($data[$field . '_translations'])) {
                $translations[$field] = $data[$field . '_translations'];
                unset($data[$field . '_translations']);
            } elseif (isset($data[$field])) {
                $translations[$field] = ['en' => $data[$field]];
                unset($data[$field]);
            }
        }

        $section = FormSection::create($data);

        // Save translations
        foreach ($translations as $field => $values) {
            $section->setTranslations($field, $values);
        }

        return $section->fresh();
    }

    /**
     * Update a section.
     */
    public function updateSection(FormSection $section, array $data): FormSection
    {
        // Extract translations
        $translations = [];
        $translatableFields = ['title', 'description'];
        
        foreach ($translatableFields as $field) {
            if (isset($data[$field . '_translations']) && is_array($data[$field . '_translations'])) {
                $translations[$field] = $data[$field . '_translations'];
                unset($data[$field . '_translations']);
            } elseif (isset($data[$field])) {
                $translations[$field] = ['en' => $data[$field]];
                unset($data[$field]);
            }
        }

        $section->update($data);

        // Save translations
        foreach ($translations as $field => $values) {
            $section->setTranslations($field, $values);
        }

        return $section->fresh();
    }

    /**
     * Delete a section.
     */
    public function deleteSection(FormSection $section): bool
    {
        // Move questions to form root (no section)
        FormQuestion::where('section_id', $section->id)
            ->update(['section_id' => null]);

        return $section->delete();
    }

    /**
     * Reorder sections.
     */
    public function reorderSections(Form $form, array $sectionIds): void
    {
        DB::transaction(function () use ($form, $sectionIds) {
            foreach ($sectionIds as $order => $sectionId) {
                FormSection::where('id', $sectionId)
                    ->where('form_id', $form->id)
                    ->update(['order' => $order + 1]);
            }
        });
    }

    /**
     * Add a question to a form.
     */
    public function addQuestion(Form $form, array $data): FormQuestion
    {
        $data['form_id'] = $form->id;

        // Set order if not provided
        if (!isset($data['order'])) {
            $maxOrder = FormQuestion::where('form_id', $form->id)
                ->where('section_id', $data['section_id'] ?? null)
                ->max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
        }

        // Extract translations
        $translations = [];
        $translatableFields = ['question_text', 'help_text'];
        
        foreach ($translatableFields as $field) {
            if (isset($data[$field . '_translations']) && is_array($data[$field . '_translations'])) {
                $translations[$field] = $data[$field . '_translations'];
                unset($data[$field . '_translations']);
            } elseif (isset($data[$field])) {
                $translations[$field] = ['en' => $data[$field]];
                unset($data[$field]);
            }
        }

        // Validate English translation is required for question_text
        if (empty($translations['question_text']['en'])) {
            throw new \Exception('English translation for question_text is required.');
        }

        $question = FormQuestion::create($data);

        // Save translations
        foreach ($translations as $field => $values) {
            $question->setTranslations($field, $values);
        }

        // Add options if provided
        if (isset($data['options']) && is_array($data['options'])) {
            $this->addOptionsToQuestion($question, $data['options']);
        }

        return $question->load('options');
    }

    /**
     * Update a question.
     */
    public function updateQuestion(FormQuestion $question, array $data): FormQuestion
    {
        // Extract translations
        $translations = [];
        $translatableFields = ['question_text', 'help_text'];
        
        foreach ($translatableFields as $field) {
            if (isset($data[$field . '_translations']) && is_array($data[$field . '_translations'])) {
                $translations[$field] = $data[$field . '_translations'];
                unset($data[$field . '_translations']);
            } elseif (isset($data[$field])) {
                $translations[$field] = ['en' => $data[$field]];
                unset($data[$field]);
            }
        }

        // Validate English translation is required if question_text is being updated
        if (isset($translations['question_text']) && empty($translations['question_text']['en'])) {
            throw new \Exception('English translation for question_text is required.');
        }

        $question->update($data);

        // Save translations
        foreach ($translations as $field => $values) {
            $question->setTranslations($field, $values);
        }

        // Update options if provided
        if (isset($data['options']) && is_array($data['options'])) {
            $this->updateQuestionOptions($question, $data['options']);
        }

        return $question->fresh(['options']);
    }

    /**
     * Delete a question.
     */
    public function deleteQuestion(FormQuestion $question): bool
    {
        return $question->delete();
    }

    /**
     * Reorder questions.
     */
    public function reorderQuestions(Form $form, array $questionIds, ?int $sectionId = null): void
    {
        DB::transaction(function () use ($form, $questionIds, $sectionId) {
            foreach ($questionIds as $order => $questionId) {
                FormQuestion::where('id', $questionId)
                    ->where('form_id', $form->id)
                    ->where('section_id', $sectionId)
                    ->update(['order' => $order + 1]);
            }
        });
    }

    /**
     * Add options to a question.
     */
    public function addOptionsToQuestion(FormQuestion $question, array $options): void
    {
        DB::transaction(function () use ($question, $options) {
            // Delete existing options
            $question->options()->delete();

            // Add new options
            foreach ($options as $index => $optionData) {
                if (is_string($optionData)) {
                    $optionData = ['option_text' => $optionData];
                }

                // Handle both 'text' and 'option_text' keys
                $optionText = $optionData['option_text'] ?? $optionData['text'] ?? (is_string($optionData) ? $optionData : '');

                $option = QuestionOption::create([
                    'question_id' => $question->id,
                    'order' => $optionData['order'] ?? $index + 1,
                ]);

                // Save translations
                if (isset($optionData['option_text_translations']) && is_array($optionData['option_text_translations'])) {
                    $option->setTranslations('option_text', $optionData['option_text_translations']);
                } elseif (!empty($optionText)) {
                    // If single value provided, treat as English
                    $option->setTranslations('option_text', ['en' => $optionText]);
                }
            }
        });
    }

    /**
     * Update question options.
     */
    public function updateQuestionOptions(FormQuestion $question, array $options): void
    {
        $this->addOptionsToQuestion($question, $options);
    }

    /**
     * Duplicate a question.
     */
    public function duplicateQuestion(FormQuestion $question): FormQuestion
    {
        $newQuestion = $question->replicate();
        $newQuestion->order = FormQuestion::where('form_id', $question->form_id)
            ->where('section_id', $question->section_id)
            ->max('order') + 1;
        $newQuestion->save();

        // Duplicate translations
        foreach ($question->translations as $translation) {
            $newTranslation = $translation->replicate();
            $newTranslation->translatable_id = $newQuestion->id;
            
            // Add (Copy) to English question_text
            if ($translation->field_name === 'question_text' && $translation->locale === 'en') {
                $newTranslation->value = $translation->value . ' (Copy)';
            }
            
            $newTranslation->save();
        }

        // Duplicate options
        foreach ($question->options as $option) {
            $newOption = $option->replicate();
            $newOption->question_id = $newQuestion->id;
            $newOption->save();

            // Duplicate option translations
            foreach ($option->translations as $translation) {
                $newTranslation = $translation->replicate();
                $newTranslation->translatable_id = $newOption->id;
                $newTranslation->save();
            }
        }

        return $newQuestion->load('options');
    }
}

