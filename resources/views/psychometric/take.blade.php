@extends('layouts.app')

@section('title', $test->name)

@section('content')
<div class="container" style="max-width: 800px;">
    <div class="card mb-6" style="background: linear-gradient(135deg, var(--color-primary-100) 0%, var(--color-secondary-100) 100%);">
        <div class="card-body">
            <h2 class="mb-2">{{ $test->name }}</h2>
            <p class="text-muted mb-4">{{ $test->description }}</p>
            <div class="flex gap-4 text-sm">
                <span><i data-lucide="clock" style="width:16px;height:16px;display:inline"></i> {{ $test->formatted_duration }}</span>
                <span><i data-lucide="list" style="width:16px;height:16px;display:inline"></i> {{ $questions->count() }} Soal</span>
                <span class="badge badge-{{ $test->type === 'cognitive' ? 'info' : ($test->type === 'aptitude' ? 'warning' : 'secondary') }}">
                    {{ ucfirst($test->type) }}
                </span>
            </div>
        </div>
    </div>

    <div class="alert alert-{{ $test->type === 'cognitive' || $test->type === 'aptitude' ? 'warning' : 'info' }} mb-6">
        <i data-lucide="info" style="width:20px;height:20px;flex-shrink:0"></i>
        <div>
            <strong>Petunjuk Pengerjaan:</strong><br>
            @if($test->type === 'cognitive' || $test->type === 'aptitude')
            Pilih jawaban yang benar. Kerjakan secepat dan seakurat mungkin. Waktu terbatas!
            @else
            Pilih jawaban yang paling sesuai dengan diri Anda. Tidak ada jawaban benar atau salah.
            @endif
        </div>
    </div>

    <form action="{{ route('psychometric.submit', [$application, $test]) }}" method="POST" id="testForm">
        @csrf
        <input type="hidden" name="started_at" value="{{ $result->started_at->toIso8601String() }}">

        @foreach($questions as $index => $question)
        <div class="card mb-4 question-card" data-question="{{ $index + 1 }}">
            <div class="card-body">
                <div class="flex items-start gap-3 mb-4">
                    <span class="flex items-center justify-center" style="width:32px;height:32px;border-radius:50%;background:var(--color-primary-200);font-weight:600;flex-shrink:0;">
                        {{ $index + 1 }}
                    </span>
                    <p class="mb-0" style="font-size: 1.05rem;">{{ $question->question_text }}</p>
                </div>

                {{-- Wartegg Stimulus Image --}}
                @if($test->slug === 'wartegg')
                    @include('psychometric.partials.wartegg-stimulus', ['box' => $index + 1])
                @endif


                @if($question->question_type === 'multiple_choice')
                <!-- Multiple Choice (Cognitive/Aptitude Tests) -->
                <div class="mc-options">
                    @foreach($question->options as $option)
                    <label class="mc-option">
                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option['value'] }}" required>
                        <span class="mc-label">
                            <span class="mc-letter">{{ $option['value'] }}</span>
                            <span class="mc-text">{{ $option['label'] }}</span>
                        </span>
                    </label>
                    @endforeach
                </div>
                @elseif($question->question_type === 'pattern_sequence')
                <!-- Logical Reasoning Pattern Sequence Test -->
                @php
                    $pattern = $question->settings['pattern'] ?? 'rotate_square';
                @endphp
                <div class="pattern-container">
                    <div class="pattern-sequence">
                        <span class="pattern-label">Pola:</span>
                        <div class="pattern-boxes">
                            @include('psychometric.patterns.' . $pattern, ['step' => 1])
                            @include('psychometric.patterns.' . $pattern, ['step' => 2])
                            @include('psychometric.patterns.' . $pattern, ['step' => 3])
                            @include('psychometric.patterns.' . $pattern, ['step' => 4])
                            <div class="pattern-box pattern-question">
                                <span style="font-size:2rem;color:var(--color-gray-400);">?</span>
                            </div>
                        </div>
                    </div>
                    <div class="pattern-answers">
                        <span class="pattern-label">Pilih jawaban:</span>
                        <div class="pattern-choices">
                            @foreach(['A', 'B', 'C', 'D'] as $choice)
                            <label class="pattern-choice">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $choice }}" required>
                                <div class="pattern-choice-box">
                                    <span class="choice-letter">{{ $choice }}</span>
                                    @include('psychometric.patterns.' . $pattern, ['step' => 5, 'answer' => $choice])
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                <!-- Likert Scale (Personality Tests) -->
                <div class="likert-scale">
                    @foreach($question->options as $option)
                    <label class="likert-option">
                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option['value'] }}" required>
                        <span class="likert-label">
                            <span class="likert-value">{{ $option['value'] }}</span>
                            <span class="likert-text">{{ $option['label'] }}</span>
                        </span>
                    </label>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endforeach

        <div class="card" style="position:sticky;bottom:20px;box-shadow:0 -4px 20px rgba(0,0,0,0.1);">
            <div class="card-body">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-muted">Progress:</span>
                        <span id="progressCount">0</span> / {{ $questions->count() }}
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                        <i data-lucide="send" style="width:20px;height:20px"></i>
                        Selesai & Kirim
                    </button>
                </div>
                <div class="progress-bar mt-3" style="height:6px;background:var(--color-gray-200);border-radius:3px;">
                    <div id="progressBar" style="height:100%;width:0%;background:linear-gradient(90deg,var(--color-accent-300),var(--color-accent-400));border-radius:3px;transition:width 0.3s;"></div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
/* Likert Scale Styles */
.likert-scale {
    display: flex;
    justify-content: space-between;
    gap: 8px;
    flex-wrap: wrap;
}

.likert-option {
    flex: 1;
    min-width: 100px;
    cursor: pointer;
}

.likert-option input { display: none; }

.likert-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 12px 8px;
    border: 2px solid var(--color-gray-200);
    border-radius: 12px;
    transition: all 0.2s;
    text-align: center;
}

.likert-option input:checked + .likert-label {
    border-color: var(--color-accent-400);
    background: var(--color-accent-50);
}

.likert-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--color-primary-600);
    margin-bottom: 4px;
}

.likert-text {
    font-size: 0.75rem;
    color: var(--color-gray-600);
}

/* Multiple Choice Styles */
.mc-options {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.mc-option {
    cursor: pointer;
}

.mc-option input { display: none; }

.mc-label {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border: 2px solid var(--color-gray-200);
    border-radius: 12px;
    transition: all 0.2s;
}

.mc-option input:checked + .mc-label {
    border-color: var(--color-accent-400);
    background: var(--color-accent-50);
}

.mc-letter {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: var(--color-primary-100);
    font-weight: 700;
    color: var(--color-primary-600);
    flex-shrink: 0;
}

.mc-option input:checked + .mc-label .mc-letter {
    background: var(--color-accent-400);
    color: white;
}

.mc-text {
    font-size: 1rem;
}

.question-card.answered {
    border-left: 4px solid var(--color-success);
}

/* Pattern Sequence Test Styles */
.pattern-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.pattern-sequence, .pattern-answers {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.pattern-label {
    font-weight: 600;
    color: var(--color-gray-600);
}

.pattern-boxes {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
}

.pattern-box {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border: 2px solid var(--color-gray-300);
    border-radius: 8px;
}

.pattern-box.pattern-question {
    border: 2px dashed var(--color-primary-400);
    background: var(--color-primary-50);
}

.pattern-svg {
    width: 60px;
    height: 60px;
}

.pattern-choices {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.pattern-choice {
    cursor: pointer;
}

.pattern-choice input { display: none; }

.pattern-choice-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 8px;
    border: 2px solid var(--color-gray-200);
    border-radius: 12px;
    transition: all 0.2s;
    background: white;
}

.pattern-choice input:checked + .pattern-choice-box {
    border-color: var(--color-accent-400);
    background: var(--color-accent-50);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.choice-letter {
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--color-primary-600);
    margin-bottom: 4px;
}

.pattern-choice input:checked + .pattern-choice-box .choice-letter {
    color: var(--color-accent-600);
}

@media (max-width: 640px) {
    .likert-option { min-width: 60px; }
    .likert-text { display: none; }
    .pattern-box { width: 55px; height: 55px; }
    .pattern-svg { width: 45px; height: 45px; }
}
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        const form = document.getElementById('testForm');
        const submitBtn = document.getElementById('submitBtn');
        const progressCount = document.getElementById('progressCount');
        const progressBar = document.getElementById('progressBar');
        const totalQuestions = {{ $questions->count() }};

        function updateProgress() {
            const answered = form.querySelectorAll('input[type="radio"]:checked').length;
            progressCount.textContent = answered;
            progressBar.style.width = (answered / totalQuestions * 100) + '%';
            submitBtn.disabled = answered < totalQuestions;

            // Mark answered questions
            document.querySelectorAll('.question-card').forEach((card, index) => {
                const questionNum = index + 1;
                const inputs = card.querySelectorAll('input[type="radio"]');
                const isAnswered = Array.from(inputs).some(input => input.checked);
                card.classList.toggle('answered', isAnswered);
            });
        }

        form.querySelectorAll('input[type="radio"]').forEach(input => {
            input.addEventListener('change', updateProgress);
        });

        // Confirm before leaving
        window.addEventListener('beforeunload', function(e) {
            e.preventDefault();
            e.returnValue = '';
        });

        form.addEventListener('submit', function() {
            window.removeEventListener('beforeunload', function() {});
        });
    });
</script>
@endpush
