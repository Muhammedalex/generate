<?php

namespace App\Repositories;

use App\Models\Form;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FormRepository implements FormRepositoryInterface
{
    /**
     * Get all forms for a user.
     */
    public function getAllForUser(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = Form::where('user_id', $userId)
            ->with(['sections.translations', 'questions.translations', 'responses', 'translations']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('field_name', 'title')
                  ->where('value', 'like', '%' . $search . '%');
            })->orWhereHas('translations', function ($q) use ($search) {
                $q->where('field_name', 'description')
                  ->where('value', 'like', '%' . $search . '%');
            });
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get a form by ID.
     */
    public function findById(int $id): ?Form
    {
        return Form::with([
            'sections.translations',
            'sections.questions.translations',
            'sections.questions.options.translations',
            'questions.translations',
            'questions.options.translations',
            'translations'
        ])->find($id);
    }

    /**
     * Get a form by slug.
     */
    public function findBySlug(string $slug): ?Form
    {
        return Form::with([
            'sections.translations',
            'sections.questions.translations',
            'sections.questions.options.translations',
            'questions.translations',
            'questions.options.translations',
            'translations'
        ])
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Create a new form.
     */
    public function create(array $data): Form
    {
        return Form::create($data);
    }

    /**
     * Update a form.
     */
    public function update(Form $form, array $data): Form
    {
        $form->update($data);
        return $form->fresh();
    }

    /**
     * Delete a form.
     */
    public function delete(Form $form): bool
    {
        return $form->delete();
    }

    /**
     * Duplicate a form.
     */
    public function duplicate(Form $form): Form
    {
        return DB::transaction(function () use ($form) {
            // Get English title to generate new slug
            $titleEn = $form->getEnglishTranslation('title');
            $newTitleEn = $titleEn . ' (Copy)';
            $newSlug = \Illuminate\Support\Str::slug($newTitleEn);
            
            // Ensure slug is unique
            $originalSlug = $newSlug;
            $counter = 1;
            while (Form::where('slug', $newSlug)->exists()) {
                $newSlug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Duplicate form
            $newForm = $form->replicate();
            $newForm->status = 'draft';
            $newForm->slug = $newSlug;
            $newForm->save();

            // Duplicate translations
            foreach ($form->translations as $translation) {
                $newTranslation = $translation->replicate();
                $newTranslation->translatable_id = $newForm->id;
                
                // Add (Copy) to English title
                if ($translation->field_name === 'title' && $translation->locale === 'en') {
                    $newTranslation->value = $newTitleEn;
                }
                
                $newTranslation->save();
            }

            // Duplicate sections
            foreach ($form->sections as $section) {
                $newSection = $section->replicate();
                $newSection->form_id = $newForm->id;
                $newSection->save();

                // Duplicate section translations
                foreach ($section->translations as $translation) {
                    $newTranslation = $translation->replicate();
                    $newTranslation->translatable_id = $newSection->id;
                    $newTranslation->save();
                }

                // Duplicate questions in section
                foreach ($section->questions as $question) {
                    $newQuestion = $question->replicate();
                    $newQuestion->form_id = $newForm->id;
                    $newQuestion->section_id = $newSection->id;
                    $newQuestion->save();

                    // Duplicate question translations
                    foreach ($question->translations as $translation) {
                        $newTranslation = $translation->replicate();
                        $newTranslation->translatable_id = $newQuestion->id;
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
                }
            }

            // Duplicate questions without sections
            foreach ($form->questions()->whereNull('section_id')->get() as $question) {
                $newQuestion = $question->replicate();
                $newQuestion->form_id = $newForm->id;
                $newQuestion->save();

                // Duplicate question translations
                foreach ($question->translations as $translation) {
                    $newTranslation = $translation->replicate();
                    $newTranslation->translatable_id = $newQuestion->id;
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
            }

            return $newForm->load([
                'sections.translations',
                'sections.questions.translations',
                'sections.questions.options.translations',
                'questions.translations',
                'questions.options.translations',
                'translations'
            ]);
        });
    }

    /**
     * Get published forms.
     */
    public function getPublished(array $filters = []): Collection
    {
        $query = Form::where('status', 'published')
            ->with([
                'sections.translations',
                'sections.questions.translations',
                'sections.questions.options.translations',
                'questions.translations',
                'questions.options.translations',
                'translations'
            ]);

        if (isset($filters['slug'])) {
            $query->where('slug', $filters['slug']);
        }

        return $query->get();
    }
}

