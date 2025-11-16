<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\Company;
use App\Models\User;
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
            'recent_companies' => Company::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('dashboard', compact('stats'));
    }
}
