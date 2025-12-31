<?php

namespace Database\Seeders;

use App\Models\PsychometricTest;
use App\Models\PsychometricQuestion;
use Illuminate\Database\Seeder;

class DISCTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create DISC Test
        $test = PsychometricTest::create([
            'name' => 'Tes Kepribadian DISC',
            'slug' => 'disc',
            'description' => 'Tes kepribadian DISC mengukur 4 dimensi: Dominance (D), Influence (I), Steadiness (S), dan Compliance (C). Hasil tes ini membantu memahami gaya kerja dan komunikasi kandidat.',
            'type' => 'personality',
            'duration_minutes' => 15,
            'total_questions' => 24,
            'is_active' => true,
            'settings' => [
                'dimensions' => ['D', 'I', 'S', 'C'],
                'scoring_method' => 'sum',
            ],
        ]);

        // DISC Questions - 6 questions per dimension
        $questions = [
            // Dominance (D) Questions
            ['dimension' => 'D', 'question' => 'Saya lebih suka memimpin daripada dipimpin dalam sebuah tim.'],
            ['dimension' => 'D', 'question' => 'Saya cenderung mengambil keputusan dengan cepat dan tegas.'],
            ['dimension' => 'D', 'question' => 'Saya menikmati tantangan dan kompetisi.'],
            ['dimension' => 'D', 'question' => 'Saya langsung menyampaikan pendapat meskipun kontroversial.'],
            ['dimension' => 'D', 'question' => 'Saya fokus pada hasil akhir, bukan prosesnya.'],
            ['dimension' => 'D', 'question' => 'Saya tidak takut menghadapi konflik jika diperlukan.'],

            // Influence (I) Questions
            ['dimension' => 'I', 'question' => 'Saya mudah bergaul dan menikmati berinteraksi dengan orang baru.'],
            ['dimension' => 'I', 'question' => 'Saya cenderung antusias dan optimis dalam berbagai situasi.'],
            ['dimension' => 'I', 'question' => 'Saya pandai memotivasi dan mempengaruhi orang lain.'],
            ['dimension' => 'I', 'question' => 'Saya lebih suka bekerja dalam tim daripada sendirian.'],
            ['dimension' => 'I', 'question' => 'Saya sering menjadi pusat perhatian dalam percakapan.'],
            ['dimension' => 'I', 'question' => 'Saya sangat ekspresif dalam menunjukkan emosi.'],

            // Steadiness (S) Questions
            ['dimension' => 'S', 'question' => 'Saya lebih suka rutinitas dan stabilitas daripada perubahan mendadak.'],
            ['dimension' => 'S', 'question' => 'Saya sabar dan tenang dalam menghadapi tekanan.'],
            ['dimension' => 'S', 'question' => 'Saya sangat loyal kepada tim dan organisasi.'],
            ['dimension' => 'S', 'question' => 'Saya lebih suka mendengarkan daripada berbicara.'],
            ['dimension' => 'S', 'question' => 'Saya berusaha menghindari konflik dan menjaga harmoni.'],
            ['dimension' => 'S', 'question' => 'Saya dapat diandalkan untuk menyelesaikan tugas dengan konsisten.'],

            // Compliance (C) Questions
            ['dimension' => 'C', 'question' => 'Saya sangat teliti dan memperhatikan detail.'],
            ['dimension' => 'C', 'question' => 'Saya mengikuti aturan dan prosedur dengan ketat.'],
            ['dimension' => 'C', 'question' => 'Saya menganalisis data dengan cermat sebelum mengambil keputusan.'],
            ['dimension' => 'C', 'question' => 'Kualitas lebih penting bagi saya daripada kecepatan.'],
            ['dimension' => 'C', 'question' => 'Saya skeptis dan selalu memverifikasi informasi.'],
            ['dimension' => 'C', 'question' => 'Saya lebih suka bekerja dengan data dan fakta daripada intuisi.'],
        ];

        // Likert scale options
        $options = [
            ['value' => 1, 'label' => 'Sangat Tidak Setuju'],
            ['value' => 2, 'label' => 'Tidak Setuju'],
            ['value' => 3, 'label' => 'Netral'],
            ['value' => 4, 'label' => 'Setuju'],
            ['value' => 5, 'label' => 'Sangat Setuju'],
        ];

        foreach ($questions as $index => $q) {
            PsychometricQuestion::create([
                'test_id' => $test->id,
                'question_text' => $q['question'],
                'question_type' => 'likert',
                'options' => $options,
                'dimension' => $q['dimension'],
                'order' => $index + 1,
                'is_active' => true,
            ]);
        }

        $this->command->info('DISC Test with 24 questions created!');
    }
}
