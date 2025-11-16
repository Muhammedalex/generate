<?php

namespace App\Repositories;

use App\Models\Form;
use App\Models\FormResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface FormResponseRepositoryInterface
{
    /**
     * Get all responses for a form.
     */
    public function getForForm(Form $form, array $filters = []): LengthAwarePaginator;

    /**
     * Get a response by ID.
     */
    public function findById(int $id): ?FormResponse;

    /**
     * Create a new response.
     */
    public function create(array $data): FormResponse;

    /**
     * Update a response.
     */
    public function update(FormResponse $response, array $data): FormResponse;

    /**
     * Delete a response.
     */
    public function delete(FormResponse $response): bool;

    /**
     * Get response statistics for a form.
     */
    public function getStatistics(Form $form): array;
}

