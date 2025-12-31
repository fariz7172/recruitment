@extends('layouts.app')

@section('title', 'Buat Lowongan Baru - ScreeningAI')

@section('content')
<div class="mb-6">
    <a href="{{ route('jobs.index') }}" class="btn btn-sm btn-secondary mb-4">
        <i data-lucide="arrow-left" style="width:16px;height:16px"></i>
        Kembali
    </a>
    <h1 class="mb-2">Buat Lowongan Baru</h1>
    <p class="text-muted">Isi detail posisi yang akan dibuka</p>
</div>

<form action="{{ route('jobs.store') }}" method="POST">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="card-title">Informasi Dasar</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Judul Posisi <span style="color: var(--color-danger-dark)">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title') }}" placeholder="contoh: Senior Software Engineer" required>
                        @error('title')
                        <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi Pekerjaan <span style="color: var(--color-danger-dark)">*</span></label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="6" placeholder="Jelaskan tanggung jawab, scope pekerjaan, dll..." required>{{ old('description') }}</textarea>
                        @error('description')
                        <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Departemen</label>
                            <input type="text" name="department" class="form-control" 
                                   value="{{ old('department') }}" placeholder="contoh: IT, Marketing, HR">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Lokasi</label>
                            <input type="text" name="location" class="form-control" 
                                   value="{{ old('location') }}" placeholder="contoh: Jakarta, Remote">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Tipe Pekerjaan <span style="color: var(--color-danger-dark)">*</span></label>
                            <select name="employment_type" class="form-control form-select" required>
                                <option value="full-time" {{ old('employment_type') == 'full-time' ? 'selected' : '' }}>Full-time</option>
                                <option value="part-time" {{ old('employment_type') == 'part-time' ? 'selected' : '' }}>Part-time</option>
                                <option value="contract" {{ old('employment_type') == 'contract' ? 'selected' : '' }}>Kontrak</option>
                                <option value="internship" {{ old('employment_type') == 'internship' ? 'selected' : '' }}>Magang</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Deadline Lamaran</label>
                            <input type="date" name="deadline" class="form-control" value="{{ old('deadline') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="card-title">Kualifikasi (Untuk AI Screening)</h3>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Pendidikan Minimal <span style="color: var(--color-danger-dark)">*</span></label>
                            <select name="min_education" class="form-control form-select" required>
                                <option value="SMA" {{ old('min_education') == 'SMA' ? 'selected' : '' }}>SMA/SMK</option>
                                <option value="D3" {{ old('min_education') == 'D3' ? 'selected' : '' }}>D3</option>
                                <option value="S1" {{ old('min_education', 'S1') == 'S1' ? 'selected' : '' }}>S1</option>
                                <option value="S2" {{ old('min_education') == 'S2' ? 'selected' : '' }}>S2</option>
                                <option value="S3" {{ old('min_education') == 'S3' ? 'selected' : '' }}>S3</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Pengalaman Minimal (tahun) <span style="color: var(--color-danger-dark)">*</span></label>
                            <input type="number" name="min_experience_years" class="form-control" 
                                   value="{{ old('min_experience_years', 0) }}" min="0" max="30" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Skills yang Dibutuhkan <span style="color: var(--color-danger-dark)">*</span></label>
                        <div class="text-sm text-muted mb-2">Tekan Enter untuk menambah skill baru</div>
                        <div id="requiredSkillsContainer">
                            <div class="flex gap-2 mb-2 skill-input-row">
                                <input type="text" name="required_skills[]" class="form-control" placeholder="contoh: PHP">
                                <button type="button" class="btn btn-secondary btn-icon remove-skill" style="display: none;">
                                    <i data-lucide="x" style="width:16px;height:16px"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary" id="addRequiredSkill">
                            <i data-lucide="plus" style="width:14px;height:14px"></i>
                            Tambah Skill
                        </button>
                        @error('required_skills')
                        <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Skills Tambahan (Opsional)</label>
                        <div id="preferredSkillsContainer">
                            <div class="flex gap-2 mb-2 skill-input-row">
                                <input type="text" name="preferred_skills[]" class="form-control" placeholder="contoh: Docker">
                                <button type="button" class="btn btn-secondary btn-icon remove-skill" style="display: none;">
                                    <i data-lucide="x" style="width:16px;height:16px"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary" id="addPreferredSkill">
                            <i data-lucide="plus" style="width:14px;height:14px"></i>
                            Tambah Skill
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="card-title">Gaji (Opsional)</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Gaji Minimal (Rp)</label>
                        <input type="number" name="salary_min" class="form-control" 
                               value="{{ old('salary_min') }}" placeholder="5000000">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gaji Maksimal (Rp)</label>
                        <input type="number" name="salary_max" class="form-control" 
                               value="{{ old('salary_max') }}" placeholder="10000000">
                    </div>
                </div>
            </div>

            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="card-title">Status</h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <select name="status" class="form-control form-select">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>✅ Aktif (Terima Lamaran)</option>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>📝 Draft (Simpan Dulu)</option>
                            <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>🔒 Ditutup</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                <i data-lucide="save" style="width:20px;height:20px"></i>
                Simpan Lowongan
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();

    function addSkillRow(containerId) {
        const container = document.getElementById(containerId);
        const inputName = containerId === 'requiredSkillsContainer' ? 'required_skills[]' : 'preferred_skills[]';
        
        const row = document.createElement('div');
        row.className = 'flex gap-2 mb-2 skill-input-row';
        row.innerHTML = `
            <input type="text" name="${inputName}" class="form-control" placeholder="Skill...">
            <button type="button" class="btn btn-secondary btn-icon remove-skill">
                <i data-lucide="x" style="width:16px;height:16px"></i>
            </button>
        `;
        container.appendChild(row);
        
        lucide.createIcons();
        updateRemoveButtons(containerId);
    }

    function updateRemoveButtons(containerId) {
        const container = document.getElementById(containerId);
        const rows = container.querySelectorAll('.skill-input-row');
        
        rows.forEach((row, index) => {
            const removeBtn = row.querySelector('.remove-skill');
            if (removeBtn) {
                removeBtn.style.display = rows.length > 1 ? 'flex' : 'none';
            }
        });
    }

    document.getElementById('addRequiredSkill').addEventListener('click', function() {
        addSkillRow('requiredSkillsContainer');
    });

    document.getElementById('addPreferredSkill').addEventListener('click', function() {
        addSkillRow('preferredSkillsContainer');
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-skill')) {
            const row = e.target.closest('.skill-input-row');
            const container = row.parentElement;
            row.remove();
            updateRemoveButtons(container.id);
        }
    });

    // Handle Enter key to add new skill
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.name && (e.target.name.includes('required_skills') || e.target.name.includes('preferred_skills'))) {
            e.preventDefault();
            const containerId = e.target.name.includes('required') ? 'requiredSkillsContainer' : 'preferredSkillsContainer';
            addSkillRow(containerId);
            const inputs = document.querySelectorAll(`#${containerId} input`);
            inputs[inputs.length - 1].focus();
        }
    });
});
</script>
@endpush
