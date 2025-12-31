@extends('layouts.app')

@section('title', 'Input Lamaran - ScreeningAI')

@section('content')
<div class="mb-6">
    <a href="{{ route('applications.index') }}" class="btn btn-sm btn-secondary mb-4">
        <i data-lucide="arrow-left" style="width:16px;height:16px"></i>
        Kembali
    </a>
    <h1 class="mb-2">Input Lamaran Baru</h1>
    <p class="text-muted">Masukkan data pelamar dan upload CV</p>
</div>

<form action="{{ route('applications.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="card-title">Data Pelamar</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Posisi yang Dilamar <span style="color: var(--color-danger-dark)">*</span></label>
                        <select name="job_id" class="form-control form-select @error('job_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Lowongan --</option>
                            @foreach($jobs as $j)
                            <option value="{{ $j->id }}" {{ old('job_id', $job?->id) == $j->id ? 'selected' : '' }}>
                                {{ $j->title }} {{ $j->department ? '(' . $j->department . ')' : '' }}
                            </option>
                            @endforeach
                        </select>
                        @error('job_id')
                        <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span style="color: var(--color-danger-dark)">*</span></label>
                        <input type="text" name="applicant_name" class="form-control @error('applicant_name') is-invalid @enderror" 
                               value="{{ old('applicant_name') }}" placeholder="Nama lengkap pelamar" required>
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
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                            @error('phone')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="3" 
                                  placeholder="Catatan tambahan tentang pelamar (opsional)">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="card-title">Upload Dokumen</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">CV / Resume <span style="color: var(--color-danger-dark)">*</span></label>
                        <label class="file-upload" for="resume">
                            <div class="file-upload-icon">
                                <i data-lucide="upload-cloud"></i>
                            </div>
                            <div class="file-upload-text">
                                <strong>Klik untuk upload</strong> atau drag and drop<br>
                                PDF, JPG, PNG (Max 10MB)
                            </div>
                            <input type="file" name="resume" id="resume" accept=".pdf,.jpg,.jpeg,.png,.webp" 
                                   style="display: none;" required onchange="updateFileName(this, 'resumeFileName')">
                        </label>
                        <div id="resumeFileName" class="text-sm text-muted mt-2"></div>
                        @error('resume')
                        <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Cover Letter (Opsional)</label>
                        <label class="file-upload" for="cover_letter">
                            <div class="file-upload-icon">
                                <i data-lucide="file-text"></i>
                            </div>
                            <div class="file-upload-text">
                                <strong>Klik untuk upload</strong><br>
                                PDF, DOC, DOCX (Max 5MB)
                            </div>
                            <input type="file" name="cover_letter" id="cover_letter" accept=".pdf,.doc,.docx" 
                                   style="display: none;" onchange="updateFileName(this, 'coverLetterFileName')">
                        </label>
                        <div id="coverLetterFileName" class="text-sm text-muted mt-2"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <div class="card mb-6" style="background: var(--color-info); border: none;">
                <div class="card-body">
                    <h4 class="mb-3 flex items-center gap-2">
                        <i data-lucide="info" style="width:20px;height:20px"></i>
                        Tips Upload CV
                    </h4>
                    <ul style="padding-left: 20px; margin: 0;">
                        <li class="mb-2">Gunakan file PDF untuk hasil OCR terbaik</li>
                        <li class="mb-2">Pastikan teks dalam CV dapat dibaca dengan jelas</li>
                        <li class="mb-2">Hindari CV dengan banyak gambar/grafik yang menutupi teks</li>
                        <li>Format CV standar lebih mudah dianalisis AI</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-6">
                <div class="card-body">
                    <div class="form-check mb-4">
                        <input type="checkbox" name="auto_screen" id="auto_screen" class="form-check-input" value="1" checked>
                        <label for="auto_screen" class="form-label mb-0">
                            <strong>Langsung jalankan AI Screening</strong><br>
                            <span class="text-sm text-muted">CV akan langsung dianalisis setelah disubmit</span>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                <i data-lucide="send" style="width:20px;height:20px"></i>
                Submit Lamaran
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });

    function updateFileName(input, targetId) {
        const target = document.getElementById(targetId);
        if (input.files && input.files[0]) {
            target.innerHTML = '<i data-lucide="file" style="width:14px;height:14px;display:inline"></i> ' + input.files[0].name;
            lucide.createIcons();
        } else {
            target.innerHTML = '';
        }
    }

    // Drag and drop support
    document.querySelectorAll('.file-upload').forEach(label => {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            label.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            label.addEventListener(eventName, () => label.style.borderColor = 'var(--color-primary-500)', false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            label.addEventListener(eventName, () => label.style.borderColor = '', false);
        });

        label.addEventListener('drop', function(e) {
            const input = this.querySelector('input[type="file"]');
            input.files = e.dataTransfer.files;
            input.dispatchEvent(new Event('change'));
        }, false);
    });
</script>
@endpush
