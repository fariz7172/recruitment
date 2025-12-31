<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiScreeningService
{
    private ?string $apiKey;
    private string $model = 'gemini-1.5-pro';
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key') ?: null;
    }

    /**
     * Analyze resume from file (PDF/Image) directly using Gemini's multimodal capability.
     */
    public function analyzeFromFile(string $filePath, array $requirements): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        $mimeType = $this->getMimeType($filePath);
        $fileContent = base64_encode(file_get_contents($filePath));
        $prompt = $this->buildPrompt($requirements);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        [
                            'inlineData' => [
                                'mimeType' => $mimeType,
                                'data' => $fileContent,
                            ],
                        ],
                        [
                            'text' => $prompt,
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.3,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 8192,
            ],
        ]);

        if (!$response->successful()) {
            Log::error('Gemini API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Gemini API request failed: ' . $response->body());
        }

        $result = $response->json();
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

        return $this->parseResponse($text);
    }

    /**
     * Analyze resume from extracted text (if using separate OCR).
     */
    public function analyzeFromText(string $resumeText, array $requirements): array
    {
        $prompt = $this->buildPromptWithText($resumeText, $requirements);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt,
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.3,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 8192,
            ],
        ]);

        if (!$response->successful()) {
            Log::error('Gemini API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Gemini API request failed: ' . $response->body());
        }

        $result = $response->json();
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

        return $this->parseResponse($text);
    }

    /**
     * Build prompt for file-based analysis.
     */
    private function buildPrompt(array $requirements): string
    {
        $education = $requirements['min_education'] ?? 'S1';
        $experience = $requirements['min_experience_years'] ?? 0;
        $skills = is_array($requirements['required_skills']) 
            ? implode(', ', $requirements['required_skills']) 
            : ($requirements['required_skills'] ?? '');

        return <<<PROMPT
Kamu adalah sistem HR AI untuk screening kandidat kerja.

Baca dan analisis dokumen CV/Resume yang dilampirkan dengan teliti.

KUALIFIKASI YANG DIBUTUHKAN:
- Pendidikan minimal: {$education}
- Pengalaman kerja: minimal {$experience} tahun
- Skills yang dibutuhkan: {$skills}

TUGAS:
1. Ekstrak semua informasi penting dari CV
2. Bandingkan dengan kualifikasi yang dibutuhkan
3. Berikan skor kesesuaian (0-100)
4. Berikan rekomendasi

BERIKAN OUTPUT DALAM FORMAT JSON VALID (tanpa markdown code block):
{
    "nama_lengkap": "string atau null jika tidak ditemukan",
    "email": "string atau null",
    "no_telepon": "string atau null",
    "alamat": "string atau null",
    "pendidikan": {
        "tingkat": "SD/SMP/SMA/D3/S1/S2/S3",
        "jurusan": "string atau null",
        "institusi": "string atau null",
        "tahun_lulus": "string atau null",
        "ipk": "string atau null"
    },
    "pengalaman_kerja": [
        {
            "perusahaan": "string",
            "posisi": "string",
            "periode": "string (contoh: Jan 2020 - Des 2022)",
            "durasi_bulan": 24,
            "deskripsi": "string singkat tentang tugas"
        }
    ],
    "total_pengalaman_tahun": 2.5,
    "skills": ["skill1", "skill2"],
    "sertifikasi": ["sertifikasi1"],
    "bahasa": ["Bahasa Indonesia", "English"],
    "analisis_kesesuaian": {
        "pendidikan_sesuai": true,
        "alasan_pendidikan": "penjelasan singkat",
        "pengalaman_sesuai": true,
        "alasan_pengalaman": "penjelasan singkat",
        "skills_terpenuhi": ["skill yang match"],
        "skills_kurang": ["skill yang belum ada"],
        "kelebihan": ["poin plus kandidat"],
        "kekurangan": ["area yang perlu diperhatikan"]
    },
    "skor_total": 75,
    "status_rekomendasi": "SANGAT_SESUAI",
    "ringkasan": "Penjelasan 2-3 kalimat mengapa kandidat ini direkomendasikan atau tidak"
}

CATATAN PENTING:
- status_rekomendasi HARUS salah satu dari: SANGAT_SESUAI (skor >= 80), SESUAI (skor 60-79), PERTIMBANGKAN (skor 40-59), TIDAK_SESUAI (skor < 40)
- Berikan HANYA JSON, tanpa penjelasan tambahan, tanpa markdown code block
- Jika informasi tidak ditemukan di CV, gunakan null
PROMPT;
    }

    /**
     * Build prompt for text-based analysis.
     */
    private function buildPromptWithText(string $resumeText, array $requirements): string
    {
        $basePrompt = $this->buildPrompt($requirements);
        
        return <<<PROMPT
{$basePrompt}

ISI CV/RESUME:
{$resumeText}
PROMPT;
    }

    /**
     * Parse Gemini response to structured array.
     */
    private function parseResponse(string $response): array
    {
        // Clean response - remove markdown code blocks if present
        $cleaned = preg_replace('/```json\s*|\s*```/', '', $response);
        $cleaned = trim($cleaned);

        // Try to extract JSON from response
        if (preg_match('/\{[\s\S]*\}/', $cleaned, $matches)) {
            $cleaned = $matches[0];
        }

        $data = json_decode($cleaned, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Failed to parse Gemini response', [
                'error' => json_last_error_msg(),
                'response' => $response,
            ]);
            
            // Return default structure if parsing fails
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
                ],
                'skor_total' => 0,
                'status_rekomendasi' => 'TIDAK_SESUAI',
                'ringkasan' => 'Gagal menganalisis dokumen. Silakan coba lagi atau upload dokumen yang lebih jelas.',
                '_parsing_error' => true,
            ];
        }

        return $data;
    }

    /**
     * Get MIME type from file extension.
     */
    private function getMimeType(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        return match ($extension) {
            'pdf' => 'application/pdf',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            default => throw new \Exception("Unsupported file type: {$extension}. Supported: PDF, JPG, PNG, WEBP"),
        };
    }

    /**
     * Check if API key is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
