<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormBuilderRequest;
use App\Models\Form;
use App\Services\FormService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormController extends Controller
{
    protected FormService $formService;

    public function __construct(FormService $formService)
    {
        $this->formService = $formService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = [
            'status' => $request->get('status'),
            'search' => $request->get('search'),
            'per_page' => $request->get('per_page', 15),
        ];

        $forms = $this->formService->getAllForUser($filters);

        return view('forms.index', compact('forms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('forms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FormBuilderRequest $request)
    {
        $data = $request->validated();
        
        // Validate English translation is required
        if (empty($data['title_translations']['en'])) {
            return back()->withErrors(['title_translations.en' => 'English translation for title is required.'])->withInput();
        }

        $form = $this->formService->create($data);

        return redirect()->route('forms.builder', $form)
            ->with('success', __('forms.created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Form $form)
    {
        // Check if form is accessible
        // Owners can always view their forms, even if not active
        $isOwner = Auth::check() && $form->user_id === Auth::id();
        
        if (!$form->isActive() && !$isOwner) {
            abort(404);
        }

        // Check if authentication is required
        if ($form->require_auth && !Auth::check()) {
            // Store intended URL for redirect after login
            session()->put('url.intended', route('forms.show', $form));
            return redirect()->route('login')->with('error', __('forms.login_required'));
        }

        // Load form data with proper ordering
        $form->load([
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
            'questions.options.translations',
            'translations'
        ]);

        return view('forms.show', compact('form', 'isOwner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Form $form)
    {
        // Check authorization
        if ($form->user_id !== Auth::id()) {
            abort(403);
        }

        $form->load('translations');

        return view('forms.edit', compact('form'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FormBuilderRequest $request, Form $form)
    {
        // Check authorization
        if ($form->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validated();
        
        // Validate English translation is required if title is being updated
        if (isset($data['title_translations']) && empty($data['title_translations']['en'])) {
            return back()->withErrors(['title_translations.en' => 'English translation for title is required.'])->withInput();
        }

        $this->formService->update($form, $data);

        return redirect()->route('forms.index')
            ->with('success', __('forms.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Form $form)
    {
        // Check authorization
        if ($form->user_id !== Auth::id()) {
            abort(403);
        }

        $this->formService->delete($form);

        return redirect()->route('forms.index')
            ->with('success', __('forms.deleted_successfully'));
    }

    /**
     * Duplicate a form.
     */
    public function duplicate(Form $form)
    {
        // Check authorization
        if ($form->user_id !== Auth::id()) {
            abort(403);
        }

        $newForm = $this->formService->duplicate($form);

        return redirect()->route('forms.builder', $newForm)
            ->with('success', __('forms.duplicated_successfully'));
    }

    /**
     * Publish a form.
     */
    public function publish(Form $form)
    {
        // Check authorization
        if ($form->user_id !== Auth::id()) {
            abort(403);
        }

        $this->formService->publish($form);

        return redirect()->back()
            ->with('success', __('forms.published_successfully'));
    }

    /**
     * Unpublish a form.
     */
    public function unpublish(Form $form)
    {
        // Check authorization
        if ($form->user_id !== Auth::id()) {
            abort(403);
        }

        $this->formService->unpublish($form);

        return redirect()->back()
            ->with('success', __('forms.unpublished_successfully'));
    }
}
