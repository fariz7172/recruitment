@extends('layouts.app')

@section('title', 'Lamaran Masuk - ScreeningAI')

@section('content')
<div class="flex justify-between items-center mb-6 flex-wrap gap-4">
    <div>
        <h1 class="mb-2">Lamaran Masuk</h1>
        <p class="text-muted">Kelola semua lamaran yang masuk</p>
    </div>
    <a href="{{ route('applications.create') }}" class="btn btn-primary">
        <i data-lucide="upload" style="width:18px;height:18px"></i>
        Input Lamaran
    </a>
</div>

@if(auth()->user()->role === 'admin')
<!-- Filters -->
<div class="card mb-6">
    <div class="card-body">
        <form action="{{ route('applications.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="form-group mb-0" style="flex: 1; min-width: 200px;">
                <label class="form-label">Cari</label>
                <input type="text" name="search" class="form-control" placeholder="Nama atau email pelamar..." value="{{ request('search') }}">
            </div>
            <div class="form-group mb-0" style="min-width: 180px;">
                <label class="form-label">Lowongan</label>
                <select name="job_id" class="form-control form-select">
                    <option value="">Semua Lowongan</option>
                    @foreach($jobs as $job)
                    <option value="{{ $job->id }}" {{ request('job_id') == $job->id ? 'selected' : '' }}>{{ $job->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-0" style="min-width: 150px;">
                <label class="form-label">Status</label>
                <select name="status" class="form-control form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="screening" {{ request('status') == 'screening' ? 'selected' : '' }}>Screening</option>
                    <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Ditinjau</option>
                    <option value="shortlisted" {{ request('status') == 'shortlisted' ? 'selected' : '' }}>Lolos</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="hired" {{ request('status') == 'hired' ? 'selected' : '' }}>Diterima</option>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">
                <i data-lucide="search" style="width:16px;height:16px"></i>
                Filter
            </button>
            @if(request()->hasAny(['search', 'job_id', 'status']))
            <a href="{{ route('applications.index') }}" class="btn btn-secondary">
                <i data-lucide="x" style="width:16px;height:16px"></i>
                Reset
            </a>
            @endif
        </form>
    </div>
</div>
@endif

<!-- Applications Table -->
<div class="card">
    @if($applications->count() > 0)
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Pelamar</th>
                    <th>Posisi</th>
                    <th>Skor AI</th>
                    <th>Rekomendasi</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($applications as $application)
                <tr>
                    <td>
                        <div class="font-semibold">{{ $application->applicant_name }}</div>
                        <div class="text-sm text-muted">{{ $application->email }}</div>
                    </td>
                    <td>
                        <a href="{{ route('jobs.show', $application->job) }}" class="font-medium">
                            {{ $application->job->title ?? '-' }}
                        </a>
                    </td>
                    <td>
                        @if($application->screeningResult)
                        <div class="flex items-center gap-2">
                            <div class="progress" style="width: 60px;">
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
                        @if($application->screeningResult)
                        <span class="badge badge-{{ $application->screeningResult->recommendation_color }}">
                            {{ $application->screeningResult->recommendation_label }}
                        </span>
                        @else
                        <span class="text-muted text-sm">Belum dianalisis</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $application->status_color }}">
                            {{ $application->status_label }}
                        </span>
                    </td>
                    <td class="text-sm text-muted">
                        {{ $application->created_at->format('d M Y') }}
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('applications.show', $application) }}" class="btn btn-sm btn-secondary" title="Lihat Detail">
                                <i data-lucide="eye" style="width:14px;height:14px"></i>
                            </a>
                            
                            @if(auth()->user()->role === 'admin' && !$application->screeningResult && $application->status === 'pending')
                            <form action="{{ route('applications.screen', $application) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary" title="Jalankan AI Screening">
                                    <i data-lucide="brain" style="width:14px;height:14px"></i>
                                </button>
                            </form>
                            @endif

                             <form action="{{ route('applications.destroy', $application) }}" method="POST" class="delete-form" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger btn-delete" title="Hapus">
                                    <i data-lucide="trash-2" style="width:14px;height:14px"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="card-footer">
        {{ $applications->withQueryString()->links() }}
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon">
            <i data-lucide="file-text"></i>
        </div>
        <h4 class="empty-state-title">Belum Ada Lamaran</h4>
        <p class="empty-state-text">Lamaran yang masuk akan muncul di sini</p>
        <a href="{{ route('applications.create') }}" class="btn btn-primary">
            <i data-lucide="upload"></i>
            Input Lamaran Manual
        </a>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        // Delete Confirmation
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Apakah Anda yakin ingin menghapus lamaran ini? Data yang dihapus tidak dapat dikembalikan.')) {
                    this.closest('form').submit();
                }
            });
        });
    });
</script>
@endpush
