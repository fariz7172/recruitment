<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::with('creator')->withCount('applications');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('department', 'like', '%' . $request->search . '%');
            });
        }

        $jobs = $query->latest()->paginate(10);

        return view('jobs.index', compact('jobs'));
    }

    public function create()
    {
        return view('jobs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'department' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'employment_type' => 'required|in:full-time,part-time,contract,internship',
            'min_education' => 'required|string|max:50',
            'min_experience_years' => 'required|integer|min:0',
            'required_skills' => 'required|array|min:1',
            'required_skills.*' => 'string|max:100',
            'preferred_skills' => 'nullable|array',
            'preferred_skills.*' => 'string|max:100',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'deadline' => 'nullable|date|after:today',
            'status' => 'required|in:active,closed,draft',
        ]);

        $validated['created_by'] = Auth::id() ?? 1;
        $validated['required_skills'] = array_filter($validated['required_skills']);
        $validated['preferred_skills'] = array_filter($validated['preferred_skills'] ?? []);

        $job = Job::create($validated);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Lowongan berhasil dibuat!');
    }

    public function show(Job $job)
    {
        $job->load(['applications.screeningResult', 'creator']);
        
        $applicationStats = [
            'total' => $job->applications->count(),
            'pending' => $job->applications->where('status', 'pending')->count(),
            'shortlisted' => $job->applications->where('status', 'shortlisted')->count(),
            'rejected' => $job->applications->where('status', 'rejected')->count(),
        ];

        return view('jobs.show', compact('job', 'applicationStats'));
    }

    public function edit(Job $job)
    {
        return view('jobs.edit', compact('job'));
    }

    public function update(Request $request, Job $job)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'department' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'employment_type' => 'required|in:full-time,part-time,contract,internship',
            'min_education' => 'required|string|max:50',
            'min_experience_years' => 'required|integer|min:0',
            'required_skills' => 'required|array|min:1',
            'required_skills.*' => 'string|max:100',
            'preferred_skills' => 'nullable|array',
            'preferred_skills.*' => 'string|max:100',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'deadline' => 'nullable|date',
            'status' => 'required|in:active,closed,draft',
        ]);

        $validated['required_skills'] = array_filter($validated['required_skills']);
        $validated['preferred_skills'] = array_filter($validated['preferred_skills'] ?? []);

        $job->update($validated);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Lowongan berhasil diperbarui!');
    }

    public function destroy(Job $job)
    {
        $job->delete();

        return redirect()->route('jobs.index')
            ->with('success', 'Lowongan berhasil dihapus!');
    }

    /**
     * Public listing of active jobs
     */
    public function publicIndex()
    {
        $jobs = Job::active()
            ->where(function ($query) {
                $query->whereNull('deadline')
                      ->orWhere('deadline', '>=', now());
            })
            ->latest()
            ->paginate(12);

        return view('jobs.public', compact('jobs'));
    }
}
