@extends('layouts.app')

@section('title', 'Dashboard - ScreeningAI')

@section('content')
<div class="mb-6">
    <h1 class="mb-2">Dashboard</h1>
    <p class="text-muted">Selamat datang di Sistem Screening & Rekrutmen AI</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="stat-card animate-fadeIn">
        <div class="stat-card-icon primary">
            <i data-lucide="briefcase"></i>
        </div>
        <div class="stat-card-value">{{ $stats['total_jobs'] }}</div>
        <div class="stat-card-label">Total Lowongan</div>
    </div>

    <div class="stat-card animate-fadeIn delay-100">
        <div class="stat-card-icon secondary">
            <i data-lucide="file-text"></i>
        </div>
        <div class="stat-card-value">{{ $stats['total_applications'] }}</div>
        <div class="stat-card-label">Total Lamaran</div>
    </div>

    <div class="stat-card animate-fadeIn delay-200">
        <div class="stat-card-icon accent">
            <i data-lucide="clock"></i>
        </div>
        <div class="stat-card-value">{{ $stats['pending_applications'] }}</div>
        <div class="stat-card-label">Menunggu Screening</div>
    </div>

    <div class="stat-card animate-fadeIn delay-300">
        <div class="stat-card-icon info">
            <i data-lucide="check-circle"></i>
        </div>
        <div class="stat-card-value">{{ $stats['shortlisted'] }}</div>
        <div class="stat-card-label">Lolos Seleksi</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Chart Section -->
    <div class="lg:col-span-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i data-lucide="bar-chart-3" style="width:20px;height:20px"></i>
                    Lamaran Minggu Ini
                </h3>
            </div>
            <div class="card-body">
                <canvas id="weeklyChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i data-lucide="pie-chart" style="width:20px;height:20px"></i>
                Status Lamaran
            </h3>
        </div>
        <div class="card-body">
            <canvas id="statusChart" height="250"></canvas>
        </div>
    </div>
</div>

<!-- Recent Applications -->
<div class="card mt-6">
    <div class="card-header">
        <h3 class="card-title">
            <i data-lucide="users" style="width:20px;height:20px"></i>
            Lamaran Terbaru
        </h3>
        <a href="{{ route('applications.index') }}" class="btn btn-sm btn-secondary">
            Lihat Semua
        </a>
    </div>
    <div class="card-body p-0">
        @if($recentApplications->count() > 0)
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Pelamar</th>
                        <th>Posisi</th>
                        <th>Skor AI</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentApplications as $application)
                    <tr>
                        <td>
                            <div class="font-semibold">{{ $application->applicant_name }}</div>
                            <div class="text-sm text-muted">{{ $application->email }}</div>
                        </td>
                        <td>{{ $application->job->title ?? '-' }}</td>
                        <td>
                            @if($application->screeningResult)
                                <div class="flex items-center gap-2">
                                    <div class="progress" style="width: 80px;">
                                        <div class="progress-bar {{ $application->screeningResult->is_high_score ? 'high' : ($application->screeningResult->is_medium_score ? 'medium' : 'low') }}" 
                                             style="width: {{ $application->screeningResult->score }}%"></div>
                                    </div>
                                    <span class="font-semibold">{{ $application->screeningResult->score }}%</span>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $application->status_color }}">
                                {{ $application->status_label }}
                            </span>
                        </td>
                        <td class="text-muted text-sm">
                            {{ $application->created_at->format('d M Y') }}
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
        <div class="empty-state">
            <div class="empty-state-icon">
                <i data-lucide="inbox"></i>
            </div>
            <h4 class="empty-state-title">Belum Ada Lamaran</h4>
            <p class="empty-state-text">Lamaran yang masuk akan muncul di sini</p>
            <a href="{{ route('jobs.create') }}" class="btn btn-primary">
                <i data-lucide="plus"></i>
                Buat Lowongan Pertama
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();

    // Weekly Applications Chart
    const weeklyData = @json($weeklyApplications);
    const weeklyLabels = Object.keys(weeklyData).map(date => {
        const d = new Date(date);
        return d.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric' });
    });
    const weeklyValues = Object.values(weeklyData);

    new Chart(document.getElementById('weeklyChart'), {
        type: 'bar',
        data: {
            labels: weeklyLabels.length ? weeklyLabels : ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            datasets: [{
                label: 'Lamaran',
                data: weeklyValues.length ? weeklyValues : [0, 0, 0, 0, 0, 0, 0],
                backgroundColor: 'rgba(184, 174, 216, 0.8)',
                borderColor: 'rgba(155, 142, 200, 1)',
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // Status Distribution Chart
    const statusData = @json($applicationsByStatus);
    const statusLabels = {
        'pending': 'Menunggu',
        'screening': 'Screening',
        'reviewed': 'Ditinjau',
        'shortlisted': 'Lolos',
        'rejected': 'Ditolak',
        'hired': 'Diterima'
    };
    const statusColors = {
        'pending': '#FFE0B2',
        'screening': '#B3E5FC',
        'reviewed': '#E8E4F0',
        'shortlisted': '#A8E6CF',
        'rejected': '#FFCDD2',
        'hired': '#6BBD9F'
    };

    const labels = Object.keys(statusData).map(s => statusLabels[s] || s);
    const values = Object.values(statusData);
    const colors = Object.keys(statusData).map(s => statusColors[s] || '#EEEEEE');

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: labels.length ? labels : ['Tidak ada data'],
            datasets: [{
                data: values.length ? values : [1],
                backgroundColor: colors.length ? colors : ['#EEEEEE'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                    }
                }
            },
            cutout: '60%',
        }
    });
});
</script>
@endpush
