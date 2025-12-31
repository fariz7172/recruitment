<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqScreeningService
{
    private ?string $apiKey;
    private string $model = 'llama-3.3-70b-versatile';
    private string $baseUrl = 'https://api.groq.com/openai/v1';

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key') ?: null;
    }

    /**
     * Analyze resume from file.
     * Extracts text from PDF/images first, then analyzes with AI.
     */
    public function analyzeFromFile(string $filePath, array $requirements): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        // Try to extract text from PDF
        $extractedText = '';
        if ($extension === 'pdf') {
            $extractedText = $this->extractTextFromPdf($filePath);
        }

        // If we got text, analyze it properly
        if (!empty(trim($extractedText))) {
            return $this->analyzeFromText($extractedText, $requirements);
        }

        // Fall back to placeholder if text extraction failed
        $fileName = basename($filePath);
        $prompt = $this->buildPrompt($requirements, $fileName, $extension);
        return $this->callGroq($prompt);
    }

    /**
     * Extract text from PDF file.
     */
    private function extractTextFromPdf(string $filePath): string
    {
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();
            
            // Clean up the text
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim($text);
            
            Log::info('PDF text extracted', ['length' => strlen($text)]);
            
            return $text;
        } catch (\Exception $e) {
            Log::warning('PDF text extraction failed', ['error' => $e->getMessage()]);
            return '';
        }
    }

    /**
     * Analyze resume from text.
     */
    public function analyzeFromText(string $resumeText, array $requirements): array
    {
        $prompt = $this->buildPromptWithText($resumeText, $requirements);
        return $this->callGroq($prompt);
    }

    /**
     * Call Groq API.
     */
    private function callGroq(string $prompt): array
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
                Log::error('Groq API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Groq API request failed: ' . $response->body());
            }

            $result = $response->json();
            $text = $result['choices'][0]['message']['content'] ?? '';

            return $this->parseResponse($text);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \Exception('Tidak dapat terhubung ke Groq API.');
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

Karena file tidak bisa dibaca langsung, berikan analisis placeholder yang menunjukkan CV berhasil diterima.

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
        "kelebihan": ["CV berhasil diupload", "File format valid ({$extension})"],
        "kekurangan": ["CV perlu direview manual oleh HR"]
    },
    "skor_total": 50,
    "status_rekomendasi": "PERTIMBANGKAN",
    "ringkasan": "CV dengan file {$fileName} telah diterima. Kandidat memerlukan review manual oleh HR untuk memverifikasi kesesuaian dengan kualifikasi: pendidikan minimal {$education}, pengalaman minimal {$experience} tahun, dan skill yang dibutuhkan: {$skills}."
}

HANYA berikan JSON tanpa penjelasan.
PROMPT;
    }

    /**
     * Build prompt with resume text.
     */
    private function buildPromptWithText(string $resumeText, array $requirements): string
    {
        $education = $requirements['min_education'] ?? 'S1';
        $experience = $requirements['min_experience_years'] ?? 0;
        $skills = isset($requirements['required_skills']) && is_array($requirements['required_skills']) 
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

Berikan output JSON:
{
    "nama_lengkap": "nama dari CV atau null",
    "email": "email dari CV atau null",
    "pendidikan": {"tingkat": "S1", "jurusan": "...", "institusi": "..."},
    "total_pengalaman_tahun": 2,
    "skills": ["skill1", "skill2"],
    "analisis_kesesuaian": {
        "pendidikan_sesuai": true,
        "pengalaman_sesuai": true,
        "skills_terpenuhi": ["skill yang match"],
        "skills_kurang": ["skill yang kurang"],
        "kelebihan": ["kelebihan"],
        "kekurangan": ["kekurangan"]
    },
    "skor_total": 75,
    "status_rekomendasi": "SESUAI",
    "ringkasan": "Penjelasan singkat"
}

status_rekomendasi: SANGAT_SESUAI (>=80), SESUAI (60-79), PERTIMBANGKAN (40-59), TIDAK_SESUAI (<40)
HANYA JSON.
PROMPT;
    }

    /**
     * Parse response.
     */
    private function parseResponse(string $response): array
    {
        $cleaned = preg_replace('/```json\s*|\s*```/', '', $response);
        $cleaned = trim($cleaned);

        if (preg_match('/\{[\s\S]*\}/', $cleaned, $matches)) {
            $cleaned = $matches[0];
        }

        $data = json_decode($cleaned, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('Failed to parse Groq response', [
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
     * Check if configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
