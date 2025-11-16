<?php

namespace App\Repositories;

use App\Models\Form;
use App\Models\FormResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FormResponseRepository implements FormResponseRepositoryInterface
{
    /**
     * Get all responses for a form.
     */
    public function getForForm(Form $form, array $filters = []): LengthAwarePaginator
    {
        $query = FormResponse::where('form_id', $form->id)
            ->with([
                'user',
                'answers.question.translations',
                'answers.question.options.translations',
                'form.translations'
            ]);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('submitted_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('submitted_at', '<=', $filters['date_to']);
        }

        return $query->latest('submitted_at')->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get a response by ID.
     */
    public function findById(int $id): ?FormResponse
    {
        return FormResponse::with([
            'user',
            'answers.question.translations',
            'answers.question.options.translations',
            'form.translations'
        ])->find($id);
    }

    /**
     * Create a new response.
     */
    public function create(array $data): FormResponse
    {
        return FormResponse::create($data);
    }

    /**
     * Update a response.
     */
    public function update(FormResponse $response, array $data): FormResponse
    {
        $response->update($data);
        return $response->fresh();
    }

    /**
     * Delete a response.
     */
    public function delete(FormResponse $response): bool
    {
        return $response->delete();
    }

    /**
     * Get response statistics for a form.
     */
    public function getStatistics(Form $form): array
    {
        $total = FormResponse::where('form_id', $form->id)->count();
        $completed = FormResponse::where('form_id', $form->id)
            ->where('status', FormResponse::STATUS_COMPLETED)
            ->count();
        $partial = FormResponse::where('form_id', $form->id)
            ->where('status', FormResponse::STATUS_PARTIAL)
            ->count();
        $abandoned = FormResponse::where('form_id', $form->id)
            ->where('status', FormResponse::STATUS_ABANDONED)
            ->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'partial' => $partial,
            'abandoned' => $abandoned,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
        ];
    }
}

