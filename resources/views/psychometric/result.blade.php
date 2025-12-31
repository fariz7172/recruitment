@extends('layouts.app')

@section('title', 'Hasil ' . $test->name)

@section('content')
<div class="mb-6 flex gap-2">
    <a href="{{ route('applications.show', $application) }}" class="btn btn-sm btn-secondary">
        <i data-lucide="arrow-left" style="width:16px;height:16px"></i>
        Kembali ke Lamaran
    </a>
    <a href="{{ route('psychometric.index', $application) }}" class="btn btn-sm btn-secondary">
        <i data-lucide="list" style="width:16px;height:16px"></i>
        Daftar Tes
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Result -->
    <div class="lg:col-span-2">
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="card-title">
                    <i data-lucide="award" style="width:20px;height:20px"></i>
                    Hasil {{ $test->name }}
                </h3>
                <span class="badge badge-{{ $test->type === 'cognitive' ? 'info' : ($test->type === 'aptitude' ? 'warning' : 'secondary') }}">
                    {{ ucfirst($test->type) }}
                </span>
            </div>
            <div class="card-body">
                @if($test->type === 'cognitive' || $test->type === 'aptitude')
                    {{-- COGNITIVE/APTITUDE TEST RESULT --}}
                    @php
                        $totalScore = $result->scores['total'] ?? 0;
                        $correctCount = $result->scores['correct_count'] ?? 0;
                        $totalCount = $result->scores['total_count'] ?? 20;
                        $gradeColors = [
                            'SANGAT_TINGGI' => 'success',
                            'TINGGI' => 'info',
                            'CUKUP' => 'warning',
                            'RENDAH' => 'danger',
                            'SANGAT_RENDAH' => 'danger',
                        ];
                        $gradeLabels = [
                            'SANGAT_TINGGI' => 'Sangat Tinggi',
                            'TINGGI' => 'Tinggi',
                            'CUKUP' => 'Cukup',
                            'RENDAH' => 'Rendah',
                            'SANGAT_RENDAH' => 'Sangat Rendah',
                        ];
                        $gradeColor = $gradeColors[$result->primary_type] ?? 'secondary';
                        $gradeLabel = $gradeLabels[$result->primary_type] ?? $result->primary_type;
                    @endphp
                    
                    <!-- Score Circle -->
                    <div class="text-center mb-6 py-6" style="background: linear-gradient(135deg, var(--color-{{ $gradeColor }}) 0%, var(--color-{{ $gradeColor }}) 100%); border-radius: var(--radius-lg);">
                        <div style="font-size: 4rem; font-weight: 700; color: var(--color-gray-800);">
                            {{ $totalScore }}%
                        </div>
                        <div class="text-lg font-semibold">{{ $gradeLabel }}</div>
                        <div class="text-sm mt-2">{{ $correctCount }} dari {{ $totalCount }} jawaban benar</div>
                    </div>

                    <!-- Dimension Scores -->
                    <h4 class="mb-4">Skor per Kategori</h4>
                    <div class="dimension-scores mb-6">
                        @php
                            $dimensionLabels = [
                                'verbal' => '💬 Verbal',
                                'numerical' => '🔢 Numerical',
                                'logical' => '🧠 Logical',
                                'spatial' => '📐 Spatial',
                                'general' => '📚 General',
                                'calculation' => '➕ Calculation',
                            ];
                            $scores = $result->scores ?? [];
                        @endphp
                        @forelse($scores as $dim => $score)
                            @if(!in_array($dim, ['total', 'correct_count', 'total_count']))
                            <div class="mb-3">
                                <div class="flex justify-between mb-1">
                                    <span class="font-semibold">{{ $dimensionLabels[$dim] ?? ucfirst($dim) }}</span>
                                    <span class="font-semibold">{{ $score }}%</span>
                                </div>
                                <div style="height:20px;background:var(--color-gray-200);border-radius:10px;overflow:hidden;">
                                    <div style="height:100%;width:{{ $score }}%;background:linear-gradient(90deg, var(--color-accent-300), var(--color-accent-500));border-radius:10px;transition:width 1s ease;"></div>
                                </div>
                            </div>
                            @endif
                        @empty
                            <p class="text-muted">Tidak ada data skor detail.</p>
                        @endforelse
                    </div>

                @else
                    {{-- PERSONALITY TEST RESULT (DISC, EPPS) --}}
                    <!-- Primary Type -->
                    <div class="text-center mb-6 py-6" style="background: linear-gradient(135deg, var(--color-primary-100) 0%, var(--color-accent-100) 100%); border-radius: var(--radius-lg);">
                        <div style="font-size: 4rem; font-weight: 700; color: var(--color-primary-600);">
                            {{ $result->primary_type }}
                        </div>
                        <div class="text-lg font-semibold">{{ $result->type_description }}</div>
                        <div class="text-sm text-muted mt-2">Tipe Dominan</div>
                    </div>

                    @if($test->slug === 'disc')
                    <!-- DISC Scores Chart -->
                    <h4 class="mb-4">Profil DISC</h4>
                    <div class="disc-bars mb-6">
                        @php
                            $colors = [
                                'D' => '#EF4444',
                                'I' => '#F59E0B', 
                                'S' => '#10B981',
                                'C' => '#3B82F6'
                            ];
                            $labels = [
                                'D' => 'Dominance',
                                'I' => 'Influence',
                                'S' => 'Steadiness',
                                'C' => 'Compliance'
                            ];
                        @endphp
                        @foreach(['D', 'I', 'S', 'C'] as $dim)
                        <div class="disc-bar-item mb-3">
                            <div class="flex justify-between mb-1">
                                <span class="font-semibold">
                                    <span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:{{ $colors[$dim] }};color:white;text-align:center;line-height:24px;margin-right:8px;">{{ $dim }}</span>
                                    {{ $labels[$dim] }}
                                </span>
                                <span class="font-semibold">{{ $result->scores[$dim] ?? 0 }} pts ({{ $result->getScorePercentage($dim) }}%)</span>
                            </div>
                            <div style="height:24px;background:var(--color-gray-200);border-radius:12px;overflow:hidden;">
                                <div style="height:100%;width:{{ $result->getScorePercentage($dim) }}%;background:{{ $colors[$dim] }};border-radius:12px;transition:width 1s ease;"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <!-- EPPS/Other Personality Profile -->
                    <h4 class="mb-4">Profil Motivasi</h4>
                    <div class="motivation-scores mb-6">
                        @php
                            $eppsLabels = [
                                'achievement' => '🏆 Achievement',
                                'order' => '📋 Order',
                                'autonomy' => '🦅 Autonomy',
                                'affiliation' => '🤝 Affiliation',
                                'dominance' => '👑 Dominance',
                            ];
                            $maxScore = max($result->scores ?: [1]);
                        @endphp
                        @foreach($result->scores as $dim => $score)
                        <div class="mb-3">
                            <div class="flex justify-between mb-1">
                                <span class="font-semibold">{{ $eppsLabels[$dim] ?? ucfirst($dim) }}</span>
                                <span class="font-semibold">{{ $score }} pts</span>
                            </div>
                            <div style="height:20px;background:var(--color-gray-200);border-radius:10px;overflow:hidden;">
                                <div style="height:100%;width:{{ ($score / ($maxScore ?: 1)) * 100 }}%;background:linear-gradient(90deg, var(--color-secondary-300), var(--color-secondary-500));border-radius:10px;transition:width 1s ease;"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                @endif

                <!-- AI Analysis -->
                <div class="p-4 mb-4" style="background:var(--color-secondary-50);border-radius:var(--radius-md);border-left:4px solid var(--color-secondary-400);">
                    <h4 class="mb-2 flex items-center gap-2">
                        <i data-lucide="sparkles" style="width:20px;height:20px;color:var(--color-accent-500)"></i>
                        Analisis AI
                    </h4>
                    <p class="mb-0">{{ $result->analysis }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Candidate Info -->
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="mb-3">Info Kandidat</h4>
                <div class="mb-2">
                    <span class="text-muted">Nama:</span>
                    <strong>{{ $application->applicant_name }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Posisi:</span>
                    <strong>{{ $application->job->title ?? 'N/A' }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Email:</span>
                    <strong>{{ $application->email }}</strong>
                </div>
            </div>
        </div>

        <!-- Test Stats -->
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="mb-3">Statistik Tes</h4>
                <div class="mb-2 flex justify-between">
                    <span class="text-muted">Waktu Pengerjaan:</span>
                    <strong>{{ $result->formatted_time }}</strong>
                </div>
                <div class="mb-2 flex justify-between">
                    <span class="text-muted">Selesai:</span>
                    <strong>{{ $result->completed_at ? $result->completed_at->format('d M Y H:i') : 'Belum selesai' }}</strong>
                </div>
                @if($test->type === 'cognitive' || $test->type === 'aptitude')
                <div class="mb-2 flex justify-between">
                    <span class="text-muted">Jawaban Benar:</span>
                    <strong>{{ $result->secondary_type }}</strong>
                </div>
                @else
                <div class="mb-2 flex justify-between">
                    <span class="text-muted">Tipe Sekunder:</span>
                    <strong>{{ $result->secondary_type }}</strong>
                </div>
                @endif
            </div>
        </div>

        <!-- Test Type Description -->
        <div class="card">
            <div class="card-body">
                @if($test->type === 'cognitive' || $test->type === 'aptitude')
                <h4 class="mb-3">Interpretasi Skor</h4>
                <div class="text-sm">
                    <div class="mb-2 p-2" style="background:var(--color-success);border-radius:var(--radius-sm);">
                        <strong>90-100%:</strong> Sangat Tinggi
                    </div>
                    <div class="mb-2 p-2" style="background:var(--color-info);border-radius:var(--radius-sm);">
                        <strong>75-89%:</strong> Tinggi
                    </div>
                    <div class="mb-2 p-2" style="background:var(--color-warning);border-radius:var(--radius-sm);">
                        <strong>60-74%:</strong> Cukup
                    </div>
                    <div class="mb-2 p-2" style="background:var(--color-danger);border-radius:var(--radius-sm);">
                        <strong>40-59%:</strong> Rendah
                    </div>
                    <div class="p-2" style="background:var(--color-gray-200);border-radius:var(--radius-sm);">
                        <strong>&lt;40%:</strong> Sangat Rendah
                    </div>
                </div>
                @elseif($test->slug === 'disc')
                <h4 class="mb-3">Interpretasi DISC</h4>
                <div class="text-sm">
                    <div class="mb-3 p-2" style="background:var(--color-gray-50);border-radius:var(--radius-sm);">
                        <strong style="color:#EF4444">D - Dominance:</strong><br>
                        Tegas, kompetitif, berorientasi hasil
                    </div>
                    <div class="mb-3 p-2" style="background:var(--color-gray-50);border-radius:var(--radius-sm);">
                        <strong style="color:#F59E0B">I - Influence:</strong><br>
                        Antusias, optimis, pandai bersosialisasi
                    </div>
                    <div class="mb-3 p-2" style="background:var(--color-gray-50);border-radius:var(--radius-sm);">
                        <strong style="color:#10B981">S - Steadiness:</strong><br>
                        Tenang, sabar, dapat diandalkan
                    </div>
                    <div class="p-2" style="background:var(--color-gray-50);border-radius:var(--radius-sm);">
                        <strong style="color:#3B82F6">C - Compliance:</strong><br>
                        Analitis, teliti, mengutamakan kualitas
                    </div>
                </div>
                @else
                <h4 class="mb-3">Interpretasi EPPS</h4>
                <div class="text-sm">
                    <div class="mb-2 p-2" style="background:var(--color-gray-50);border-radius:var(--radius-sm);">
                        <strong>🏆 Achievement:</strong> Motivasi berprestasi
                    </div>
                    <div class="mb-2 p-2" style="background:var(--color-gray-50);border-radius:var(--radius-sm);">
                        <strong>📋 Order:</strong> Kebutuhan keteraturan
                    </div>
                    <div class="mb-2 p-2" style="background:var(--color-gray-50);border-radius:var(--radius-sm);">
                        <strong>🦅 Autonomy:</strong> Kemandirian
                    </div>
                    <div class="mb-2 p-2" style="background:var(--color-gray-50);border-radius:var(--radius-sm);">
                        <strong>🤝 Affiliation:</strong> Kebutuhan berafiliasi
                    </div>
                    <div class="p-2" style="background:var(--color-gray-50);border-radius:var(--radius-sm);">
                        <strong>👑 Dominance:</strong> Kecenderungan memimpin
                    </div>
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
