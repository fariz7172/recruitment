<?php

namespace Database\Seeders;

use App\Models\PsychometricTest;
use App\Models\PsychometricQuestion;
use Illuminate\Database\Seeder;

class CognitiveTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create Wonderlic-style Cognitive Test
        $test = PsychometricTest::create([
            'name' => 'Tes Kognitif (Wonderlic)',
            'slug' => 'wonderlic',
            'description' => 'Tes kemampuan kognitif cepat yang mengukur kemampuan berpikir logis, numerik, dan verbal. Waktu terbatas 12 menit untuk 20 soal.',
            'type' => 'cognitive',
            'duration_minutes' => 12,
            'total_questions' => 20,
            'is_active' => true,
            'settings' => [
                'scoring_method' => 'correct_answers',
                'time_limit' => true,
            ],
        ]);

        $questions = [
            // Verbal Reasoning
            [
                'question' => 'KUCING adalah seperti MEONG, maka ANJING adalah seperti...',
                'options' => [
                    ['value' => 'A', 'label' => 'Menggonggong', 'correct' => false],
                    ['value' => 'B', 'label' => 'GUK', 'correct' => true],
                    ['value' => 'C', 'label' => 'Berlari', 'correct' => false],
                    ['value' => 'D', 'label' => 'Ekor', 'correct' => false],
                ],
                'dimension' => 'verbal',
            ],
            [
                'question' => 'Manakah kata yang TIDAK termasuk dalam kelompok? APEL, MANGGA, WORTEL, JERUK',
                'options' => [
                    ['value' => 'A', 'label' => 'APEL', 'correct' => false],
                    ['value' => 'B', 'label' => 'MANGGA', 'correct' => false],
                    ['value' => 'C', 'label' => 'WORTEL', 'correct' => true],
                    ['value' => 'D', 'label' => 'JERUK', 'correct' => false],
                ],
                'dimension' => 'verbal',
            ],
            [
                'question' => 'BESAR lawan kata dari...',
                'options' => [
                    ['value' => 'A', 'label' => 'TINGGI', 'correct' => false],
                    ['value' => 'B', 'label' => 'LEBAR', 'correct' => false],
                    ['value' => 'C', 'label' => 'KECIL', 'correct' => true],
                    ['value' => 'D', 'label' => 'PENDEK', 'correct' => false],
                ],
                'dimension' => 'verbal',
            ],
            [
                'question' => 'Jika semua A adalah B, dan semua B adalah C, maka...',
                'options' => [
                    ['value' => 'A', 'label' => 'Semua A adalah C', 'correct' => true],
                    ['value' => 'B', 'label' => 'Semua C adalah A', 'correct' => false],
                    ['value' => 'C', 'label' => 'Tidak ada hubungan', 'correct' => false],
                    ['value' => 'D', 'label' => 'Beberapa C adalah A', 'correct' => false],
                ],
                'dimension' => 'verbal',
            ],

            // Numerical Reasoning
            [
                'question' => 'Berapa hasil dari: 15 + 27 - 12 = ?',
                'options' => [
                    ['value' => 'A', 'label' => '28', 'correct' => false],
                    ['value' => 'B', 'label' => '30', 'correct' => true],
                    ['value' => 'C', 'label' => '32', 'correct' => false],
                    ['value' => 'D', 'label' => '29', 'correct' => false],
                ],
                'dimension' => 'numerical',
            ],
            [
                'question' => 'Lanjutkan deret berikut: 2, 4, 8, 16, ?',
                'options' => [
                    ['value' => 'A', 'label' => '24', 'correct' => false],
                    ['value' => 'B', 'label' => '32', 'correct' => true],
                    ['value' => 'C', 'label' => '20', 'correct' => false],
                    ['value' => 'D', 'label' => '28', 'correct' => false],
                ],
                'dimension' => 'numerical',
            ],
            [
                'question' => 'Jika 3 pena harganya Rp 15.000, berapa harga 7 pena?',
                'options' => [
                    ['value' => 'A', 'label' => 'Rp 30.000', 'correct' => false],
                    ['value' => 'B', 'label' => 'Rp 35.000', 'correct' => true],
                    ['value' => 'C', 'label' => 'Rp 40.000', 'correct' => false],
                    ['value' => 'D', 'label' => 'Rp 25.000', 'correct' => false],
                ],
                'dimension' => 'numerical',
            ],
            [
                'question' => 'Berapa 25% dari 80?',
                'options' => [
                    ['value' => 'A', 'label' => '15', 'correct' => false],
                    ['value' => 'B', 'label' => '20', 'correct' => true],
                    ['value' => 'C', 'label' => '25', 'correct' => false],
                    ['value' => 'D', 'label' => '18', 'correct' => false],
                ],
                'dimension' => 'numerical',
            ],
            [
                'question' => 'Lanjutkan deret: 1, 1, 2, 3, 5, 8, ?',
                'options' => [
                    ['value' => 'A', 'label' => '11', 'correct' => false],
                    ['value' => 'B', 'label' => '13', 'correct' => true],
                    ['value' => 'C', 'label' => '10', 'correct' => false],
                    ['value' => 'D', 'label' => '12', 'correct' => false],
                ],
                'dimension' => 'numerical',
            ],
            [
                'question' => 'Berapa hasil dari: 144 ÷ 12 = ?',
                'options' => [
                    ['value' => 'A', 'label' => '11', 'correct' => false],
                    ['value' => 'B', 'label' => '12', 'correct' => true],
                    ['value' => 'C', 'label' => '13', 'correct' => false],
                    ['value' => 'D', 'label' => '14', 'correct' => false],
                ],
                'dimension' => 'numerical',
            ],

            // Logical Reasoning
            [
                'question' => 'Jika HARI INI adalah RABU, maka 3 hari yang lalu adalah...',
                'options' => [
                    ['value' => 'A', 'label' => 'SENIN', 'correct' => false],
                    ['value' => 'B', 'label' => 'MINGGU', 'correct' => true],
                    ['value' => 'C', 'label' => 'SABTU', 'correct' => false],
                    ['value' => 'D', 'label' => 'JUMAT', 'correct' => false],
                ],
                'dimension' => 'logical',
            ],
            [
                'question' => 'Ali lebih tinggi dari Budi. Citra lebih pendek dari Budi. Siapa yang paling tinggi?',
                'options' => [
                    ['value' => 'A', 'label' => 'Ali', 'correct' => true],
                    ['value' => 'B', 'label' => 'Budi', 'correct' => false],
                    ['value' => 'C', 'label' => 'Citra', 'correct' => false],
                    ['value' => 'D', 'label' => 'Tidak dapat ditentukan', 'correct' => false],
                ],
                'dimension' => 'logical',
            ],
            [
                'question' => 'Temukan pola: AZ, BY, CX, ?',
                'options' => [
                    ['value' => 'A', 'label' => 'DW', 'correct' => true],
                    ['value' => 'B', 'label' => 'DV', 'correct' => false],
                    ['value' => 'C', 'label' => 'EW', 'correct' => false],
                    ['value' => 'D', 'label' => 'DX', 'correct' => false],
                ],
                'dimension' => 'logical',
            ],
            [
                'question' => 'Meja : Kayu = Buku : ?',
                'options' => [
                    ['value' => 'A', 'label' => 'Kertas', 'correct' => true],
                    ['value' => 'B', 'label' => 'Membaca', 'correct' => false],
                    ['value' => 'C', 'label' => 'Penulis', 'correct' => false],
                    ['value' => 'D', 'label' => 'Rak', 'correct' => false],
                ],
                'dimension' => 'logical',
            ],
            [
                'question' => 'Jika pola berikut berlanjut: 🔴🔵🔴🔴🔵🔴🔴🔴🔵, maka selanjutnya adalah...',
                'options' => [
                    ['value' => 'A', 'label' => '🔴🔴🔴🔴🔵', 'correct' => true],
                    ['value' => 'B', 'label' => '🔵🔴🔴🔴🔴', 'correct' => false],
                    ['value' => 'C', 'label' => '🔴🔴🔵🔴🔴', 'correct' => false],
                    ['value' => 'D', 'label' => '🔵🔵🔴🔴🔴', 'correct' => false],
                ],
                'dimension' => 'logical',
            ],

            // Spatial/Abstract
            [
                'question' => 'Jika kubus memiliki 6 sisi, berapa sisi yang dimiliki oleh 3 kubus?',
                'options' => [
                    ['value' => 'A', 'label' => '12', 'correct' => false],
                    ['value' => 'B', 'label' => '18', 'correct' => true],
                    ['value' => 'C', 'label' => '24', 'correct' => false],
                    ['value' => 'D', 'label' => '9', 'correct' => false],
                ],
                'dimension' => 'spatial',
            ],
            [
                'question' => 'Berapa banyak segitiga dalam huruf "A"?',
                'options' => [
                    ['value' => 'A', 'label' => '1', 'correct' => false],
                    ['value' => 'B', 'label' => '2', 'correct' => false],
                    ['value' => 'C', 'label' => '3', 'correct' => true],
                    ['value' => 'D', 'label' => '4', 'correct' => false],
                ],
                'dimension' => 'spatial',
            ],
            [
                'question' => 'Jika cermin memiliki refleksi, huruf "b" akan terlihat seperti...',
                'options' => [
                    ['value' => 'A', 'label' => 'b', 'correct' => false],
                    ['value' => 'B', 'label' => 'd', 'correct' => true],
                    ['value' => 'C', 'label' => 'p', 'correct' => false],
                    ['value' => 'D', 'label' => 'q', 'correct' => false],
                ],
                'dimension' => 'spatial',
            ],

            // General Knowledge
            [
                'question' => 'Berapa bulan dalam 2 tahun?',
                'options' => [
                    ['value' => 'A', 'label' => '20', 'correct' => false],
                    ['value' => 'B', 'label' => '24', 'correct' => true],
                    ['value' => 'C', 'label' => '22', 'correct' => false],
                    ['value' => 'D', 'label' => '26', 'correct' => false],
                ],
                'dimension' => 'general',
            ],
            [
                'question' => 'Jika pesawat bergerak dengan kecepatan 600 km/jam, berapa lama waktu yang dibutuhkan untuk menempuh 300 km?',
                'options' => [
                    ['value' => 'A', 'label' => '20 menit', 'correct' => false],
                    ['value' => 'B', 'label' => '30 menit', 'correct' => true],
                    ['value' => 'C', 'label' => '45 menit', 'correct' => false],
                    ['value' => 'D', 'label' => '1 jam', 'correct' => false],
                ],
                'dimension' => 'general',
            ],
        ];

        foreach ($questions as $index => $q) {
            PsychometricQuestion::create([
                'test_id' => $test->id,
                'question_text' => $q['question'],
                'question_type' => 'multiple_choice',
                'options' => $q['options'],
                'dimension' => $q['dimension'],
                'order' => $index + 1,
                'is_active' => true,
            ]);
        }

        $this->command->info('Wonderlic Cognitive Test with 20 questions created!');
    }
}
