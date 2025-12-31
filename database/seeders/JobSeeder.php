<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user if not exists
        $user = User::firstOrCreate(
            ['email' => 'admin@screening.test'],
            [
                'name' => 'Admin HR',
                'password' => Hash::make('password'),
            ]
        );

        // Sample Jobs
        $jobs = [
            [
                'title' => 'Senior Software Engineer',
                'description' => "Kami mencari Senior Software Engineer yang berpengalaman untuk bergabung dengan tim teknologi kami.\n\nTanggung Jawab:\n- Mengembangkan dan memelihara aplikasi backend menggunakan Laravel\n- Melakukan code review dan mentoring tim junior\n- Berkolaborasi dengan tim product dan design\n- Mengoptimalkan performa aplikasi\n\nBenefit:\n- Gaji kompetitif\n- BPJS Kesehatan & Ketenagakerjaan\n- Remote-friendly\n- Professional development budget",
                'department' => 'IT Development',
                'location' => 'Jakarta / Remote',
                'employment_type' => 'full-time',
                'min_education' => 'S1',
                'min_experience_years' => 3,
                'required_skills' => ['PHP', 'Laravel', 'MySQL', 'REST API', 'Git'],
                'preferred_skills' => ['Docker', 'Redis', 'Vue.js', 'AWS'],
                'salary_min' => 15000000,
                'salary_max' => 25000000,
                'deadline' => now()->addDays(30),
                'status' => 'active',
            ],
            [
                'title' => 'UI/UX Designer',
                'description' => "Kami mencari UI/UX Designer kreatif untuk mendesain pengalaman pengguna yang luar biasa.\n\nTanggung Jawab:\n- Membuat wireframe, mockup, dan prototype\n- Melakukan user research dan usability testing\n- Berkolaborasi dengan developer untuk implementasi\n- Menjaga konsistensi design system\n\nKualifikasi:\n- Mahir menggunakan Figma\n- Memahami prinsip-prinsip UX\n- Portfolio yang kuat",
                'department' => 'Product Design',
                'location' => 'Jakarta',
                'employment_type' => 'full-time',
                'min_education' => 'S1',
                'min_experience_years' => 2,
                'required_skills' => ['Figma', 'UI Design', 'UX Research', 'Prototyping', 'Design System'],
                'preferred_skills' => ['Adobe XD', 'Sketch', 'Motion Design', 'HTML/CSS'],
                'salary_min' => 10000000,
                'salary_max' => 18000000,
                'deadline' => now()->addDays(21),
                'status' => 'active',
            ],
            [
                'title' => 'Digital Marketing Specialist',
                'description' => "Bergabunglah dengan tim marketing kami untuk mengembangkan strategi digital yang efektif.\n\nTanggung Jawab:\n- Mengelola kampanye iklan digital (Google Ads, Meta Ads)\n- Menganalisis performa dan membuat laporan\n- Mengoptimalkan konversi dan ROI\n- Berkolaborasi dengan tim content",
                'department' => 'Marketing',
                'location' => 'Jakarta',
                'employment_type' => 'full-time',
                'min_education' => 'S1',
                'min_experience_years' => 2,
                'required_skills' => ['Google Ads', 'Meta Ads', 'Google Analytics', 'SEO', 'Data Analysis'],
                'preferred_skills' => ['Email Marketing', 'CRM', 'A/B Testing'],
                'salary_min' => 8000000,
                'salary_max' => 15000000,
                'deadline' => now()->addDays(14),
                'status' => 'active',
            ],
            [
                'title' => 'HR Recruitment Intern',
                'description' => "Kesempatan magang untuk mahasiswa yang tertarik dengan bidang HR dan rekrutmen.\n\nYang akan dipelajari:\n- Proses rekrutmen end-to-end\n- Screening CV dan interview\n- Penggunaan HRIS\n- Employer branding\n\nDurasi: 3-6 bulan\nKompensasi: Allowance + Sertifikat",
                'department' => 'Human Resources',
                'location' => 'Jakarta',
                'employment_type' => 'internship',
                'min_education' => 'SMA',
                'min_experience_years' => 0,
                'required_skills' => ['Microsoft Office', 'Communication', 'Attention to Detail'],
                'preferred_skills' => ['HRIS', 'Psychology Background'],
                'salary_min' => 2000000,
                'salary_max' => 3000000,
                'deadline' => now()->addDays(7),
                'status' => 'active',
            ],
            [
                'title' => 'Data Analyst',
                'description' => "Kami mencari Data Analyst untuk mengubah data menjadi insight yang actionable.\n\nTanggung Jawab:\n- Menganalisis data bisnis dan membuat dashboard\n- Membuat laporan berkala untuk stakeholder\n- Mengidentifikasi trend dan peluang\n- Berkolaborasi dengan berbagai tim",
                'department' => 'Business Intelligence',
                'location' => 'Jakarta / Hybrid',
                'employment_type' => 'full-time',
                'min_education' => 'S1',
                'min_experience_years' => 1,
                'required_skills' => ['SQL', 'Python', 'Data Visualization', 'Excel', 'Statistics'],
                'preferred_skills' => ['Tableau', 'Power BI', 'Machine Learning'],
                'salary_min' => 10000000,
                'salary_max' => 18000000,
                'deadline' => now()->addDays(28),
                'status' => 'active',
            ],
        ];

        foreach ($jobs as $jobData) {
            Job::create(array_merge($jobData, ['created_by' => $user->id]));
        }

        $this->command->info('Created ' . count($jobs) . ' sample jobs!');
    }
}
