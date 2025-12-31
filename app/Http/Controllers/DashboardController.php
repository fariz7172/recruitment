<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use App\Models\ScreeningResult;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_jobs' => Job::count(),
            'active_jobs' => Job::active()->count(),
            'total_applications' => Application::count(),
            'pending_applications' => Application::pending()->count(),
            'screened_today' => ScreeningResult::whereDate('created_at', today())->count(),
            'shortlisted' => Application::status('shortlisted')->count(),
        ];

        $recentApplications = Application::with(['job', 'screeningResult'])
            ->latest()
            ->take(10)
            ->get();

        $applicationsByStatus = Application::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $weeklyApplications = Application::selectRaw('DATE(created_at) as date, count(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return view('dashboard.index', compact(
            'stats',
            'recentApplications',
            'applicationsByStatus',
            'weeklyApplications'
        ));
    }
}
