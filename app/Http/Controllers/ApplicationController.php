<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use App\Models\ScreeningResult;
use App\Services\GroqScreeningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function __construct(
        private GroqScreeningService $screeningService
    ) {}

    public function index(Request $request)
    {
        $query = Application::with(['job', 'screeningResult']);

        // RESTRICTION: Candidates see ONLY their own application
        if (auth()->user()->role === 'candidate') {
            $query->where('id', auth()->user()->application_id);
        }

        if ($request->filled('job_id')) {
            $query->where('job_id', $request->job_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('applicant_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $applications = $query->latest()->paginate(15);
        $jobs = Job::active()->get();

        return view('applications.index', compact('applications', 'jobs'));
    }

    public function create(Request $request)
    {
        $job = null;
        if ($request->filled('job_id')) {
            $job = Job::findOrFail($request->job_id);
        }

        $jobs = Job::active()->get();

        return view('applications.create', compact('jobs', 'job'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'applicant_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'resume' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:2048',
            'cover_letter' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Handle resume upload
        $resumePath = $request->file('resume')->store('resumes', 'public');

        // Handle cover letter upload
        $coverLetterPath = null;
        if ($request->hasFile('cover_letter')) {
            $coverLetterPath = $request->file('cover_letter')->store('cover_letters', 'public');
        }

        $application = Application::create([
            'job_id' => $validated['job_id'],
            'applicant_name' => $validated['applicant_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'resume_path' => $resumePath,
            'cover_letter_path' => $coverLetterPath,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        // Check if we should auto-screen
        if ($request->has('auto_screen') && $this->screeningService->isConfigured()) {
            return $this->processScreening($application);
        }

        return redirect()->route('applications.show', $application)
            ->with('success', 'Lamaran berhasil disubmit!');
    }

    public function show(Application $application)
    {
        // Authorization check
        $user = auth()->user();
        if ($user && $user->role !== 'admin' && $user->application_id !== $application->id) {
             abort(403, 'Anda tidak memiliki akses untuk melihat lamaran ini.');
        }

        $application->load(['job', 'screeningResult']);

        return view('applications.show', compact('application'));
    }

    public function updateStatus(Request $request, Application $application)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,screening,reviewed,shortlisted,rejected,hired',
        ]);

        // Auto-delete resume if status is rejected
        if ($validated['status'] === 'rejected') {
            if ($application->resume_path) {
                Storage::disk('public')->delete($application->resume_path);
                $application->resume_path = null;
            }
            if ($application->cover_letter_path) {
                Storage::disk('public')->delete($application->cover_letter_path);
                $application->cover_letter_path = null;
            }
        }

        $application->update($validated);
        // Save changes for nulled paths if any
        if ($application->isDirty()) {
            $application->save();
        }

        return redirect()->back()
            ->with('success', 'Status lamaran berhasil diperbarui!');
    }

    public function destroy(Application $application)
    {
        // Delete uploaded files
        if ($application->resume_path) {
            Storage::disk('public')->delete($application->resume_path);
        }
        if ($application->cover_letter_path) {
            Storage::disk('public')->delete($application->cover_letter_path);
        }

        $application->delete();

        return redirect()->route('applications.index')
            ->with('success', 'Lamaran berhasil dihapus!');
    }

    /**
     * Trigger AI screening for an application.
     */
    public function screen(Application $application)
    {
        if (!$this->screeningService->isConfigured()) {
            return redirect()->back()
                ->with('error', 'Groq API Key belum dikonfigurasi. Tambahkan GROQ_API_KEY di file .env (Gratis dari console.groq.com)');
        }

        return $this->processScreening($application);
    }

    /**
     * Process AI screening.
     */
    private function processScreening(Application $application)
    {
        try {
            $application->update(['status' => 'screening']);

            $job = $application->job;
            $requirements = [
                'min_education' => $job->min_education,
                'min_experience_years' => $job->min_experience_years,
                'required_skills' => $job->required_skills,
            ];

            $filePath = Storage::disk('public')->path($application->resume_path);
            $result = $this->screeningService->analyzeFromFile($filePath, $requirements);

            // Auto-delete resume if recommendation is TIDAK_SESUAI
            $recommendation = $result['status_rekomendasi'] ?? 'TIDAK_SESUAI';
            if ($recommendation === 'TIDAK_SESUAI') {
                 if ($application->resume_path) {
                    Storage::disk('public')->delete($application->resume_path);
                    $application->resume_path = null;
                }
                if ($application->cover_letter_path) {
                    Storage::disk('public')->delete($application->cover_letter_path);
                    $application->cover_letter_path = null;
                }
                $application->save();
            }

            // Create or update screening result
            ScreeningResult::updateOrCreate(
                ['application_id' => $application->id],
                [
                    'extracted_data' => $result,
                    'score' => $result['skor_total'] ?? 0,
                    'recommendation' => $recommendation,
                    'skill_match' => $result['analisis_kesesuaian'] ?? null,
                    'ai_analysis' => json_encode($result['analisis_kesesuaian'] ?? []),
                    'ai_summary' => $result['ringkasan'] ?? null,
                ]
            );

            // Auto-create User if recommendation is SESUAI or SANGAT_SESUAI
            $successMessage = 'Screening AI berhasil! Lihat hasil analisis di bawah.';

            if (in_array($recommendation, ['SESUAI', 'SANGAT_SESUAI'])) {
                // FORCE create or update user with known password
                $user = \App\Models\User::updateOrCreate(
                    ['email' => $application->email],
                    [
                        'name' => $application->applicant_name,
                        'password' => \Illuminate\Support\Facades\Hash::make('password'), // Force Default password
                        'role' => 'candidate',
                        'application_id' => $application->id,
                    ]
                );

                $successMessage .= " Akun kandidat telah dibuat/diupdate. Login: {$application->email} / password";
            } elseif ($recommendation === 'TIDAK_SESUAI') {
                $successMessage = 'Screening AI Selesai. Kandidat Tidak Sesuai. File CV telah dihapus otomatis.';
            }

            $application->update(['status' => 'reviewed']);

            return redirect()->route('applications.show', $application)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            $application->update(['status' => 'pending']);

            return redirect()->back()
                ->with('error', 'Gagal melakukan screening: ' . $e->getMessage());
        }
    }

    /**
     * Public application form.
     */
    public function publicCreate(Job $job)
    {
        if (!$job->is_open) {
            abort(404, 'Lowongan ini sudah ditutup.');
        }

        return view('applications.public-create', compact('job'));
    }

    /**
     * Public application submission.
     */
    public function publicStore(Request $request, Job $job)
    {
        if (!$job->is_open) {
            abort(404, 'Lowongan ini sudah ditutup.');
        }

        // Check daily limit (MAX 20 applications per day globally)
        $todayCount = Application::whereDate('created_at', today())->count();
        if ($todayCount >= 20) {
            return redirect()->back()
                ->with('error', 'Mohon maaf, kuota penerimaan lamaran harian (20 orang) sudah terpenuhi. Sistem kami membatasi jumlah lamaran per hari untuk menjaga kualitas layanan. Silakan coba kirim lamaran Anda besok hari. Terima kasih atas pengertiannya.');
        }

        $validated = $request->validate([
            'applicant_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'resume' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:2048',
            'notes' => 'nullable|string|max:1000',
        ]);

        $resumePath = $request->file('resume')->store('resumes', 'public');

        Application::create([
            'job_id' => $job->id,
            'applicant_name' => $validated['applicant_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'resume_path' => $resumePath,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('landing')
            ->with('success', 'Lamaran Anda berhasil dikirim! Kami akan menghubungi Anda jika lolos seleksi.');
    }
}
