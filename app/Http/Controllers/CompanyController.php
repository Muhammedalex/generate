<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Requests\CompanyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::where('user_id', Auth::id())
            ->with('translations')
            ->latest()
            ->paginate(12);

        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request)
    {
        $data = $request->validated();
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $data['logo_path'] = $logoPath;
        }

        // Extract translations from data
        $translations = [];
        $translatableFields = ['name', 'description'];
        
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

        // Validate English translation is required
        if (empty($translations['name']['en'])) {
            return back()->withErrors(['name_translations.en' => 'English translation for name is required.'])->withInput();
        }

        // Set user_id
        $data['user_id'] = Auth::id();
        
        // Generate slug from English name
        $data['slug'] = \Illuminate\Support\Str::slug($translations['name']['en']);
        
        // Ensure slug is unique
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Company::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Create company
        $company = Company::create($data);

        // Save translations
        foreach ($translations as $field => $values) {
            $company->setTranslations($field, $values);
        }

        return redirect()->route('company.show', $company->slug)
            ->with('success', __('companies.created_successfully'));
    }

    /**
     * Display the specified resource (Public Profile).
     */
    public function show(Company $company)
    {
        // For public profile, we don't need authentication
        // But we can check if it's active (unless it's the owner viewing)
        if (!$company->is_active && (!Auth::check() || $company->user_id !== Auth::id())) {
            abort(404);
        }

        $company->load('translations');

        return view('companies.show', compact('company'));
    }

    /**
     * Display company details for authenticated users (with full details).
     */
    public function details(Company $company)
    {
        // Check authorization
        if ($company->user_id !== Auth::id()) {
            abort(403);
        }

        $company->load('translations');

        return view('companies.details', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        // Check authorization
        if ($company->user_id !== Auth::id()) {
            abort(403);
        }

        $company->load('translations');

        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $request, Company $company)
    {
        // Check authorization
        if ($company->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            
            $logoPath = $request->file('logo')->store('logos', 'public');
            $data['logo_path'] = $logoPath;
        }

        // Extract translations from data
        $translations = [];
        $translatableFields = ['name', 'description'];
        
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

        // Validate English translation is required if name is being updated
        if (isset($translations['name']) && empty($translations['name']['en'])) {
            return back()->withErrors(['name_translations.en' => 'English translation for name is required.'])->withInput();
        }

        // Update slug if name is being updated
        if (isset($translations['name']['en'])) {
            $newSlug = \Illuminate\Support\Str::slug($translations['name']['en']);
            if ($newSlug !== $company->slug) {
                // Ensure slug is unique
                $originalSlug = $newSlug;
                $counter = 1;
                while (Company::where('slug', $newSlug)->where('id', '!=', $company->id)->exists()) {
                    $newSlug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                $data['slug'] = $newSlug;
            }
        }

        // Update company
        $company->update($data);

        // Save translations
        foreach ($translations as $field => $values) {
            $company->setTranslations($field, $values);
        }

        return redirect()->route('company.show', $company->slug)
            ->with('success', __('companies.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        // Check authorization
        if ($company->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete logo if exists
        if ($company->logo_path) {
            Storage::disk('public')->delete($company->logo_path);
        }

        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', __('companies.deleted_successfully'));
    }
}
