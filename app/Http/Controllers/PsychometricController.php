<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\PsychometricTest;
use App\Models\PsychometricQuestion;
use App\Models\PsychometricResult;
use App\Services\GroqScreeningService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PsychometricController extends Controller
{
    public function __construct(
        private GroqScreeningService $aiService
    ) {}

    /**
     * Show available tests for an application.
     */
    public function index(Application $application)
    {
        // Check eligibility - only SESUAI or SANGAT_SESUAI can take tests
        if (!$this->isEligible($application)) {
            return redirect()->route('applications.show', $application)
                ->with('error', 'Kandidat tidak memenuhi syarat untuk mengikuti tes psikotes. Status rekomendasi harus SESUAI atau SANGAT_SESUAI.');
        }

        $tests = PsychometricTest::active()->get();
        $completedTests = $application->psychometricResults()
            ->whereNotNull('completed_at')
            ->pluck('test_id')
            ->toArray();

        return view('psychometric.index', compact('application', 'tests', 'completedTests'));
    }

    /**
     * Start a test.
     */
    public function start(Application $application, PsychometricTest $test)
    {
        if (!$this->isEligible($application)) {
            return redirect()->route('applications.show', $application)
                ->with('error', 'Kandidat tidak memenuhi syarat untuk mengikuti tes.');
        }

        // Check if already completed
        $existingResult = PsychometricResult::where('application_id', $application->id)
            ->where('test_id', $test->id)
            ->first();

        if ($existingResult && $existingResult->isCompleted()) {
            return redirect()->route('psychometric.result', [$application, $test])
                ->with('info', 'Tes sudah pernah dikerjakan.');
        }

        // Create or get in-progress result
        $result = PsychometricResult::updateOrCreate(
            ['application_id' => $application->id, 'test_id' => $test->id],
            ['started_at' => $existingResult?->started_at ?? now()]
        );

        // Get questions and randomize order to prevent cheating
        $questions = $test->activeQuestions()->get()->shuffle();

        return view('psychometric.take', compact('application', 'test', 'questions', 'result'));
    }

    /**
     * Submit test answers.
     */
    public function submit(Request $request, Application $application, PsychometricTest $test)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required',
            'started_at' => 'required',
        ]);

        $questions = $test->activeQuestions()->get()->keyBy('id');
        
        // Calculate completion time
        $startedAt = Carbon::parse($validated['started_at']);
        $completionTime = $startedAt->diffInSeconds(now());

        // Different scoring based on test type
        if ($test->type === 'cognitive' || $test->type === 'aptitude') {
            // Multiple choice scoring (correct/incorrect)
            $result = $this->scoreCognitiveTest($validated['answers'], $questions, $test);
        } else {
            // Personality test (Likert scale) - DISC, EPPS
            $result = $this->scorePersonalityTest($validated['answers'], $questions, $test);
        }

        // Generate AI analysis
        $analysis = $this->generateAnalysisForTest($result['scores'], $result['primary_type'], $application, $test);

        // Save result
        PsychometricResult::updateOrCreate(
            ['application_id' => $application->id, 'test_id' => $test->id],
            [
                'answers' => $validated['answers'],
                'scores' => $result['scores'],
                'primary_type' => $result['primary_type'],
                'secondary_type' => $result['secondary_type'],
                'analysis' => $analysis,
                'completion_time_seconds' => $completionTime,
                'completed_at' => now(),
            ]
        );

        return redirect()->route('psychometric.result', [$application, $test])
            ->with('success', 'Tes berhasil diselesaikan!');
    }

    /**
     * Score cognitive/aptitude tests (correct/incorrect answers)
     */
    private function scoreCognitiveTest(array $answers, $questions, PsychometricTest $test): array
    {
        $correct = 0;
        $total = $questions->count();
        $dimensionScores = [];

        foreach ($answers as $questionId => $answer) {
            $question = $questions->get($questionId);
            if (!$question) continue;

            // Find if answer is correct
            $isCorrect = false;
            foreach ($question->options as $option) {
                if ($option['value'] == $answer && ($option['correct'] ?? false)) {
                    $isCorrect = true;
                    break;
                }
            }

            if ($isCorrect) {
                $correct++;
            }

            // Track dimension scores
            $dimension = $question->dimension ?? 'general';
            if (!isset($dimensionScores[$dimension])) {
                $dimensionScores[$dimension] = ['correct' => 0, 'total' => 0];
            }
            $dimensionScores[$dimension]['total']++;
            if ($isCorrect) {
                $dimensionScores[$dimension]['correct']++;
            }
        }

        // Calculate percentage scores per dimension
        $scores = [];
        foreach ($dimensionScores as $dim => $data) {
            $scores[$dim] = $data['total'] > 0 
                ? round(($data['correct'] / $data['total']) * 100) 
                : 0;
        }

        // Overall score
        $scores['total'] = $total > 0 ? round(($correct / $total) * 100) : 0;
        $scores['correct_count'] = $correct;
        $scores['total_count'] = $total;

        // Determine grade
        $primaryType = $this->getCognitiveGrade($scores['total']);

        return [
            'scores' => $scores,
            'primary_type' => $primaryType,
            'secondary_type' => "{$correct}/{$total}",
        ];
    }

    /**
     * Get cognitive test grade
     */
    private function getCognitiveGrade(int $percentage): string
    {
        if ($percentage >= 90) return 'SANGAT_TINGGI';
        if ($percentage >= 75) return 'TINGGI';
        if ($percentage >= 60) return 'CUKUP';
        if ($percentage >= 40) return 'RENDAH';
        return 'SANGAT_RENDAH';
    }

    /**
     * Score personality tests (Likert scale)
     */
    private function scorePersonalityTest(array $answers, $questions, PsychometricTest $test): array
    {
        $scores = [];
        
        foreach ($answers as $questionId => $answer) {
            $question = $questions->get($questionId);
            if (!$question) continue;

            $dimension = $question->dimension;
            if (!isset($scores[$dimension])) {
                $scores[$dimension] = 0;
            }
            $scores[$dimension] += (int) $answer;
        }

        // Sort and get primary/secondary
        arsort($scores);
        $types = array_keys($scores);
        
        return [
            'scores' => $scores,
            'primary_type' => $types[0] ?? 'N/A',
            'secondary_type' => $types[1] ?? 'N/A',
        ];
    }

    /**
     * Show test result.
     */
    public function result(Application $application, PsychometricTest $test)
    {
        $result = PsychometricResult::where('application_id', $application->id)
            ->where('test_id', $test->id)
            ->firstOrFail();

        return view('psychometric.result', compact('application', 'test', 'result'));
    }

    /**
     * Check if application is eligible for tests.
     */
    private function isEligible(Application $application): bool
    {
        $screeningResult = $application->screeningResult;
        
        if (!$screeningResult) {
            return false;
        }

        $eligibleStatuses = ['SANGAT_SESUAI', 'SESUAI'];
        return in_array($screeningResult->recommendation, $eligibleStatuses);
    }

    /**
     * Generate AI analysis for test results.
     */
    private function generateAnalysisForTest(array $scores, string $primaryType, Application $application, PsychometricTest $test): string
    {
        $jobTitle = $application->job->title ?? 'posisi yang dilamar';

        try {
            if ($test->type === 'cognitive' || $test->type === 'aptitude') {
                return $this->generateCognitiveAnalysis($scores, $primaryType, $jobTitle, $test);
            } else {
                return $this->generatePersonalityAnalysis($scores, $primaryType, $jobTitle, $test);
            }
        } catch (\Exception $e) {
            return $this->getDefaultAnalysisForTest($primaryType, $test);
        }
    }

    /**
     * Generate analysis for cognitive/aptitude tests.
     */
    private function generateCognitiveAnalysis(array $scores, string $grade, string $jobTitle, PsychometricTest $test): string
    {
        $totalScore = $scores['total'] ?? 0;
        $correct = $scores['correct_count'] ?? 0;
        $total = $scores['total_count'] ?? 0;

        $gradeLabels = [
            'SANGAT_TINGGI' => 'Sangat Tinggi',
            'TINGGI' => 'Tinggi',
            'CUKUP' => 'Cukup',
            'RENDAH' => 'Rendah',
            'SANGAT_RENDAH' => 'Sangat Rendah',
        ];

        $gradeLabel = $gradeLabels[$grade] ?? 'Tidak Diketahui';

        $prompt = <<<PROMPT
Hasil tes kognitif "{$test->name}":
- Skor: {$totalScore}% ({$correct}/{$total} jawaban benar)
- Kategori: {$gradeLabel}
- Posisi yang dilamar: {$jobTitle}

Berikan analisis singkat (2-3 kalimat) tentang kemampuan kognitif kandidat dan kesesuaiannya dengan posisi tersebut.
PROMPT;

        $result = $this->aiService->analyzeFromText($prompt, [
            'min_education' => '',
            'min_experience_years' => 0,
            'required_skills' => [],
        ]);

        return $result['ringkasan'] ?? "Kandidat memperoleh skor {$totalScore}% ({$gradeLabel}). {$correct} dari {$total} soal dijawab dengan benar.";
    }

    /**
     * Generate analysis for personality tests (DISC, EPPS).
     */
    private function generatePersonalityAnalysis(array $scores, string $primaryType, string $jobTitle, PsychometricTest $test): string
    {
        $scoresText = '';
        foreach ($scores as $dim => $score) {
            $scoresText .= "- {$dim}: {$score} poin\n";
        }

        $prompt = <<<PROMPT
Hasil tes kepribadian "{$test->name}":
{$scoresText}
Tipe dominan: {$primaryType}
Posisi yang dilamar: {$jobTitle}

Berikan analisis singkat (2-3 kalimat) tentang kepribadian kandidat dan kesesuaiannya dengan posisi tersebut.
PROMPT;

        $result = $this->aiService->analyzeFromText($prompt, [
            'min_education' => '',
            'min_experience_years' => 0,
            'required_skills' => [],
        ]);

        return $result['ringkasan'] ?? $this->getDefaultAnalysisForTest($primaryType, $test);
    }

    /**
     * Get default analysis text based on test type.
     */
    private function getDefaultAnalysisForTest(string $type, PsychometricTest $test): string
    {
        if ($test->type === 'cognitive' || $test->type === 'aptitude') {
            $cognitiveAnalysis = [
                'SANGAT_TINGGI' => 'Kandidat menunjukkan kemampuan kognitif yang sangat tinggi. Mampu menyelesaikan soal dengan cepat dan akurat.',
                'TINGGI' => 'Kandidat memiliki kemampuan kognitif di atas rata-rata. Menunjukkan penalaran logis yang baik.',
                'CUKUP' => 'Kandidat memiliki kemampuan kognitif yang memadai. Mampu menyelesaikan sebagian besar soal dengan benar.',
                'RENDAH' => 'Kandidat menunjukkan kemampuan kognitif di bawah rata-rata. Perlu pengembangan lebih lanjut.',
                'SANGAT_RENDAH' => 'Kandidat perlu peningkatan signifikan dalam kemampuan kognitif.',
            ];
            return $cognitiveAnalysis[$type] ?? 'Hasil tes telah dicatat.';
        }

        // Personality tests
        $discAnalysis = [
            'D' => 'Kandidat memiliki kepribadian Dominance yang kuat. Tegas, kompetitif, dan fokus pada hasil.',
            'I' => 'Kandidat memiliki kepribadian Influence yang menonjol. Antusias, optimis, dan pandai bersosialisasi.',
            'S' => 'Kandidat memiliki kepribadian Steadiness yang dominan. Tenang, sabar, dan dapat diandalkan.',
            'C' => 'Kandidat memiliki kepribadian Compliance yang kuat. Analitis, teliti, dan mengutamakan kualitas.',
        ];

        $eppsAnalysis = [
            'achievement' => 'Kandidat memiliki motivasi berprestasi tinggi. Selalu berusaha mencapai target.',
            'order' => 'Kandidat memiliki kebutuhan keteraturan yang kuat. Rapi dan terorganisir.',
            'autonomy' => 'Kandidat menghargai kemandirian dalam bekerja. Lebih suka cara kerja sendiri.',
            'affiliation' => 'Kandidat memiliki kebutuhan afiliasi tinggi. Senang bekerja dalam tim.',
            'dominance' => 'Kandidat memiliki kecenderungan memimpin. Nyaman mengkoordinir kegiatan.',
        ];

        if ($test->slug === 'epps') {
            return $eppsAnalysis[$type] ?? 'Hasil tes menunjukkan profil motivasi yang beragam.';
        }

        return $discAnalysis[$type] ?? 'Hasil tes menunjukkan profil kepribadian yang beragam.';
    }

    /**
     * Generate Final Executive Summary for all psychometric tests.
     */
    public function generateFinalSummary(Application $application)
    {
        $psychometricResults = $application->psychometricResults()->with('test')->get();
        
        if ($psychometricResults->isEmpty()) {
            return back()->with('error', 'Belum ada tes psikotes yang diselesaikan.');
        }

        $summaryData = [];
        $jobTitle = $application->job->title ?? 'posisi yang dilamar';

        foreach ($psychometricResults as $result) {
            if (!$result->completed_at) continue;

            $testName = $result->test->name;
            $primaryType = $result->primary_type;
            $scores = $result->scores;
            
            // Format scores for readability
            $scoreDetails = "";
            if (isset($scores['total'])) {
                $scoreDetails = "Skor: {$scores['total']}%";
            } elseif (is_array($scores)) {
                $topDimensions = array_slice($scores, 0, 3, true); // Top 3 dimensions
                $dimStr = [];
                foreach ($topDimensions as $k => $v) {
                    $dimStr[] = "$k ($v)";
                }
                $scoreDetails = "Dimensi Dominan: " . implode(", ", $dimStr);
            }

            $summaryData[] = "- {$testName}: {$primaryType}. {$scoreDetails}";
        }

        $dataText = implode("\n", $summaryData);

        $prompt = <<<PROMPT
Bertindaklah sebagai Kepala HRD (Human Resources). 
Aplikasi Rekrutmen: {$jobTitle}
Nama Kandidat: {$application->applicant_name}

Berikut adalah rangkuman hasil tes psikotes kandidat ini:
{$dataText}

Tugas Anda:
Buatlah "Rangkuman Eksekutif & Rekomendasi Akhir" (1 paragraf utuh, maksimal 150 kata).
1. Sintesakan hasil-hasil tes tersebut menjadi satu narasi kepribadian dan potensi kandidat.
2. Hubungkan dengan kecocokan untuk posisi {$jobTitle}.
3. Berikan rekomendasi tegas: Apakah kandidat ini **Sangat Disarankan**, **Disarankan**, atau **Perlu Pertimbangan** untuk posisi tersebut, dan alasannya.

Gunakan bahasa profesional, objektif, dan persuasif. Jangan hanya mengulang skor, tapi berikan *insight*.
PROMPT;

        $analysis = $this->aiService->analyzeFromText($prompt, []);
        $finalSummary = $analysis['ringkasan'] ?? 'Gagal membuat rangkuman.';

        // Save to ScreeningResult
        if (!$application->screeningResult) {
            // Create empty screening result if not exists (edge case)
            $application->screeningResult()->create([
                'application_id' => $application->id,
                'psychometric_summary' => $finalSummary
            ]);
        } else {
            $application->screeningResult->update([
                'psychometric_summary' => $finalSummary
            ]);
        }

        return back()->with('success', 'Rangkuman akhir berhasil dibuat!');
    }
}

