@extends('layouts.app')

@section('title', 'Lamar: ' . $job->title)

@section('content')
<section class="py-8 px-4">
    <div class="container" style="max-width: 800px;">
        <a href="{{ route('jobs.public') }}" class="btn btn-sm btn-secondary mb-6">
            <i data-lucide="arrow-left" style="width:16px;height:16px"></i>
            Kembali ke Daftar Lowongan
        </a>

        <!-- Job Summary -->
        <div class="card mb-6" style="background: linear-gradient(135deg, var(--color-primary-100) 0%, var(--color-secondary-100) 100%);">
            <div class="card-body">
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
                    @if($job->salary_range)
                    <div class="text-right">
                        <div class="text-sm text-muted">Gaji</div>
                        <div class="font-semibold" style="color: var(--color-success-dark);">{{ $job->salary_range }}</div>
                    </div>
                    @endif
                </div>

                <div class="mt-4 pt-4" style="border-top: 1px solid rgba(0,0,0,0.1);">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <div class="text-sm text-muted">Pendidikan</div>
                            <div class="font-semibold">Min. {{ $job->min_education }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-muted">Pengalaman</div>
                            <div class="font-semibold">Min. {{ $job->min_experience_years }} tahun</div>
                        </div>
                        @if($job->deadline)
                        <div>
                            <div class="text-sm text-muted">Deadline</div>
                            <div class="font-semibold">{{ $job->deadline->format('d M Y') }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                @if($job->required_skills && count($job->required_skills) > 0)
                <div class="mt-4">
                    <div class="text-sm text-muted mb-2">Skills yang Dibutuhkan</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($job->required_skills as $skill)
                        <span class="skill-tag">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Application Form -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i data-lucide="file-text" style="width:20px;height:20px"></i>
                    Form Lamaran
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('apply.store', $job) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span style="color: var(--color-danger-dark)">*</span></label>
                        <input type="text" name="applicant_name" class="form-control @error('applicant_name') is-invalid @enderror" 
                               value="{{ old('applicant_name') }}" placeholder="Masukkan nama lengkap Anda" required>
                        @error('applicant_name')
                        <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Email <span style="color: var(--color-danger-dark)">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" placeholder="email@example.com" required>
                            @error('email')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">No. Telepon / WhatsApp</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone') }}" placeholder="62821xxxxxxx">
                            @error('phone')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">CV / Resume <span style="color: var(--color-danger-dark)">*</span></label>
                        <label class="file-upload" for="resume">
                            <div class="file-upload-icon">
                                <i data-lucide="upload-cloud"></i>
                            </div>
                            <div class="file-upload-text">
                                <strong>Klik untuk upload CV</strong> atau drag & drop<br>
                                PDF, JPG, PNG (Max 2MB)
                            </div>
                            <input type="file" name="resume" id="resume" accept=".pdf,.jpg,.jpeg,.png,.webp" 
                                   style="display: none;" required onchange="updateFileName(this)">
                        </label>
                        <div id="fileName" class="text-sm mt-2" style="color: var(--color-success-dark);"></div>
                        @error('resume')
                        <div class="form-error">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            💡 Tips: Gunakan PDF dengan teks yang jelas untuk hasil screening AI terbaik
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Catatan Tambahan</label>
                        <textarea name="notes" class="form-control" rows="3" 
                                  placeholder="Ceritakan mengapa Anda tertarik dengan posisi ini... (opsional)">{{ old('notes') }}</textarea>
                    </div>

                    <div class="alert alert-info mb-6">
                        <i data-lucide="info" style="width:20px;height:20px;flex-shrink:0"></i>
                        <div>
                            <strong>CV Anda akan dianalisis oleh AI</strong><br>
                            Sistem kami menggunakan teknologi AI untuk mencocokkan kualifikasi Anda dengan persyaratan posisi ini. 
                            Pastikan CV Anda lengkap dan up-to-date.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        <i data-lucide="send" style="width:20px;height:20px"></i>
                        Kirim Lamaran
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });

    function updateFileName(input) {
        const target = document.getElementById('fileName');
        if (input.files && input.files[0]) {
            target.innerHTML = '✅ File dipilih: <strong>' + input.files[0].name + '</strong>';
        } else {
            target.innerHTML = '';
        }
    }

    // Drag and drop
    const fileUpload = document.querySelector('.file-upload');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(event => {
        fileUpload.addEventListener(event, e => {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    ['dragenter', 'dragover'].forEach(event => {
        fileUpload.addEventListener(event, () => {
            fileUpload.style.borderColor = 'var(--color-primary-500)';
            fileUpload.style.background = 'var(--color-primary-50)';
        });
    });

    ['dragleave', 'drop'].forEach(event => {
        fileUpload.addEventListener(event, () => {
            fileUpload.style.borderColor = '';
            fileUpload.style.background = '';
        });
    });

    fileUpload.addEventListener('drop', e => {
        const input = document.getElementById('resume');
        input.files = e.dataTransfer.files;
        updateFileName(input);
    });
</script>
@endpush
