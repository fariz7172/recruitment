<?php

namespace Database\Seeders;

use App\Models\PsychometricTest;
use App\Models\PsychometricQuestion;
use Illuminate\Database\Seeder;

class LogicalReasoningTestSeeder extends Seeder
{
    public function run(): void
    {
        // Delete existing test if any
        PsychometricTest::where('slug', 'logical-reasoning')->delete();

        // Create Logical Reasoning Test
        $test = PsychometricTest::create([
            'name' => 'Tes Logika Penalaran',
            'slug' => 'logical-reasoning',
            'description' => 'Tes logika deret gambar. Perhatikan pola gambar yang diberikan dan tentukan gambar yang melengkapi urutan. Memerlukan konsentrasi penuh.',
            'type' => 'cognitive',
            'duration_minutes' => 20,
            'total_questions' => 20,
            'is_active' => true,
            'settings' => [
                'scoring_method' => 'correct_answers',
                'dimensions' => ['pattern', 'sequence', 'spatial', 'rotation'],
            ],
        ]);

        // Each question has: pattern SVG sequence + 4 answer choices
        // Using SVG patterns that can be rendered in browser
        $questions = [
            // 1. Shape rotation pattern
            [
                'pattern' => 'rotate_square',
                'description' => 'Kotak berputar 45° setiap langkah',
                'correct' => 'C',
                'dimension' => 'rotation',
            ],
            // 2. Fill pattern
            [
                'pattern' => 'fill_increase',
                'description' => 'Pengisian bertambah setiap langkah',
                'correct' => 'B',
                'dimension' => 'pattern',
            ],
            // 3. Circle count
            [
                'pattern' => 'circle_increase',
                'description' => 'Jumlah lingkaran bertambah',
                'correct' => 'D',
                'dimension' => 'sequence',
            ],
            // 4. Arrow direction
            [
                'pattern' => 'arrow_rotate',
                'description' => 'Panah berputar searah jarum jam',
                'correct' => 'A',
                'dimension' => 'rotation',
            ],
            // 5. Triangle flip
            [
                'pattern' => 'triangle_flip',
                'description' => 'Segitiga berbalik arah',
                'correct' => 'C',
                'dimension' => 'pattern',
            ],
            // 6. Dots pattern
            [
                'pattern' => 'dots_diagonal',
                'description' => 'Titik bergerak diagonal',
                'correct' => 'B',
                'dimension' => 'spatial',
            ],
            // 7. Size progression
            [
                'pattern' => 'size_grow',
                'description' => 'Ukuran membesar',
                'correct' => 'D',
                'dimension' => 'sequence',
            ],
            // 8. Line addition
            [
                'pattern' => 'line_add',
                'description' => 'Garis bertambah setiap langkah',
                'correct' => 'A',
                'dimension' => 'pattern',
            ],
            // 9. Shape alternation
            [
                'pattern' => 'shape_alternate',
                'description' => 'Bentuk bergantian',
                'correct' => 'C',
                'dimension' => 'sequence',
            ],
            // 10. Quarter fill
            [
                'pattern' => 'quarter_rotate',
                'description' => 'Kuartal terisi berputar',
                'correct' => 'B',
                'dimension' => 'rotation',
            ],
            // 11. Cross pattern
            [
                'pattern' => 'cross_extend',
                'description' => 'Silang memanjang',
                'correct' => 'A',
                'dimension' => 'pattern',
            ],
            // 12. Nested shapes
            [
                'pattern' => 'nested_shapes',
                'description' => 'Bentuk bersarang',
                'correct' => 'D',
                'dimension' => 'spatial',
            ],
            // 13. Grid fill
            [
                'pattern' => 'grid_fill',
                'description' => 'Kotak terisi bertambah',
                'correct' => 'C',
                'dimension' => 'sequence',
            ],
            // 14. Mirror pattern
            [
                'pattern' => 'mirror_line',
                'description' => 'Pola cermin',
                'correct' => 'B',
                'dimension' => 'spatial',
            ],
            // 15. Spiral
            [
                'pattern' => 'spiral_grow',
                'description' => 'Spiral membesar',
                'correct' => 'A',
                'dimension' => 'pattern',
            ],
            // 16. Domino dots
            [
                'pattern' => 'domino_pattern',
                'description' => 'Pola domino',
                'correct' => 'D',
                'dimension' => 'sequence',
            ],
            // 17. Pentagon rotation
            [
                'pattern' => 'pentagon_rotate',
                'description' => 'Pentagon berputar',
                'correct' => 'C',
                'dimension' => 'rotation',
            ],
            // 18. Bar chart
            [
                'pattern' => 'bar_increase',
                'description' => 'Batang meningkat',
                'correct' => 'B',
                'dimension' => 'sequence',
            ],
            // 19. Tile pattern
            [
                'pattern' => 'tile_checker',
                'description' => 'Pola papan catur',
                'correct' => 'A',
                'dimension' => 'pattern',
            ],
            // 20. Complex shape
            [
                'pattern' => 'complex_combine',
                'description' => 'Kombinasi bentuk',
                'correct' => 'D',
                'dimension' => 'spatial',
            ],
        ];

        foreach ($questions as $index => $q) {
            PsychometricQuestion::create([
                'test_id' => $test->id,
                'question_text' => 'Perhatikan pola gambar berikut (' . $q['description'] . '). Pilih gambar yang melengkapi urutan.',
                'question_type' => 'multiple_choice',
                'options' => [
                    ['value' => 'A', 'label' => 'Opsi A', 'correct' => $q['correct'] === 'A'],
                    ['value' => 'B', 'label' => 'Opsi B', 'correct' => $q['correct'] === 'B'],
                    ['value' => 'C', 'label' => 'Opsi C', 'correct' => $q['correct'] === 'C'],
                    ['value' => 'D', 'label' => 'Opsi D', 'correct' => $q['correct'] === 'D'],
                ],
                'dimension' => $q['dimension'],
                'order' => $index + 1,
                'is_active' => true,
            ]);
        }

        $this->command->info('Logical Reasoning Test with 20 visual pattern questions created!');
    }
}
