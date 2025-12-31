@extends('layouts.app')

@section('title', $job->title . ' - ScreeningAI')

@section('content')
<div class="mb-6">
    <a href="{{ route('jobs.index') }}" class="btn btn-sm btn-secondary mb-4">
        <i data-lucide="arrow-left" style="width:16px;height:16px"></i>
        Kembali
    </a>
    
    <div class="flex justify-between items-start flex-wrap gap-4">
        <div>
            <h1 class="mb-2">{{ $job->title }}</h1>
            <div class="flex flex-wrap gap-4 text-muted">
                @if($job->department)
                <span class="flex items-center gap-2">
                    <i data-lucide="building" style="width:16px;height:16px"></i>
                    {{ $job->department }}
                </span>
                @endif
                @if($job->location)
                <span class="flex items-center gap-2">
                    <i data-lucide="map-pin" style="width:16px;height:16px"></i>
                    {{ $job->location }}
                </span>
                @endif
                <span class="flex items-center gap-2">
                    <i data-lucide="clock" style="width:16px;height:16px"></i>
                    {{ ucfirst(str_replace('-', ' ', $job->employment_type)) }}
                </span>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('jobs.edit', $job) }}" class="btn btn-secondary">
                <i data-lucide="pencil" style="width:16px;height:16px"></i>
                Edit
            </a>
            @php
                $statusColors = ['active' => 'success', 'closed' => 'danger', 'draft' => 'warning'];
                $statusLabels = ['active' => 'Aktif', 'closed' => 'Ditutup', 'draft' => 'Draft'];
            @endphp
            <span class="badge badge-{{ $statusColors[$job->status] ?? 'secondary' }}" style="font-size: 14px; padding: 8px 16px;">
                {{ $statusLabels[$job->status] ?? $job->status }}
            </span>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-card-value">{{ $applicationStats['total'] }}</div>
        <div class="stat-card-label">Total Lamaran</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-value">{{ $applicationStats['pending'] }}</div>
        <div class="stat-card-label">Menunggu</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-value">{{ $applicationStats['shortlisted'] }}</div>
        <div class="stat-card-label">Lolos</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-value">{{ $applicationStats['rejected'] }}</div>
        <div class="stat-card-label">Ditolak</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2">
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="card-title">Deskripsi Pekerjaan</h3>
            </div>
            <div class="card-body">
                <div style="white-space: pre-wrap;">{{ $job->description }}</div>
            </div>
        </div>

        <!-- Applications List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i data-lucide="users" style="width:20px;height:20px"></i>
                    Lamaran Masuk
                </h3>
                <a href="{{ route('applications.create', ['job_id' => $job->id]) }}" class="btn btn-sm btn-primary">
                    <i data-lucide="plus" style="width:14px;height:14px"></i>
                    Input Lamaran
                </a>
            </div>
            @if($job->applications->count() > 0)
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Pelamar</th>
                            <th>Skor AI</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($job->applications->sortByDesc('created_at') as $application)
                        <tr>
                            <td>
                                <div class="font-semibold">{{ $application->applicant_name }}</div>
                                <div class="text-sm text-muted">{{ $application->email }}</div>
                            </td>
                            <td>
                                @if($application->screeningResult)
                                <div class="flex items-center gap-2">
                                    <div class="progress" style="width: 60px;">
                                        <div class="progress-bar {{ $application->screeningResult->is_high_score ? 'high' : ($application->screeningResult->is_medium_score ? 'medium' : 'low') }}" 
                                             style="width: {{ $application->screeningResult->score }}%"></div>
                                    </div>
                                    <span class="font-semibold text-sm">{{ $application->screeningResult->score }}%</span>
                                </div>
                                @else
                                <span class="text-muted text-sm">Belum</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $application->status_color }}">
                                    {{ $application->status_label }}
                                </span>
                            </td>
                            <td class="text-sm text-muted">
                                {{ $application->created_at->format('d M') }}
                            </td>
                            <td>
                                <a href="{{ route('applications.show', $application) }}" class="btn btn-sm btn-secondary">
                                    <i data-lucide="eye" style="width:14px;height:14px"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state" style="padding: 48px;">
                <div class="empty-state-icon">
                    <i data-lucide="inbox"></i>
                </div>
                <h4 class="empty-state-title">Belum Ada Lamaran</h4>
                <p class="empty-state-text">Bagikan link lowongan untuk menerima lamaran</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="card-title">Kualifikasi</h3>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="text-sm text-muted mb-1">Pendidikan Minimal</div>
                    <div class="font-semibold">{{ $job->min_education }}</div>
                </div>
                <div class="mb-4">
                    <div class="text-sm text-muted mb-1">Pengalaman Minimal</div>
                    <div class="font-semibold">{{ $job->min_experience_years }} tahun</div>
                </div>
                <div class="mb-4">
                    <div class="text-sm text-muted mb-2">Skills Dibutuhkan</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($job->required_skills ?? [] as $skill)
                        <span class="skill-tag">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
                @if($job->preferred_skills && count($job->preferred_skills) > 0)
                <div>
                    <div class="text-sm text-muted mb-2">Skills Tambahan</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($job->preferred_skills as $skill)
                        <span class="badge badge-secondary">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if($job->salary_range)
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="card-title">Gaji</h3>
            </div>
            <div class="card-body">
                <div class="font-semibold text-lg">{{ $job->salary_range }}</div>
            </div>
        </div>
        @endif

        <div class="card mb-6">
            <div class="card-header">
                <h3 class="card-title">Informasi</h3>
            </div>
            <div class="card-body">
                @if($job->deadline)
                <div class="mb-3">
                    <div class="text-sm text-muted">Deadline</div>
                    <div class="font-semibold">{{ $job->deadline->format('d M Y') }}</div>
                </div>
                @endif
                <div class="mb-3">
                    <div class="text-sm text-muted">Dibuat</div>
                    <div class="font-semibold">{{ $job->created_at->format('d M Y') }}</div>
                </div>
                <div>
                    <div class="text-sm text-muted">Oleh</div>
                    <div class="font-semibold">{{ $job->creator->name ?? 'Admin' }}</div>
                </div>
            </div>
        </div>

        @if($job->is_open)
        <div class="card" style="background: var(--color-secondary-100);">
            <div class="card-body text-center">
                <div class="text-sm text-muted mb-2">Link Lamaran Publik</div>
                <input type="text" class="form-control mb-3" value="{{ route('apply.create', $job) }}" readonly id="publicLink">
                <button class="btn btn-primary" style="width: 100%;" onclick="copyLink()">
                    <i data-lucide="copy" style="width:16px;height:16px"></i>
                    Salin Link
                </button>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });

    function copyLink() {
        const input = document.getElementById('publicLink');
        input.select();
        document.execCommand('copy');
        alert('Link berhasil disalin!');
    }
</script>
@endpush
