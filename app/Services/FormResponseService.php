<?php

namespace App\Services;

use App\Models\Form;
use App\Models\FormResponse;
use App\Models\ResponseAnswer;
use App\Repositories\FormResponseRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\FormQuestion;

class FormResponseService
{
    protected FormResponseRepositoryInterface $repository;

    public function __construct(FormResponseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Submit a form response.
     */
    public function submitResponse(Form $form, array $data, Request $request): FormResponse
    {
        return DB::transaction(function () use ($form, $data, $request) {
            // Check if form allows multiple submissions
            if (!$form->allow_multiple && Auth::check()) {
                $existingResponse = FormResponse::where('form_id', $form->id)
                    ->where('user_id', Auth::id())
                    ->where('status', FormResponse::STATUS_COMPLETED)
                    ->first();

                if ($existingResponse) {
                    throw new \Exception('You have already submitted this form.');
                }
            }

            // Create response
            $responseData = [
                'form_id' => $form->id,
                'user_id' => Auth::check() ? Auth::id() : null,
                'email' => $data['email'] ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => FormResponse::STATUS_COMPLETED,
                'submitted_at' => now(),
            ];

            $response = $this->repository->create($responseData);

            // Save answers
            $answers = $data['answers'] ?? [];
            foreach ($answers as $questionId => $answerData) {
                $this->saveAnswer($response, (int) $questionId, $answerData);
            }

            return $response->load('answers.question');
        });
    }

    /**
     * Save an answer to a question.
     */
    protected function saveAnswer(FormResponse $response, int $questionId, $answerData): ResponseAnswer
    {
        $question = $response->form->questions()->find($questionId);
        if (!$question) {
            throw new \Exception("Question {$questionId} not found.");
        }

        $answer = new ResponseAnswer();
        $answer->response_id = $response->id;
        $answer->question_id = $questionId;

        // Handle different question types
        switch ($question->type) {
            case FormQuestion::TYPE_SHORT_TEXT:
            case FormQuestion::TYPE_LONG_TEXT:
            case FormQuestion::TYPE_EMAIL:
            case FormQuestion::TYPE_URL:
            case FormQuestion::TYPE_PHONE:
                $answer->answer_text = $answerData;
                break;

            case FormQuestion::TYPE_NUMBER:
            case FormQuestion::TYPE_LINEAR_SCALE:
                $answer->answer_number = (float) $answerData;
                break;

            case FormQuestion::TYPE_YES_NO:
                $answer->answer_boolean = (bool) $answerData;
                break;

            case FormQuestion::TYPE_DATE:
            case FormQuestion::TYPE_TIME:
            case FormQuestion::TYPE_DATETIME:
                $answer->answer_date = $answerData;
                break;

            case FormQuestion::TYPE_MULTIPLE_CHOICE:
            case FormQuestion::TYPE_CHECKBOX:
            case FormQuestion::TYPE_DROPDOWN:
                if (is_array($answerData)) {
                    $answer->answer_json = $answerData;
                } else {
                    $answer->answer_text = $answerData;
                }
                break;

            case FormQuestion::TYPE_FILE_UPLOAD:
                if ($answerData instanceof \Illuminate\Http\UploadedFile) {
                    $path = $answerData->store('form-uploads', 'public');
                    $answer->file_path = $path;
                } elseif (is_string($answerData)) {
                    $answer->file_path = $answerData;
                }
                break;

            default:
                $answer->answer_text = is_array($answerData) ? json_encode($answerData) : $answerData;
        }

        $answer->save();
        return $answer;
    }

    /**
     * Get all responses for a form.
     */
    public function getResponsesForForm(Form $form, array $filters = [])
    {
        return $this->repository->getForForm($form, $filters);
    }

    /**
     * Get a response by ID.
     */
    public function findResponseById(int $id): ?FormResponse
    {
        return $this->repository->findById($id);
    }

    /**
     * Delete a response.
     */
    public function deleteResponse(FormResponse $response): bool
    {
        // Delete uploaded files
        foreach ($response->answers as $answer) {
            if ($answer->file_path) {
                Storage::disk('public')->delete($answer->file_path);
            }
        }

        return $this->repository->delete($response);
    }

    /**
     * Get response statistics.
     */
    public function getStatistics(Form $form): array
    {
        return $this->repository->getStatistics($form);
    }

    /**
     * Export responses to CSV.
     */
    public function exportToCsv(Form $form): string
    {
        $responses = $this->repository->getForForm($form, ['per_page' => 10000]);
        $questions = $form->questions()->with('translations', 'options.translations')->orderBy('order')->get();

        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        $filename = $tempDir . '/form-' . $form->id . '-' . time() . '.csv';
        $file = fopen($filename, 'w');

        // Write headers
        $headers = ['ID', 'Submitted At', 'Email', 'User'];
        foreach ($questions as $question) {
            $headers[] = $question->question_text;
        }
        fputcsv($file, $headers);

        // Write data
        foreach ($responses as $response) {
            $row = [
                $response->id,
                $response->submitted_at?->format('Y-m-d H:i:s'),
                $response->email,
                $response->user?->name ?? 'Guest',
            ];

            foreach ($questions as $question) {
                $answer = $response->getAnswerForQuestion($question->id);
                $row[] = $answer ? $this->formatAnswerForExport($answer) : '';
            }

            fputcsv($file, $row);
        }

        fclose($file);
        return $filename;
    }

    /**
     * Format answer for CSV export.
     */
    protected function formatAnswerForExport(ResponseAnswer $answer): string
    {
        if ($answer->answer_text) {
            return $answer->answer_text;
        }

        if ($answer->answer_number !== null) {
            return (string) $answer->answer_number;
        }

        if ($answer->answer_boolean !== null) {
            return $answer->answer_boolean ? 'Yes' : 'No';
        }

        if ($answer->answer_date) {
            return $answer->answer_date->format('Y-m-d');
        }

        if ($answer->answer_json) {
            return is_array($answer->answer_json) 
                ? implode(', ', $answer->answer_json) 
                : json_encode($answer->answer_json);
        }

        if ($answer->file_path) {
            return Storage::url($answer->file_path);
        }

        return '';
    }
}

