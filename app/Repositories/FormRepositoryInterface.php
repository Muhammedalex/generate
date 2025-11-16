<?php

namespace App\Repositories;

use App\Models\Form;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface FormRepositoryInterface
{
    /**
     * Get all forms for a user.
     */
    public function getAllForUser(int $userId, array $filters = []): LengthAwarePaginator;

    /**
     * Get a form by ID.
     */
    public function findById(int $id): ?Form;

    /**
     * Get a form by slug.
     */
    public function findBySlug(string $slug): ?Form;

    /**
     * Create a new form.
     */
    public function create(array $data): Form;

    /**
     * Update a form.
     */
    public function update(Form $form, array $data): Form;

    /**
     * Delete a form.
     */
    public function delete(Form $form): bool;

    /**
     * Duplicate a form.
     */
    public function duplicate(Form $form): Form;

    /**
     * Get published forms.
     */
    public function getPublished(array $filters = []): Collection;
}

