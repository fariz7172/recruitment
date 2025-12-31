@extends('layouts.app')

@section('title', 'Tes Psikotes - ' . $application->applicant_name)

@section('content')
<div class="mb-6">
    <a href="{{ route('applications.show', $application) }}" class="btn btn-sm btn-secondary">
        <i data-lucide="arrow-left" style="width:16px;height:16px"></i>
        Kembali ke Detail Lamaran
    </a>
</div>

<div class="card mb-6" style="background: linear-gradient(135deg, var(--color-primary-100) 0%, var(--color-accent-100) 100%);">
    <div class="card-body">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center" style="width:60px;height:60px;border-radius:50%;background:var(--color-primary-200);">
                <i data-lucide="brain" style="width:30px;height:30px;color:var(--color-primary-600)"></i>
            </div>
            <div>
                <h2 class="mb-1">Tes Psikotes</h2>
                <p class="text-muted">{{ $application->applicant_name }} - {{ $application->job->title ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($tests as $test)
    <div class="card">
        <div class="card-body">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex items-center justify-center" style="width:48px;height:48px;border-radius:12px;background:var(--color-secondary-100);">
                    <i data-lucide="clipboard-check" style="width:24px;height:24px;color:var(--color-secondary-600)"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $test->name }}</h3>
                    <span class="text-sm text-muted">{{ $test->formatted_duration }}</span>
                </div>
            </div>

            <p class="text-sm text-muted mb-4">{{ $test->description }}</p>

            <div class="flex flex-wrap gap-2 mb-4 text-sm">
                <span class="badge badge-info">{{ $test->total_questions }} Soal</span>
                <span class="badge badge-secondary">{{ ucfirst($test->type) }}</span>
            </div>

            @if(in_array($test->id, $completedTests))
            <div class="flex gap-2">
                <a href="{{ route('psychometric.result', [$application, $test]) }}" class="btn btn-success" style="flex:1;">
                    <i data-lucide="check-circle" style="width:16px;height:16px"></i>
                    Lihat Hasil
                </a>
            </div>
            @else
            <a href="{{ route('psychometric.start', [$application, $test]) }}" class="btn btn-primary" style="width:100%;">
                <i data-lucide="play" style="width:16px;height:16px"></i>
                Mulai Tes
            </a>
            @endif
        </div>
    </div>
    @endforeach
</div>

@if($tests->isEmpty())
<div class="card">
    <div class="empty-state">
        <div class="empty-state-icon">
            <i data-lucide="file-question"></i>
        </div>
        <h4 class="empty-state-title">Belum Ada Tes</h4>
        <p class="empty-state-text">Tidak ada tes psikotes yang tersedia saat ini.</p>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
