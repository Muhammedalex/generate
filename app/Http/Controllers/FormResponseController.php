<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormResponseRequest;
use App\Models\Form;
use App\Models\FormResponse;
use App\Services\FormResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FormResponseController extends Controller
{
    protected FormResponseService $responseService;

    public function __construct(FormResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    /**
     * Submit a form response.
     */
    public function submit(FormResponseRequest $request, Form $form)
    {
        // Check if form is active
        if (!$form->isActive()) {
            return redirect()->back()
                ->with('error', __('forms.form_not_available'));
        }

        // Check if authentication is required
        if ($form->require_auth && !Auth::check()) {
            return redirect()->route('login')
                ->with('error', __('forms.login_required'));
        }

        try {
            $response = $this->responseService->submitResponse($form, $request->validated(), $request);

            // Redirect based on form settings
            if ($form->redirect_url) {
                return redirect($form->redirect_url);
            }

            return redirect()->route('forms.show', $form)
                ->with('success', $form->getTranslatedThankYouMessage() ?: __('forms.thank_you'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['form' => $e->getMessage()]);
        }
    }

    /**
     * View responses for a form (admin only).
     */
    public function index(Request $request, Form $form)
    {
        // Check authorization
        if ($form->user_id !== Auth::id()) {
            abort(403);
        }

        $filters = [
            'status' => $request->get('status'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'per_page' => $request->get('per_page', 15),
        ];

        $form->load('translations');
        
        $responses = $this->responseService->getResponsesForForm($form, $filters);
        $statistics = $this->responseService->getStatistics($form);

        return view('forms.responses', compact('form', 'responses', 'statistics'));
    }

    /**
     * View a single response.
     */
    public function show(Form $form, FormResponse $response)
    {
        // Check authorization
        if ($form->user_id !== Auth::id() || $response->form_id !== $form->id) {
            abort(403);
        }

        $response->load([
            'answers.question.translations',
            'answers.question.options.translations',
            'user',
            'form.translations'
        ]);

        return view('forms.response-detail', compact('form', 'response'));
    }

    /**
     * Delete a response.
     */
    public function destroy(Form $form, FormResponse $response)
    {
        // Check authorization
        if ($form->user_id !== Auth::id() || $response->form_id !== $form->id) {
            abort(403);
        }

        $this->responseService->deleteResponse($response);

        return redirect()->route('forms.responses', $form)
            ->with('success', __('forms.response_deleted'));
    }

    /**
     * Export responses to CSV.
     */
    public function export(Form $form)
    {
        // Check authorization
        if ($form->user_id !== Auth::id()) {
            abort(403);
        }

        $filename = $this->responseService->exportToCsv($form);

        return response()->download($filename, 'form-responses-' . $form->slug . '-' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ])->deleteFileAfterSend(true);
    }
}
