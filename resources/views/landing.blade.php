@extends('layouts.app')

@section('title', 'ScreeningAI - Sistem Rekrutmen Berbasis AI')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content animate-fadeIn">
            <h1 class="hero-title">
                Rekrutmen Cerdas dengan Kekuatan AI
            </h1>
            <p class="hero-subtitle">
                Sistem screening otomatis yang menganalisis CV pelamar menggunakan teknologi AI canggih. 
                Hemat waktu, tingkatkan akurasi, dan temukan kandidat terbaik.
            </p>
            <div class="flex gap-4 flex-wrap">
                <a href="{{ route('jobs.public') }}" class="btn btn-primary btn-lg">
                    <i data-lucide="briefcase" style="width:20px;height:20px"></i>
                    Lihat Lowongan
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-lg">
                    <i data-lucide="layout-dashboard" style="width:20px;height:20px"></i>
                    Dashboard Admin
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16 px-4">
    <div class="container">
        <div class="text-center mb-12">
            <h2 class="mb-4">Fitur Unggulan</h2>
            <p class="text-muted text-lg">Teknologi terdepan untuk proses rekrutmen modern</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Feature 1 -->
            <div class="card animate-fadeIn delay-100">
                <div class="card-body text-center">
                    <div class="stat-card-icon primary mx-auto mb-4">
                        <i data-lucide="scan"></i>
                    </div>
                    <h4 class="mb-2">OCR Otomatis</h4>
                    <p class="text-muted">
                        Upload CV dalam format PDF atau gambar, sistem akan membaca dan mengekstrak informasi secara otomatis.
                    </p>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="card animate-fadeIn delay-200">
                <div class="card-body text-center">
                    <div class="stat-card-icon secondary mx-auto mb-4">
                        <i data-lucide="brain"></i>
                    </div>
                    <h4 class="mb-2">Analisis AI (Gemini)</h4>
                    <p class="text-muted">
                        Powered by Google Gemini AI untuk menganalisis kesesuaian kandidat dengan kualifikasi yang ditentukan.
                    </p>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="card animate-fadeIn delay-300">
                <div class="card-body text-center">
                    <div class="stat-card-icon accent mx-auto mb-4">
                        <i data-lucide="gauge"></i>
                    </div>
                    <h4 class="mb-2">Skor Kesesuaian</h4>
                    <p class="text-muted">
                        Setiap kandidat mendapat skor 0-100 berdasarkan kecocokan skill, pengalaman, dan pendidikan.
                    </p>
                </div>
            </div>

            <!-- Feature 4 -->
            <div class="card animate-fadeIn delay-100">
                <div class="card-body text-center">
                    <div class="stat-card-icon info mx-auto mb-4">
                        <i data-lucide="zap"></i>
                    </div>
                    <h4 class="mb-2">Proses Cepat</h4>
                    <p class="text-muted">
                        Screening yang biasanya memakan waktu berjam-jam, kini selesai dalam hitungan detik.
                    </p>
                </div>
            </div>

            <!-- Feature 5 -->
            <div class="card animate-fadeIn delay-200">
                <div class="card-body text-center">
                    <div class="stat-card-icon primary mx-auto mb-4">
                        <i data-lucide="shield-check"></i>
                    </div>
                    <h4 class="mb-2">Objektif & Konsisten</h4>
                    <p class="text-muted">
                        AI memberikan penilaian yang konsisten tanpa bias, memastikan semua kandidat dinilai adil.
                    </p>
                </div>
            </div>

            <!-- Feature 6 -->
            <div class="card animate-fadeIn delay-300">
                <div class="card-body text-center">
                    <div class="stat-card-icon secondary mx-auto mb-4">
                        <i data-lucide="smartphone"></i>
                    </div>
                    <h4 class="mb-2">Responsive Design</h4>
                    <p class="text-muted">
                        Akses dari desktop, tablet, atau smartphone dengan tampilan yang optimal di semua perangkat.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Latest Jobs Section -->
@if($jobs->count() > 0)
<section class="py-16 px-4" style="background: var(--color-secondary-50);">
    <div class="container">
        <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
            <div>
                <h2 class="mb-2">Lowongan Terbaru</h2>
                <p class="text-muted">Posisi yang sedang dibuka</p>
            </div>
            <a href="{{ route('jobs.public') }}" class="btn btn-secondary">
                Lihat Semua
                <i data-lucide="arrow-right" style="width:18px;height:18px"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($jobs as $job)
            <div class="job-card animate-fadeIn">
                <h3 class="job-card-title">{{ $job->title }}</h3>
                <div class="job-card-meta">
                    @if($job->department)
                    <span class="job-card-meta-item">
                        <i data-lucide="building" style="width:16px;height:16px"></i>
                        {{ $job->department }}
                    </span>
                    @endif
                    @if($job->location)
                    <span class="job-card-meta-item">
                        <i data-lucide="map-pin" style="width:16px;height:16px"></i>
                        {{ $job->location }}
                    </span>
                    @endif
                    <span class="job-card-meta-item">
                        <i data-lucide="clock" style="width:16px;height:16px"></i>
                        {{ ucfirst(str_replace('-', ' ', $job->employment_type)) }}
                    </span>
                </div>
                <p class="text-muted text-sm mb-4">{{ Str::limit($job->description, 100) }}</p>
                
                <div class="job-card-skills">
                    @foreach(array_slice($job->required_skills ?? [], 0, 3) as $skill)
                    <span class="skill-tag">{{ $skill }}</span>
                    @endforeach
                    @if(count($job->required_skills ?? []) > 3)
                    <span class="skill-tag">+{{ count($job->required_skills) - 3 }}</span>
                    @endif
                </div>

                <div class="mt-4">
                    <a href="{{ route('apply.create', $job) }}" class="btn btn-primary btn-sm">
                        <i data-lucide="send" style="width:14px;height:14px"></i>
                        Lamar Sekarang
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="py-16 px-4">
    <div class="container">
        <div class="card" style="background: linear-gradient(135deg, var(--color-primary-200) 0%, var(--color-secondary-200) 100%);">
            <div class="card-body text-center p-8">
                <h2 class="mb-4">Siap Untuk Memulai?</h2>
                <p class="text-lg text-muted mb-6 max-w-2xl mx-auto">
                    Tingkatkan efisiensi proses rekrutmen Anda dengan teknologi AI. 
                    Mulai screening kandidat secara otomatis sekarang juga.
                </p>
                <div class="flex justify-center gap-4 flex-wrap">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                        <i data-lucide="rocket" style="width:20px;height:20px"></i>
                        Mulai Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="py-8 px-4" style="background: var(--color-gray-100);">
    <div class="container text-center">
        <p class="text-muted">
            © {{ date('Y') }} ScreeningAI - Sistem Rekrutmen Berbasis AI
        </p>
        <p class="text-sm text-muted mt-2">
           Fariz Ahmad
        </p>
    </div>
</footer>
@endsection

@push('scripts')
<script>
    // Animate on scroll
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-fadeIn').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s ease';
            observer.observe(el);
        });
    });
</script>
@endpush
