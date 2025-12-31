<?php

namespace Database\Seeders;

use App\Models\PsychometricTest;
use App\Models\PsychometricQuestion;
use Illuminate\Database\Seeder;

class WarteggTestSeeder extends Seeder
{
    public function run(): void
    {
        // Delete existing Wartegg test if any
        PsychometricTest::where('slug', 'wartegg')->delete();

        // Create Wartegg Test
        $test = PsychometricTest::create([
            'name' => 'Tes Wartegg',
            'slug' => 'wartegg',
            'description' => 'Tes proyeksi gambar Wartegg. Lihat gambar stimulus dan pilih apa yang Anda "lihat" atau interpretasikan dari gambar tersebut.',
            'type' => 'personality',
            'duration_minutes' => 15,
            'total_questions' => 8,
            'is_active' => true,
            'settings' => [
                'scoring_method' => 'sum',
                'dimensions' => ['self_concept', 'emotion', 'ambition', 'anxiety', 'energy', 'integration', 'sensitivity', 'social'],
                'has_images' => true,
            ],
        ]);

        // 8 Wartegg boxes with interpretations
        $questions = [
            // Box 1 - Titik (Self-concept/Ego)
            [
                'question' => 'wartegg_box_1',
                'dimension' => 'self_concept',
                'description' => 'Kotak 1: Apa yang Anda lihat dari TITIK di tengah kotak ini?',
                'options' => [
                    ['value' => 5, 'label' => 'Matahari / Pusat alam semesta'],
                    ['value' => 4, 'label' => 'Bunga / Tanaman yang tumbuh'],
                    ['value' => 3, 'label' => 'Wajah / Kepala orang'],
                    ['value' => 2, 'label' => 'Bola / Lingkaran'],
                    ['value' => 1, 'label' => 'Hanya titik biasa'],
                ],
            ],
            // Box 2 - Gelombang (Emotion/Flexibility)
            [
                'question' => 'wartegg_box_2',
                'dimension' => 'emotion',
                'description' => 'Kotak 2: Apa yang Anda lihat dari GARIS LENGKUNG kecil ini?',
                'options' => [
                    ['value' => 5, 'label' => 'Burung terbang / Sayap'],
                    ['value' => 4, 'label' => 'Ombak laut / Air'],
                    ['value' => 3, 'label' => 'Awan / Asap'],
                    ['value' => 2, 'label' => 'Rambut / Bulu'],
                    ['value' => 1, 'label' => 'Hanya garis lengkung'],
                ],
            ],
            // Box 3 - Tiga garis vertikal (Ambition/Growth)
            [
                'question' => 'wartegg_box_3',
                'dimension' => 'ambition',
                'description' => 'Kotak 3: Apa yang Anda lihat dari TIGA GARIS VERTIKAL yang meninggi?',
                'options' => [
                    ['value' => 5, 'label' => 'Gedung tinggi / Menara'],
                    ['value' => 4, 'label' => 'Tangga naik / Grafik pertumbuhan'],
                    ['value' => 3, 'label' => 'Pohon / Tanaman'],
                    ['value' => 2, 'label' => 'Pagar / Tiang'],
                    ['value' => 1, 'label' => 'Hanya garis-garis'],
                ],
            ],
            // Box 4 - Kotak hitam (Anxiety/Security)
            [
                'question' => 'wartegg_box_4',
                'dimension' => 'anxiety',
                'description' => 'Kotak 4: Apa yang Anda lihat dari KOTAK HITAM KECIL di sudut?',
                'options' => [
                    ['value' => 5, 'label' => 'Jendela rumah / Pintu'],
                    ['value' => 4, 'label' => 'Papan catur / Permainan'],
                    ['value' => 3, 'label' => 'Layar TV / Komputer'],
                    ['value' => 2, 'label' => 'Kotak / Kubus'],
                    ['value' => 1, 'label' => 'Kegelapan / Lubang'],
                ],
            ],
            // Box 5 - Dua garis berlawanan (Energy/Drive)
            [
                'question' => 'wartegg_box_5',
                'dimension' => 'energy',
                'description' => 'Kotak 5: Apa yang Anda lihat dari DUA GARIS yang saling berlawanan arah?',
                'options' => [
                    ['value' => 5, 'label' => 'Roket / Pesawat terbang'],
                    ['value' => 4, 'label' => 'Layang-layang / Panah'],
                    ['value' => 3, 'label' => 'Burung / Capung'],
                    ['value' => 2, 'label' => 'Bendera / Tanda'],
                    ['value' => 1, 'label' => 'Hanya garis silang'],
                ],
            ],
            // Box 6 - Garis horizontal dan vertikal (Integration)
            [
                'question' => 'wartegg_box_6',
                'dimension' => 'integration',
                'description' => 'Kotak 6: Apa yang Anda lihat dari GARIS HORIZONTAL dan VERTIKAL yang terpisah?',
                'options' => [
                    ['value' => 5, 'label' => 'Rumah / Bangunan'],
                    ['value' => 4, 'label' => 'Meja / Kursi / Furnitur'],
                    ['value' => 3, 'label' => 'Jalan / Persimpangan'],
                    ['value' => 2, 'label' => 'Tanda plus / Salib'],
                    ['value' => 1, 'label' => 'Garis yang tidak berhubungan'],
                ],
            ],
            // Box 7 - Titik-titik (Sensitivity/Detail)
            [
                'question' => 'wartegg_box_7',
                'dimension' => 'sensitivity',
                'description' => 'Kotak 7: Apa yang Anda lihat dari POLA TITIK-TITIK ini?',
                'options' => [
                    ['value' => 5, 'label' => 'Bintang / Galaksi / Langit malam'],
                    ['value' => 4, 'label' => 'Bunga / Taman'],
                    ['value' => 3, 'label' => 'Wajah tersenyum / Ekspresi'],
                    ['value' => 2, 'label' => 'Hujan / Salju'],
                    ['value' => 1, 'label' => 'Hanya titik-titik acak'],
                ],
            ],
            // Box 8 - Lengkungan besar (Social/Protection)
            [
                'question' => 'wartegg_box_8',
                'dimension' => 'social',
                'description' => 'Kotak 8: Apa yang Anda lihat dari LENGKUNGAN BESAR ini?',
                'options' => [
                    ['value' => 5, 'label' => 'Pelangi / Langit cerah'],
                    ['value' => 4, 'label' => 'Payung / Pelindung'],
                    ['value' => 3, 'label' => 'Kubah / Atap bangunan'],
                    ['value' => 2, 'label' => 'Helm / Topi'],
                    ['value' => 1, 'label' => 'Hanya setengah lingkaran'],
                ],
            ],
        ];

        foreach ($questions as $index => $q) {
            PsychometricQuestion::create([
                'test_id' => $test->id,
                'question_text' => $q['description'],
                'question_type' => 'wartegg',
                'options' => $q['options'],
                'dimension' => $q['dimension'],
                'order' => $index + 1,
                'is_active' => true,
            ]);
        }

        $this->command->info('Wartegg Test with 8 visual interpretation questions created!');
    }
}
