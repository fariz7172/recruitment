@extends('layouts.app')

@section('title', 'Kelola Lowongan - ScreeningAI')

@section('content')
<div class="flex justify-between items-center mb-6 flex-wrap gap-4">
    <div>
        <h1 class="mb-2">Lowongan Kerja</h1>
        <p class="text-muted">Kelola semua lowongan pekerjaan</p>
    </div>
    <a href="{{ route('jobs.create') }}" class="btn btn-primary">
        <i data-lucide="plus" style="width:18px;height:18px"></i>
        Buat Lowongan
    </a>
</div>

<!-- Filters -->
<div class="card mb-6">
    <div class="card-body">
        <form action="{{ route('jobs.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="form-group mb-0" style="flex: 1; min-width: 200px;">
                <label class="form-label">Cari</label>
                <input type="text" name="search" class="form-control" placeholder="Cari judul atau departemen..." value="{{ request('search') }}">
            </div>
            <div class="form-group mb-0" style="min-width: 150px;">
                <label class="form-label">Status</label>
                <select name="status" class="form-control form-select">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Ditutup</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">
                <i data-lucide="search" style="width:16px;height:16px"></i>
                Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('jobs.index') }}" class="btn btn-secondary">
                <i data-lucide="x" style="width:16px;height:16px"></i>
                Reset
            </a>
            @endif
        </form>
    </div>
</div>

<!-- Jobs Table -->
<div class="card">
    @if($jobs->count() > 0)
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Posisi</th>
                    <th>Departemen</th>
                    <th>Lamaran</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobs as $job)
                <tr>
                    <td>
                        <div class="font-semibold">{{ $job->title }}</div>
                        <div class="text-sm text-muted">{{ $job->location ?? 'Remote' }}</div>
                    </td>
                    <td>{{ $job->department ?? '-' }}</td>
                    <td>
                        <span class="badge badge-primary">{{ $job->applications_count }} lamaran</span>
                    </td>
                    <td>
                        @php
                            $statusColors = [
                                'active' => 'success',
                                'closed' => 'danger',
                                'draft' => 'warning'
                            ];
                            $statusLabels = [
                                'active' => 'Aktif',
                                'closed' => 'Ditutup',
                                'draft' => 'Draft'
                            ];
                        @endphp
                        <span class="badge badge-{{ $statusColors[$job->status] ?? 'secondary' }}">
                            {{ $statusLabels[$job->status] ?? $job->status }}
                        </span>
                    </td>
                    <td class="text-sm text-muted">
                        {{ $job->deadline ? $job->deadline->format('d M Y') : '-' }}
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('jobs.show', $job) }}" class="btn btn-sm btn-secondary" title="Lihat">
                                <i data-lucide="eye" style="width:14px;height:14px"></i>
                            </a>
                            <a href="{{ route('jobs.edit', $job) }}" class="btn btn-sm btn-secondary" title="Edit">
                                <i data-lucide="pencil" style="width:14px;height:14px"></i>
                            </a>
                            <form action="{{ route('jobs.destroy', $job) }}" method="POST" onsubmit="return confirm('Yakin hapus lowongan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
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
        {{ $jobs->withQueryString()->links() }}
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon">
            <i data-lucide="briefcase"></i>
        </div>
        <h4 class="empty-state-title">Belum Ada Lowongan</h4>
        <p class="empty-state-text">Buat lowongan pertama untuk mulai menerima lamaran</p>
        <a href="{{ route('jobs.create') }}" class="btn btn-primary">
            <i data-lucide="plus"></i>
            Buat Lowongan
        </a>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
