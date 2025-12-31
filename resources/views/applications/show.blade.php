@extends('layouts.app')

@section('title', $application->applicant_name . ' - Detail Lamaran')

@section('content')
<div class="mb-6">
    <a href="{{ route('applications.index') }}" class="btn btn-sm btn-secondary mb-4">
        <i data-lucide="arrow-left" style="width:16px;height:16px"></i>
        Kembali
    </a>
    
    <div class="flex justify-between items-start flex-wrap gap-4">
        <div>
            <h1 class="mb-2">{{ $application->applicant_name }}</h1>
            <div class="flex flex-wrap gap-4 text-muted">
                <span class="flex items-center gap-2">
                    <i data-lucide="mail" style="width:16px;height:16px"></i>
                    {{ $application->email }}
                </span>
                @if($application->phone)
                <span class="flex items-center gap-2">
                    <i data-lucide="phone" style="width:16px;height:16px"></i>
                    {{ $application->phone }}
                </span>
                @endif
            </div>
        </div>
        <div class="flex gap-2 flex-wrap">
            @if(!$application->screeningResult)
            <form action="{{ route('applications.screen', $application) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="brain" style="width:16px;height:16px"></i>
                    Jalankan AI Screening
                </button>
            </form>
            @endif
            <span class="badge badge-{{ $application->status_color }}" style="font-size: 14px; padding: 8px 16px;">
                {{ $application->status_label }}
            </span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2">
        @if($application->screeningResult)
        <!-- AI Screening Result -->
        <div class="card mb-6" style="border-top: 4px solid var(--color-{{ $application->screeningResult->recommendation_color }});">
            <div class="card-header">
                <h3 class="card-title">
                    <i data-lucide="brain" style="width:20px;height:20px"></i>
                    Hasil AI Screening
                </h3>
                <span class="badge badge-{{ $application->screeningResult->recommendation_color }}" style="font-size: 14px;">
                    {{ $application->screeningResult->recommendation_label }}
                </span>
            </div>
            <div class="card-body">
                <!-- Score -->
                <div class="flex items-center gap-6 mb-6 flex-wrap">
                    <div class="text-center">
                        <div class="score-circle" style="--score: {{ $application->screeningResult->score }}; --score-color: var(--color-{{ $application->screeningResult->recommendation_color }});">
                            {{ $application->screeningResult->score }}
                        </div>
                        <div class="text-sm text-muted mt-2">Skor Kesesuaian</div>
                    </div>
                    <div style="flex: 1;">
                        <h4 class="mb-2">Ringkasan AI</h4>
                        <p class="text-muted">{{ $application->screeningResult->ai_summary ?? 'Tidak ada ringkasan.' }}</p>
                    </div>
                </div>

                @php
                    $extractedData = $application->screeningResult->extracted_data ?? [];
                    $skillMatch = $application->screeningResult->skill_match ?? [];
                @endphp

                <!-- Extracted Info -->
                @if($extractedData)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Education -->
                    @if(isset($extractedData['pendidikan']))
                    <div>
                        <h5 class="mb-3 flex items-center gap-2">
                            <i data-lucide="graduation-cap" style="width:18px;height:18px"></i>
                            Pendidikan
                        </h5>
                        <div class="p-4" style="background: var(--color-gray-50); border-radius: var(--radius-lg);">
                            <div class="font-semibold">{{ $extractedData['pendidikan']['tingkat'] ?? '-' }}</div>
                            <div class="text-muted">{{ $extractedData['pendidikan']['jurusan'] ?? '-' }}</div>
                            <div class="text-sm text-muted">{{ $extractedData['pendidikan']['institusi'] ?? '-' }}</div>
                        </div>
                    </div>
                    @endif

                    <!-- Experience -->
                    <div>
                        <h5 class="mb-3 flex items-center gap-2">
                            <i data-lucide="briefcase" style="width:18px;height:18px"></i>
                            Pengalaman
                        </h5>
                        <div class="p-4" style="background: var(--color-gray-50); border-radius: var(--radius-lg);">
                            <div class="font-semibold text-lg">{{ $extractedData['total_pengalaman_tahun'] ?? 0 }} Tahun</div>
                            @if(isset($extractedData['pengalaman_kerja']) && count($extractedData['pengalaman_kerja']) > 0)
                            <div class="text-sm text-muted mt-2">
                                {{ $extractedData['pengalaman_kerja'][0]['posisi'] ?? '' }} 
                                di {{ $extractedData['pengalaman_kerja'][0]['perusahaan'] ?? '' }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Skills -->
                @if(isset($extractedData['skills']) && count($extractedData['skills']) > 0)
                <div class="mt-6">
                    <h5 class="mb-3 flex items-center gap-2">
                        <i data-lucide="code" style="width:18px;height:18px"></i>
                        Skills Terdeteksi
                    </h5>
                    <div class="flex flex-wrap gap-2">
                        @foreach($extractedData['skills'] as $skill)
                        <span class="skill-tag">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Skill Match Analysis -->
                @if($skillMatch)
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if(isset($skillMatch['skills_terpenuhi']) && count($skillMatch['skills_terpenuhi']) > 0)
                    <div class="p-4" style="background: var(--color-success); border-radius: var(--radius-lg);">
                        <h6 class="font-semibold mb-2 flex items-center gap-2">
                            <i data-lucide="check-circle" style="width:16px;height:16px"></i>
                            Skills Terpenuhi
                        </h6>
                        <div class="flex flex-wrap gap-1">
                            @foreach($skillMatch['skills_terpenuhi'] as $skill)
                            <span class="badge badge-success">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(isset($skillMatch['skills_kurang']) && count($skillMatch['skills_kurang']) > 0)
                    <div class="p-4" style="background: var(--color-danger); border-radius: var(--radius-lg);">
                        <h6 class="font-semibold mb-2 flex items-center gap-2">
                            <i data-lucide="x-circle" style="width:16px;height:16px"></i>
                            Skills Kurang
                        </h6>
                        <div class="flex flex-wrap gap-1">
                            @foreach($skillMatch['skills_kurang'] as $skill)
                            <span class="badge badge-danger">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Kelebihan & Kekurangan -->
                @if($skillMatch)
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if(isset($skillMatch['kelebihan']) && count($skillMatch['kelebihan']) > 0)
                    <div>
                        <h6 class="font-semibold mb-2 text-success">✅ Kelebihan</h6>
                        <ul style="padding-left: 20px; margin: 0;">
                            @foreach($skillMatch['kelebihan'] as $item)
                            <li class="mb-1">{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(isset($skillMatch['kekurangan']) && count($skillMatch['kekurangan']) > 0)
                    <div>
                        <h6 class="font-semibold mb-2 text-danger">⚠️ Perlu Diperhatikan</h6>
                        <ul style="padding-left: 20px; margin: 0;">
                            @foreach($skillMatch['kekurangan'] as $item)
                            <li class="mb-1">{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                @endif
                @endif
            </div>
        </div>

        <!-- Psychometric Test Section -->
        @php
            $recommendation = $application->screeningResult->recommendation ?? null;
            $isEligibleForTest = in_array($recommendation, ['SANGAT_SESUAI', 'SESUAI']);
            $psychometricResults = $application->psychometricResults ?? collect();
        @endphp

        @if($isEligibleForTest)
        <div class="card mb-6" style="border-left: 4px solid var(--color-secondary-400);">
            <div class="card-header">
                <h3 class="card-title">
                    <i data-lucide="brain" style="width:20px;height:20px"></i>
                    Tes Psikotes
                </h3>
                <a href="{{ route('psychometric.index', $application) }}" class="btn btn-sm btn-secondary">
                    <i data-lucide="list" style="width:14px;height:14px"></i>
                    Semua Tes
                </a>
            </div>
            <div class="card-body">
                @php
                    $allTests = \App\Models\PsychometricTest::active()->get();
                    $completedResults = $psychometricResults->whereNotNull('completed_at')->keyBy('test_id');
                @endphp
                
                @foreach($allTests as $test)
                    @php
                        $result = $completedResults->get($test->id);
                        $isCompleted = $result !== null;
                    @endphp
                    <div class="flex justify-between items-center p-3 mb-2" style="background:var(--color-gray-50);border-radius:var(--radius-md);">
                        <div>
                            <div class="font-semibold">{{ $test->name }}</div>
                            @if($isCompleted)
                            <div class="text-sm text-muted">
                                Tipe: <strong>{{ $result->primary_type }}</strong> 
                                | Waktu: {{ $result->formatted_time }}
                            </div>
                            @else
                            <div class="text-sm text-muted">
                                {{ $test->total_questions }} soal | {{ $test->formatted_duration }}
                            </div>
                            @endif
                        </div>
                        @if($isCompleted)
                        <a href="{{ route('psychometric.result', [$application, $test]) }}" class="btn btn-sm btn-success">
                            <i data-lucide="eye" style="width:14px;height:14px"></i>
                            Hasil
                        </a>
                        @else
                        <a href="{{ route('psychometric.start', [$application, $test]) }}" class="btn btn-sm btn-primary">
                            <i data-lucide="play" style="width:14px;height:14px"></i>
                            Mulai
                        </a>
                        @endif
                    </div>
                @endforeach

                <!-- Final Summary Section -->
                @php
                    $finalSummary = $application->screeningResult->psychometric_summary ?? null;
                    $hasCompletedAnyTest = $completedResults->isNotEmpty();
                @endphp

                <hr class="my-4" style="border-top: 1px solid var(--color-gray-200);">

                @if($finalSummary)
                    <div class="p-4" style="background:var(--color-primary-50);border-radius:var(--radius-md);border:1px solid var(--color-primary-200);">
                        <div class="flex items-center gap-2 mb-2">
                            <i data-lucide="award" style="width:20px;height:20px;color:var(--color-primary-600);"></i>
                            <h4 class="font-bold text-primary-700 m-0">Rangkuman Eksekutif & Rekomendasi</h4>
                        </div>
                        <p class="text-sm text-gray-800" style="white-space: pre-line; line-height: 1.6;">
                            {{ $finalSummary }}
                        </p>
                    </div>
                @elseif($hasCompletedAnyTest)
                    <div class="text-center mt-4">
                        <form action="{{ route('psychometric.summary', $application) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-full shadow-md hover:shadow-lg transition-all">
                                <i data-lucide="sparkles" style="width:16px;height:16px;margin-right:8px;"></i>
                                Generate Final Summary & Recommendation
                            </button>
                        </form>
                        <p class="text-xs text-muted mt-2">
                            Analisis AI akan merangkum seluruh hasil tes menjadi rekomendasi akhir untuk HRD.
                        </p>
                    </div>
                @endif
            </div>
        </div>
        @elseif($recommendation)
        <div class="card mb-6" style="border-left: 4px solid var(--color-gray-300);">
            <div class="card-body">
                <div class="flex items-center gap-3">
                    <i data-lucide="x-circle" style="width:24px;height:24px;color:var(--color-gray-400)"></i>
                    <div>
                        <div class="font-semibold">Tes Psikotes Tidak Tersedia</div>
                        <div class="text-sm text-muted">Kandidat tidak memenuhi syarat (Rekomendasi: {{ $recommendation }}). Hanya kandidat dengan status SESUAI atau SANGAT_SESUAI yang dapat mengikuti tes.</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @else
        <!-- No Screening Yet -->
        <div class="card mb-6">
            <div class="card-body text-center p-8">
                <div style="font-size: 64px; color: var(--color-gray-300); margin-bottom: 16px;">
                    <i data-lucide="brain"></i>
                </div>
                <h3 class="mb-2">Belum Di-Screening</h3>
                <p class="text-muted mb-4">CV pelamar ini belum dianalisis oleh AI</p>
                <form action="{{ route('applications.screen', $application) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i data-lucide="zap" style="width:20px;height:20px"></i>
                        Jalankan AI Screening Sekarang
                    </button>
                </form>
            </div>
        </div>
        @endif

        <!-- CV Preview -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i data-lucide="file-text" style="width:20px;height:20px"></i>
                    Dokumen CV
                </h3>
                <a href="{{ asset('storage/' . $application->resume_path) }}" target="_blank" class="btn btn-sm btn-secondary">
                    <i data-lucide="download" style="width:14px;height:14px"></i>
                    Download
                </a>
            </div>
            <div class="card-body">
                @php
                    $extension = pathinfo($application->resume_path, PATHINFO_EXTENSION);
                @endphp
                
                @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                <img src="{{ asset('storage/' . $application->resume_path) }}" alt="CV Preview" style="max-width: 100%; border-radius: var(--radius-lg);">
                @elseif($extension === 'pdf')
                <div style="background: var(--color-gray-100); padding: 48px; text-align: center; border-radius: var(--radius-lg);">
                    <i data-lucide="file-text" style="width:48px;height:48px;color:var(--color-gray-400)"></i>
                    <p class="mt-4 text-muted">Preview PDF tidak tersedia</p>
                    <a href="{{ asset('storage/' . $application->resume_path) }}" target="_blank" class="btn btn-primary mt-2">
                        <i data-lucide="external-link" style="width:16px;height:16px"></i>
                        Buka PDF di Tab Baru
                    </a>
                </div>
                @else
                <p class="text-muted">Format file tidak dapat di-preview</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Job Info -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="card-title">Posisi Dilamar</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('jobs.show', $application->job) }}" class="font-semibold text-lg">
                    {{ $application->job->title }}
                </a>
                @if($application->job->department)
                <div class="text-muted">{{ $application->job->department }}</div>
                @endif
                <div class="mt-4">
                    <div class="text-sm text-muted mb-2">Kualifikasi</div>
                    <div class="text-sm">
                        <div class="mb-1"><strong>Pendidikan:</strong> {{ $application->job->min_education }}</div>
                        <div class="mb-1"><strong>Pengalaman:</strong> {{ $application->job->min_experience_years }} tahun</div>
                    </div>
                    <div class="flex flex-wrap gap-1 mt-2">
                        @foreach($application->job->required_skills ?? [] as $skill)
                        <span class="badge badge-secondary" style="font-size: 10px;">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Status -->
        @if(auth()->user()->role === 'admin')
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="card-title">Update Status</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('applications.updateStatus', $application) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <select name="status" class="form-control form-select">
                            <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>⏳ Menunggu</option>
                            <option value="screening" {{ $application->status == 'screening' ? 'selected' : '' }}>🔍 Sedang Screening</option>
                            <option value="reviewed" {{ $application->status == 'reviewed' ? 'selected' : '' }}>👁️ Sudah Ditinjau</option>
                            <option value="shortlisted" {{ $application->status == 'shortlisted' ? 'selected' : '' }}>✅ Lolos Seleksi</option>
                            <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>❌ Ditolak</option>
                            <option value="hired" {{ $application->status == 'hired' ? 'selected' : '' }}>🎉 Diterima</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i data-lucide="save" style="width:16px;height:16px"></i>
                        Simpan Status
                    </button>
                </form>
            </div>
        </div>

        <!-- Notes -->
        @if($application->notes)
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="card-title">Catatan</h3>
            </div>
            <div class="card-body">
                <p class="text-muted">{{ $application->notes }}</p>
            </div>
        </div>
        @endif
        @endif

        <!-- Info -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-sm text-muted">Tanggal Melamar</div>
                    <div class="font-semibold">{{ $application->created_at->format('d M Y, H:i') }}</div>
                </div>
                @if($application->screeningResult)
                <div>
                    <div class="text-sm text-muted">Tanggal Screening</div>
                    <div class="font-semibold">{{ $application->screeningResult->created_at->format('d M Y, H:i') }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
