<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\Company;
use App\Models\User;
use App\Models\Form;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(): View
    {
        $user = auth()->user();
        
        // Get statistics
        $stats = [
            'total_companies' => Company::where('user_id', $user->id)->count(),
            'active_companies' => Company::where('user_id', $user->id)->where('is_active', true)->count(),
            'total_users' => $user->role === 'admin' ? User::count() : 0,
            'total_forms' => Form::where('user_id', $user->id)->count(),
            'published_forms' => Form::where('user_id', $user->id)->where('status', 'published')->count(),
            'recent_companies' => Company::where('user_id', $user->id)
                ->with('translations')
                ->latest()
                ->take(5)
                ->get(),
            'recent_forms' => Form::where('user_id', $user->id)
                ->with('translations')
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('dashboard', compact('stats'));
    }
}
