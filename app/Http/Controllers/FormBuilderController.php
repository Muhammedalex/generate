<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormQuestionRequest;
use App\Models\Form;
use App\Models\FormSection;
use App\Models\FormQuestion;
use App\Services\FormBuilderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormBuilderController extends Controller
{
    protected FormBuilderService $builderService;

    public function __construct(FormBuilderService $builderService)
    {
        $this->builderService = $builderService;
    }

    /**
     * Show the form builder.
     */
    public function builder(Form $form)
    {
        // Check authorization
        if ($form->user_id !== Auth::id()) {
            abort(403);
        }

        $form->load([
            'translations',
            'sections' => function($query) {
                $query->orderBy('order');
            },
            'sections.translations',
            'sections.questions' => function($query) {
                $query->orderBy('order');
            },
            'sections.questions.translations',
            'sections.questions.options' => function($query) {
                $query->orderBy('order');
            },
            'sections.questions.options.translations',
            'questions' => function($query) {
                $query->whereNull('section_id')->orderBy('order');
            },
            'questions.translations',
            'questions.options' => function($query) {
                $query->orderBy('order');
            },
            'questions.options.translations'
        ]);

        // If request wants JSON, return form data
        if (request()->wantsJson()) {
            return response()->json($form);
        }

        return view('forms.builder', compact('form'));
    }

    /**
     * Add a section to the form.
     */
    public function addSection(Request $request, Form $form)
    {
        // Check authorization
        if ($form->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title_translations' => ['nullable', 'array'],
            'title_translations.en' => ['nullable', 'string', 'max:255'],
            'title_translations.ar' => ['nullable', 'string', 'max:255'],
            'description_translations' => ['nullable', 'array'],
            'description_translations.en' => ['nullable', 'string'],
            'description_translations.ar' => ['nullable', 'string'],
        ]);

        $section = $this->builderService->addSection($form, $request->all());

        return response()->json($section->load('translations'));
    }

    /**
     * Update a section.
     */
    public function updateSection(Request $request, Form $form, FormSection $section)
    {
        // Check authorization
        if ($form->user_id !== Auth::id() || $section->form_id !== $form->id) {
            abort(403);
        }

        $request->validate([
            'title_translations' => ['nullable', 'array'],
            'title_translations.en' => ['nullable', 'string', 'max:255'],
            'title_translations.ar' => ['nullable', 'string', 'max:255'],
            'description_translations' => ['nullable', 'array'],
            'description_translations.en' => ['nullable', 'string'],
            'description_translations.ar' => ['nullable', 'string'],
        ]);

        $section = $this->builderService->updateSection($section, $request->all());

        return response()->json($section->load('translations'));
    }

    /**
     * Delete a section.
     */
    public function deleteSection(Form $form, FormSection $section)
    {
        // Check authorization
        if ($form->user_id !== Auth::id() || $section->form_id !== $form->id) {
            abort(403);
        }

        $this->builderService->deleteSection($section);

        return response()->json(['success' => true]);
    }

    /**
     * Reorder sections.
     */
    public function reorderSections(Request $request, Form $form)
    {
        // Check authorization
        if ($form->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'sections' => ['required', 'array'],
            'sections.*' => ['required', 'exists:form_sections,id'],
        ]);

        $this->builderService->reorderSections($form, $request->sections);

        return response()->json(['success' => true]);
    }

    /**
     * Add a question to the form.
     */
    public function addQuestion(FormQuestionRequest $request, Form $form)
    {
        // Check authorization
        if ($form->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validated();
        $data['form_id'] = $form->id;

        $question = $this->builderService->addQuestion($form, $data);

        return response()->json($question->load(['options.translations', 'translations']));
    }

    /**
     * Update a question.
     */
    public function updateQuestion(FormQuestionRequest $request, Form $form, FormQuestion $question)
    {
        // Check authorization
        if ($form->user_id !== Auth::id() || $question->form_id !== $form->id) {
            abort(403);
        }

        $question = $this->builderService->updateQuestion($question, $request->validated());

        return response()->json($question->load(['options.translations', 'translations']));
    }

    /**
     * Delete a question.
     */
    public function deleteQuestion(Form $form, FormQuestion $question)
    {
        // Check authorization
        if ($form->user_id !== Auth::id() || $question->form_id !== $form->id) {
            abort(403);
        }

        $this->builderService->deleteQuestion($question);

        return response()->json(['success' => true]);
    }

    /**
     * Reorder questions.
     */
    public function reorderQuestions(Request $request, Form $form)
    {
        // Check authorization
        if ($form->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'questions' => ['required', 'array'],
            'questions.*' => ['required', 'exists:form_questions,id'],
            'section_id' => ['nullable', 'exists:form_sections,id'],
        ]);

        $this->builderService->reorderQuestions(
            $form,
            $request->questions,
            $request->section_id
        );

        return response()->json(['success' => true]);
    }

    /**
     * Duplicate a question.
     */
    public function duplicateQuestion(Form $form, FormQuestion $question)
    {
        // Check authorization
        if ($form->user_id !== Auth::id() || $question->form_id !== $form->id) {
            abort(403);
        }

        $newQuestion = $this->builderService->duplicateQuestion($question);

        return response()->json($newQuestion->load(['options.translations', 'translations']));
    }
}
