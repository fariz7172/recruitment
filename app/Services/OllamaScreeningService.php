<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaScreeningService
{
    private string $model;
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.ollama.url', 'http://localhost:11434');
        $this->model = config('services.ollama.model', 'gemma2:2b');
    }

    /**
     * Analyze resume from file - extract text first then analyze.
     * Note: gemma2 doesn't support images, so we need text input.
     */
    public function analyzeFromFile(string $filePath, array $requirements): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        // For PDF/images, we need to inform user about limitation
        // In production, you'd use OCR (like Tesseract) for images
        $fileInfo = "Nama file: " . basename($filePath) . " (Format: {$extension})";
        
        // For now, analyze based on available information
        $prompt = $this->buildPromptForAnalysis($requirements, $fileInfo);

        return $this->callOllama($prompt);
    }

    /**
     * Analyze resume from text.
     */
    public function analyzeFromText(string $resumeText, array $requirements): array
    {
        $prompt = $this->buildPromptWithText($resumeText, $requirements);
        return $this->callOllama($prompt);
    }

    /**
     * Call Ollama API.
     */
    private function callOllama(string $prompt): array
    {
        try {
            $response = Http::timeout(120)->post("{$this->baseUrl}/api/generate", [
                'model' => $this->model,
                'prompt' => $prompt,
                'stream' => false,
                'options' => [
                    'temperature' => 0.3,
                    'num_predict' => 4096,
                ],
            ]);

            if (!$response->successful()) {
                Log::error('Ollama API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Ollama API request failed: ' . $response->body());
            }

            $result = $response->json();
            $text = $result['response'] ?? '';

            return $this->parseResponse($text);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \Exception('Tidak dapat terhubung ke Ollama. Pastikan Ollama berjalan di ' . $this->baseUrl);
        }
    }

    /**
     * Build prompt for analysis.
     */
    private function buildPromptForAnalysis(array $requirements, string $fileInfo): string
    {
        $education = $requirements['min_education'] ?? 'S1';
        $experience = $requirements['min_experience_years'] ?? 0;
        $skills = is_array($requirements['required_skills']) 
            ? implode(', ', $requirements['required_skills']) 
            : ($requirements['required_skills'] ?? '');

        return <<<PROMPT
Kamu adalah sistem HR AI untuk screening kandidat kerja.

{$fileInfo}

KUALIFIKASI YANG DIBUTUHKAN:
- Pendidikan minimal: {$education}
- Pengalaman kerja: minimal {$experience} tahun
- Skills yang dibutuhkan: {$skills}

Karena file CV tidak bisa dibaca secara langsung, berikan analisis placeholder berdasarkan persyaratan di atas.

BERIKAN OUTPUT DALAM FORMAT JSON VALID:
{
    "nama_lengkap": null,
    "email": null,
    "pendidikan": {
        "tingkat": null,
        "jurusan": null,
        "institusi": null
    },
    "total_pengalaman_tahun": 0,
    "skills": [],
    "analisis_kesesuaian": {
        "pendidikan_sesuai": false,
        "pengalaman_sesuai": false,
        "skills_terpenuhi": [],
        "skills_kurang": {$this->formatArrayForJson($requirements['required_skills'] ?? [])},
        "kelebihan": [],
        "kekurangan": ["CV perlu direview manual karena format file"]
    },
    "skor_total": 50,
    "status_rekomendasi": "PERTIMBANGKAN",
    "ringkasan": "CV telah diterima dan memerlukan review manual oleh HR. Pastikan kandidat memenuhi kualifikasi: {$education}, pengalaman {$experience} tahun, dan skill: {$skills}"
}

HANYA berikan JSON, tanpa penjelasan lain.
PROMPT;
    }

    /**
     * Build prompt with actual resume text.
     */
    private function buildPromptWithText(string $resumeText, array $requirements): string
    {
        $education = $requirements['min_education'] ?? 'S1';
        $experience = $requirements['min_experience_years'] ?? 0;
        $skills = is_array($requirements['required_skills']) 
            ? implode(', ', $requirements['required_skills']) 
            : ($requirements['required_skills'] ?? '');

        return <<<PROMPT
Kamu adalah sistem HR AI untuk screening kandidat kerja.

KUALIFIKASI YANG DIBUTUHKAN:
- Pendidikan minimal: {$education}
- Pengalaman kerja: minimal {$experience} tahun
- Skills yang dibutuhkan: {$skills}

ISI CV/RESUME:
{$resumeText}

TUGAS:
1. Ekstrak informasi penting dari CV
2. Bandingkan dengan kualifikasi
3. Berikan skor (0-100)

BERIKAN OUTPUT DALAM FORMAT JSON:
{
    "nama_lengkap": "nama dari CV",
    "email": "email dari CV",
    "pendidikan": {"tingkat": "S1", "jurusan": "...", "institusi": "..."},
    "total_pengalaman_tahun": 2,
    "skills": ["skill1", "skill2"],
    "analisis_kesesuaian": {
        "pendidikan_sesuai": true,
        "pengalaman_sesuai": true,
        "skills_terpenuhi": ["skill match"],
        "skills_kurang": ["skill kurang"],
        "kelebihan": ["poin plus"],
        "kekurangan": ["poin minus"]
    },
    "skor_total": 75,
    "status_rekomendasi": "SESUAI",
    "ringkasan": "Penjelasan singkat"
}

status_rekomendasi: SANGAT_SESUAI (>=80), SESUAI (60-79), PERTIMBANGKAN (40-59), TIDAK_SESUAI (<40)
HANYA JSON, tanpa penjelasan.
PROMPT;
    }

    /**
     * Format array for JSON string.
     */
    private function formatArrayForJson(array $arr): string
    {
        return json_encode($arr);
    }

    /**
     * Parse response to structured array.
     */
    private function parseResponse(string $response): array
    {
        // Clean response
        $cleaned = preg_replace('/```json\s*|\s*```/', '', $response);
        $cleaned = trim($cleaned);

        // Extract JSON
        if (preg_match('/\{[\s\S]*\}/', $cleaned, $matches)) {
            $cleaned = $matches[0];
        }

        $data = json_decode($cleaned, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('Failed to parse Ollama response, using default', [
                'error' => json_last_error_msg(),
                'response' => substr($response, 0, 500),
            ]);
            
            return $this->getDefaultResponse();
        }

        return $data;
    }

    /**
     * Get default response when parsing fails.
     */
    private function getDefaultResponse(): array
    {
        return [
            'nama_lengkap' => null,
            'email' => null,
            'no_telepon' => null,
            'pendidikan' => null,
            'pengalaman_kerja' => [],
            'total_pengalaman_tahun' => 0,
            'skills' => [],
            'analisis_kesesuaian' => [
                'pendidikan_sesuai' => false,
                'pengalaman_sesuai' => false,
                'skills_terpenuhi' => [],
                'skills_kurang' => [],
                'kelebihan' => [],
                'kekurangan' => [],
            ],
            'skor_total' => 50,
            'status_rekomendasi' => 'PERTIMBANGKAN',
            'ringkasan' => 'CV berhasil diterima. Silakan review manual untuk hasil yang lebih akurat.',
        ];
    }

    /**
     * Check if Ollama is available.
     */
    public function isConfigured(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/api/tags");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
