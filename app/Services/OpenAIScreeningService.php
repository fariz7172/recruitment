<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIScreeningService
{
    private ?string $apiKey;
    private string $model = 'gpt-3.5-turbo';
    private string $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key') ?: null;
    }

    /**
     * Analyze resume from file.
     * Note: GPT-3.5/4 are text-only. For images, we'd need GPT-4 Vision.
     * This implementation focuses on text-based analysis.
     */
    public function analyzeFromFile(string $filePath, array $requirements): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $fileName = basename($filePath);
        
        // Build prompt with file info
        $prompt = $this->buildPrompt($requirements, $fileName, $extension);

        return $this->callOpenAI($prompt);
    }

    /**
     * Analyze resume from text.
     */
    public function analyzeFromText(string $resumeText, array $requirements): array
    {
        $prompt = $this->buildPromptWithText($resumeText, $requirements);
        return $this->callOpenAI($prompt);
    }

    /**
     * Call OpenAI API.
     */
    private function callOpenAI(string $prompt): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post("{$this->baseUrl}/chat/completions", [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Kamu adalah sistem HR AI untuk screening kandidat kerja. Selalu berikan output dalam format JSON valid tanpa markdown code blocks.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.3,
                'max_tokens' => 4096,
            ]);

            if (!$response->successful()) {
                Log::error('OpenAI API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('OpenAI API request failed: ' . $response->body());
            }

            $result = $response->json();
            $text = $result['choices'][0]['message']['content'] ?? '';

            return $this->parseResponse($text);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \Exception('Tidak dapat terhubung ke OpenAI API.');
        }
    }

    /**
     * Build prompt for file analysis.
     */
    private function buildPrompt(array $requirements, string $fileName, string $extension): string
    {
        $education = $requirements['min_education'] ?? 'S1';
        $experience = $requirements['min_experience_years'] ?? 0;
        $skills = is_array($requirements['required_skills']) 
            ? implode(', ', $requirements['required_skills']) 
            : ($requirements['required_skills'] ?? '');

        $skillsArray = json_encode($requirements['required_skills'] ?? []);

        return <<<PROMPT
File CV diterima: {$fileName} (format: {$extension})

KUALIFIKASI YANG DIBUTUHKAN:
- Pendidikan minimal: {$education}
- Pengalaman kerja: minimal {$experience} tahun
- Skills yang dibutuhkan: {$skills}

Karena file tidak bisa dibaca langsung, berikan analisis placeholder.

Berikan output JSON berikut:
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
        "skills_kurang": {$skillsArray},
        "kelebihan": ["CV berhasil diupload"],
        "kekurangan": ["CV perlu direview manual"]
    },
    "skor_total": 50,
    "status_rekomendasi": "PERTIMBANGKAN",
    "ringkasan": "CV telah diterima. Kandidat memerlukan review manual oleh HR untuk memverifikasi kualifikasi: pendidikan {$education}, pengalaman {$experience} tahun, dan skill: {$skills}."
}

HANYA berikan JSON, tanpa penjelasan lain.
PROMPT;
    }

    /**
     * Build prompt with resume text.
     */
    private function buildPromptWithText(string $resumeText, array $requirements): string
    {
        $education = $requirements['min_education'] ?? 'S1';
        $experience = $requirements['min_experience_years'] ?? 0;
        $skills = is_array($requirements['required_skills']) 
            ? implode(', ', $requirements['required_skills']) 
            : ($requirements['required_skills'] ?? '');

        return <<<PROMPT
KUALIFIKASI YANG DIBUTUHKAN:
- Pendidikan minimal: {$education}
- Pengalaman kerja: minimal {$experience} tahun
- Skills yang dibutuhkan: {$skills}

ISI CV/RESUME:
{$resumeText}

TUGAS:
1. Ekstrak informasi penting dari CV
2. Bandingkan dengan kualifikasi yang dibutuhkan
3. Berikan skor kesesuaian (0-100)
4. Berikan rekomendasi

Berikan output JSON:
{
    "nama_lengkap": "nama dari CV atau null",
    "email": "email dari CV atau null",
    "pendidikan": {
        "tingkat": "SMA/D3/S1/S2/S3",
        "jurusan": "jurusan atau null",
        "institusi": "nama institusi atau null"
    },
    "total_pengalaman_tahun": 0,
    "skills": ["skill1", "skill2"],
    "analisis_kesesuaian": {
        "pendidikan_sesuai": true/false,
        "pengalaman_sesuai": true/false,
        "skills_terpenuhi": ["skill yang match"],
        "skills_kurang": ["skill yang kurang"],
        "kelebihan": ["kelebihan kandidat"],
        "kekurangan": ["kekurangan kandidat"]
    },
    "skor_total": 75,
    "status_rekomendasi": "SESUAI",
    "ringkasan": "Penjelasan 2-3 kalimat"
}

status_rekomendasi: SANGAT_SESUAI (>=80), SESUAI (60-79), PERTIMBANGKAN (40-59), TIDAK_SESUAI (<40)
HANYA JSON, tanpa markdown code block.
PROMPT;
    }

    /**
     * Parse OpenAI response.
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
            Log::warning('Failed to parse OpenAI response', [
                'error' => json_last_error_msg(),
                'response' => substr($response, 0, 500),
            ]);
            
            return $this->getDefaultResponse();
        }

        return $data;
    }

    /**
     * Get default response.
     */
    private function getDefaultResponse(): array
    {
        return [
            'nama_lengkap' => null,
            'email' => null,
            'pendidikan' => null,
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
            'ringkasan' => 'CV berhasil diterima. Silakan review manual.',
        ];
    }

    /**
     * Check if API key is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
