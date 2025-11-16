<?php

namespace App\Services;

use App\Models\Form;
use App\Repositories\FormRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FormService
{
    protected FormRepositoryInterface $repository;

    public function __construct(FormRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all forms for the authenticated user.
     */
    public function getAllForUser(array $filters = [])
    {
        return $this->repository->getAllForUser(Auth::id(), $filters);
    }

    /**
     * Get a form by ID.
     */
    public function findById(int $id): ?Form
    {
        return $this->repository->findById($id);
    }

    /**
     * Get a form by slug.
     */
    public function findBySlug(string $slug): ?Form
    {
        return $this->repository->findBySlug($slug);
    }

    /**
     * Create a new form.
     */
    public function create(array $data): Form
    {
        $data['user_id'] = Auth::id();

        // Extract translations from data
        $translations = [];
        $translatableFields = ['title', 'description', 'thank_you_message'];
        
        foreach ($translatableFields as $field) {
            if (isset($data[$field . '_translations']) && is_array($data[$field . '_translations'])) {
                $translations[$field] = $data[$field . '_translations'];
                unset($data[$field . '_translations']);
            } elseif (isset($data[$field])) {
                // If single value provided, treat as English
                $translations[$field] = ['en' => $data[$field]];
                unset($data[$field]);
            }
        }

        // Generate slug from English title
        if (isset($translations['title']['en'])) {
            $data['slug'] = Str::slug($translations['title']['en']);
            
            // Ensure slug is unique
            $originalSlug = $data['slug'];
            $counter = 1;
            while (\App\Models\Form::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Create form
        $form = $this->repository->create($data);

        // Save translations
        foreach ($translations as $field => $values) {
            $form->setTranslations($field, $values);
        }

        return $form->fresh();
    }

    /**
     * Update a form.
     */
    public function update(Form $form, array $data): Form
    {
        // Extract translations from data
        $translations = [];
        $translatableFields = ['title', 'description', 'thank_you_message'];
        
        foreach ($translatableFields as $field) {
            if (isset($data[$field . '_translations']) && is_array($data[$field . '_translations'])) {
                $translations[$field] = $data[$field . '_translations'];
                unset($data[$field . '_translations']);
            } elseif (isset($data[$field])) {
                // If single value provided, treat as English
                $translations[$field] = ['en' => $data[$field]];
                unset($data[$field]);
            }
        }

        // Update slug if title is being updated
        if (isset($translations['title']['en'])) {
            $newSlug = Str::slug($translations['title']['en']);
            if ($newSlug !== $form->slug) {
                // Ensure slug is unique
                $originalSlug = $newSlug;
                $counter = 1;
                while (\App\Models\Form::where('slug', $newSlug)->where('id', '!=', $form->id)->exists()) {
                    $newSlug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                $data['slug'] = $newSlug;
            }
        }

        // Update form
        $form = $this->repository->update($form, $data);

        // Save translations
        foreach ($translations as $field => $values) {
            $form->setTranslations($field, $values);
        }

        return $form->fresh();
    }

    /**
     * Delete a form.
     */
    public function delete(Form $form): bool
    {
        return $this->repository->delete($form);
    }

    /**
     * Duplicate a form.
     */
    public function duplicate(Form $form): Form
    {
        return $this->repository->duplicate($form);
    }

    /**
     * Publish a form.
     */
    public function publish(Form $form): Form
    {
        return $this->repository->update($form, ['status' => 'published']);
    }

    /**
     * Unpublish a form.
     */
    public function unpublish(Form $form): Form
    {
        return $this->repository->update($form, ['status' => 'draft']);
    }

    /**
     * Close a form.
     */
    public function close(Form $form): Form
    {
        return $this->repository->update($form, ['status' => 'closed']);
    }
}

