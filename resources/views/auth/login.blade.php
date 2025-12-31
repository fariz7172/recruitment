@extends('layouts.app')

@section('title', 'Login - ScreeningAI')

@section('content')
<div class="flex justify-center items-center py-12 px-4">
    <div class="card" style="width: 100%; max-width: 400px;">
        <div class="card-header text-center">
            <h1 class="mb-2">Login</h1>
            <p class="text-muted">Masuk ke akun Anda</p>
        </div>
        <div class="card-body">
            <form action="{{ route('login.authenticate') }}" method="POST">
                @csrf

                <div class="form-group mb-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" required autofocus placeholder="email@example.com">
                    @error('email')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                </div>

                <button type="submit" class="btn btn-primary w-full shadow-md">
                    <i data-lucide="log-in" style="width:18px;height:18px;margin-right:8px;"></i>
                    Masuk
                </button>
            </form>
        </div>
        <div class="p-4 text-center border-t text-sm text-muted">
            <p>Belum punya akun? Lamar pekerjaan dulu!</p>
            <a href="{{ route('jobs.public') }}" class="text-primary-600 font-semibold hover:underline">Lihat Lowongan</a>
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
