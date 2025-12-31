<?php

namespace Database\Seeders;

use App\Models\PsychometricTest;
use App\Models\PsychometricQuestion;
use Illuminate\Database\Seeder;

class WorkAbilityTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create Kraepelin-Pauli Style Test
        $kraepelinTest = PsychometricTest::create([
            'name' => 'Tes Kraepelin-Pauli',
            'slug' => 'kraepelin',
            'description' => 'Tes untuk mengukur kecepatan kerja, ketelitian, dan ketahanan kerja. Jumlahkan angka-angka yang berdekatan secepat dan seakurat mungkin.',
            'type' => 'aptitude',
            'duration_minutes' => 10,
            'total_questions' => 30,
            'is_active' => true,
            'settings' => [
                'scoring_method' => 'speed_accuracy',
                'dimensions' => ['speed', 'accuracy', 'consistency'],
            ],
        ]);

        // Generate Kraepelin questions (number addition)
        $kraepelinQuestions = $this->generateKraepelinQuestions(30);
        
        foreach ($kraepelinQuestions as $index => $q) {
            PsychometricQuestion::create([
                'test_id' => $kraepelinTest->id,
                'question_text' => $q['question'],
                'question_type' => 'multiple_choice',
                'options' => $q['options'],
                'dimension' => 'calculation',
                'order' => $index + 1,
                'is_active' => true,
            ]);
        }

        $this->command->info('Kraepelin-Pauli Test with 30 questions created!');

        // Create EPPS-style Motivation Test
        $eppsTest = PsychometricTest::create([
            'name' => 'Tes Motivasi Kerja (EPPS)',
            'slug' => 'epps',
            'description' => 'Tes untuk mengukur kebutuhan psikologis dan motivasi kerja. Pilih pernyataan yang paling menggambarkan diri Anda.',
            'type' => 'personality',
            'duration_minutes' => 20,
            'total_questions' => 20,
            'is_active' => true,
            'settings' => [
                'scoring_method' => 'sum',
                'dimensions' => ['achievement', 'order', 'autonomy', 'affiliation', 'dominance'],
            ],
        ]);

        $eppsQuestions = [
            // Achievement
            ['question' => 'Saya berusaha keras untuk mencapai target yang telah saya tetapkan.', 'dimension' => 'achievement'],
            ['question' => 'Saya ingin menjadi yang terbaik dalam pekerjaan saya.', 'dimension' => 'achievement'],
            ['question' => 'Saya tidak puas jika hasil pekerjaan saya hanya "cukup baik".', 'dimension' => 'achievement'],
            ['question' => 'Saya menikmati tantangan yang sulit.', 'dimension' => 'achievement'],

            // Order
            ['question' => 'Saya selalu merapikan meja kerja sebelum pulang.', 'dimension' => 'order'],
            ['question' => 'Saya membuat jadwal dan mengikutinya dengan ketat.', 'dimension' => 'order'],
            ['question' => 'Saya tidak nyaman dengan ketidakteraturan.', 'dimension' => 'order'],
            ['question' => 'Saya selalu merencanakan pekerjaan dengan detail.', 'dimension' => 'order'],

            // Autonomy
            ['question' => 'Saya lebih suka bekerja dengan cara saya sendiri.', 'dimension' => 'autonomy'],
            ['question' => 'Saya tidak suka diarahkan secara berlebihan.', 'dimension' => 'autonomy'],
            ['question' => 'Saya ingin memiliki kebebasan dalam mengambil keputusan.', 'dimension' => 'autonomy'],
            ['question' => 'Saya merasa terkekang dengan aturan yang terlalu ketat.', 'dimension' => 'autonomy'],

            // Affiliation
            ['question' => 'Saya senang bekerja dalam tim.', 'dimension' => 'affiliation'],
            ['question' => 'Hubungan baik dengan rekan kerja sangat penting bagi saya.', 'dimension' => 'affiliation'],
            ['question' => 'Saya sering membantu rekan kerja yang kesulitan.', 'dimension' => 'affiliation'],
            ['question' => 'Saya merasa sedih jika ada konflik di tempat kerja.', 'dimension' => 'affiliation'],

            // Dominance
            ['question' => 'Saya merasa nyaman memimpin sebuah proyek.', 'dimension' => 'dominance'],
            ['question' => 'Saya sering diminta untuk mengkoordinir kegiatan.', 'dimension' => 'dominance'],
            ['question' => 'Saya tidak ragu untuk memberikan arahan kepada orang lain.', 'dimension' => 'dominance'],
            ['question' => 'Saya ingin memiliki posisi dengan tanggung jawab besar.', 'dimension' => 'dominance'],
        ];

        $likertOptions = [
            ['value' => 1, 'label' => 'Sangat Tidak Setuju'],
            ['value' => 2, 'label' => 'Tidak Setuju'],
            ['value' => 3, 'label' => 'Netral'],
            ['value' => 4, 'label' => 'Setuju'],
            ['value' => 5, 'label' => 'Sangat Setuju'],
        ];

        foreach ($eppsQuestions as $index => $q) {
            PsychometricQuestion::create([
                'test_id' => $eppsTest->id,
                'question_text' => $q['question'],
                'question_type' => 'likert',
                'options' => $likertOptions,
                'dimension' => $q['dimension'],
                'order' => $index + 1,
                'is_active' => true,
            ]);
        }

        $this->command->info('EPPS Motivation Test with 20 questions created!');
    }

    /**
     * Generate Kraepelin-style arithmetic questions
     */
    private function generateKraepelinQuestions(int $count): array
    {
        $questions = [];
        
        for ($i = 0; $i < $count; $i++) {
            $num1 = rand(3, 9);
            $num2 = rand(3, 9);
            $correctAnswer = $num1 + $num2;
            
            // Generate wrong answers
            $wrongAnswers = [];
            while (count($wrongAnswers) < 3) {
                $wrong = $correctAnswer + rand(-3, 3);
                if ($wrong != $correctAnswer && $wrong > 0 && !in_array($wrong, $wrongAnswers)) {
                    $wrongAnswers[] = $wrong;
                }
            }
            
            $allAnswers = array_merge([$correctAnswer], $wrongAnswers);
            shuffle($allAnswers);
            
            $options = [];
            foreach ($allAnswers as $key => $answer) {
                $letter = chr(65 + $key); // A, B, C, D
                $options[] = [
                    'value' => $letter,
                    'label' => (string) $answer,
                    'correct' => $answer === $correctAnswer,
                ];
            }
            
            $questions[] = [
                'question' => "Berapa hasil dari: {$num1} + {$num2} = ?",
                'options' => $options,
            ];
        }
        
        return $questions;
    }
}
