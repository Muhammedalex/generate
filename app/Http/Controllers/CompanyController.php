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

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Ensure slug is unique
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Company::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Set user_id
        $data['user_id'] = Auth::id();

        // Handle translations - Laravel will automatically cast to JSON
        // No need to manually encode since we have it in $casts

        $company = Company::create($data);

        return redirect()->route('companies.show', $company)
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

        return view('companies.show', compact('company'));
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

        // Generate slug if not provided and name changed
        if (empty($data['slug']) && $data['name'] !== $company->name) {
            $data['slug'] = Str::slug($data['name']);
            
            // Ensure slug is unique (excluding current company)
            $originalSlug = $data['slug'];
            $counter = 1;
            while (Company::where('slug', $data['slug'])->where('id', '!=', $company->id)->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Handle translations - Laravel will automatically cast to JSON
        // No need to manually encode since we have it in $casts

        $company->update($data);

        return redirect()->route('companies.show', $company)
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
