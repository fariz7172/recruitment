@extends('layouts.app')

@section('title', 'Lowongan Kerja - ScreeningAI')

@section('content')
<section class="py-8 px-4">
    <div class="container">
        <div class="text-center mb-8">
            <h1 class="mb-2">Lowongan Kerja</h1>
            <p class="text-muted text-lg">Temukan karir impian Anda</p>
        </div>

        @if($jobs->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($jobs as $job)
            <div class="job-card animate-fadeIn">
                <div class="flex justify-between items-start mb-3">
                    <h3 class="job-card-title">{{ $job->title }}</h3>
                    <span class="badge badge-{{ $job->employment_type == 'full-time' ? 'success' : 'info' }}">
                        {{ ucfirst(str_replace('-', ' ', $job->employment_type)) }}
                    </span>
                </div>
                
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
                </div>

                <p class="text-muted text-sm mb-4">{{ Str::limit($job->description, 120) }}</p>

                <div class="mb-4">
                    <div class="text-sm text-muted mb-2">Kualifikasi:</div>
                    <div class="flex flex-wrap gap-2 text-sm">
                        <span class="flex items-center gap-1">
                            <i data-lucide="graduation-cap" style="width:14px;height:14px"></i>
                            {{ $job->min_education }}
                        </span>
                        <span class="flex items-center gap-1">
                            <i data-lucide="clock" style="width:14px;height:14px"></i>
                            {{ $job->min_experience_years }}+ tahun
                        </span>
                    </div>
                </div>
                
                <div class="job-card-skills">
                    @foreach(array_slice($job->required_skills ?? [], 0, 4) as $skill)
                    <span class="skill-tag">{{ $skill }}</span>
                    @endforeach
                    @if(count($job->required_skills ?? []) > 4)
                    <span class="skill-tag" style="background: var(--color-gray-200);">+{{ count($job->required_skills) - 4 }}</span>
                    @endif
                </div>

                @if($job->salary_range)
                <div class="mt-4 text-sm font-semibold" style="color: var(--color-success-dark);">
                    <i data-lucide="banknote" style="width:14px;height:14px;display:inline"></i>
                    {{ $job->salary_range }}
                </div>
                @endif

                @if($job->deadline)
                <div class="mt-2 text-sm text-muted">
                    <i data-lucide="calendar" style="width:14px;height:14px;display:inline"></i>
                    Deadline: {{ $job->deadline->format('d M Y') }}
                </div>
                @endif

                <div class="mt-4 pt-4" style="border-top: 1px solid var(--color-gray-200);">
                    <a href="{{ route('apply.create', $job) }}" class="btn btn-primary" style="width: 100%;">
                        <i data-lucide="send" style="width:16px;height:16px"></i>
                        Lamar Sekarang
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $jobs->links() }}
        </div>
        @else
        <div class="card">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i data-lucide="briefcase"></i>
                </div>
                <h4 class="empty-state-title">Belum Ada Lowongan</h4>
                <p class="empty-state-text">Lowongan baru akan segera tersedia. Kunjungi lagi nanti!</p>
                <a href="{{ route('landing') }}" class="btn btn-primary">
                    <i data-lucide="home"></i>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
