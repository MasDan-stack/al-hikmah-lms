@extends('layouts.auth-layout')

@section('title', 'Masuk ke Akun | AL-HIKMAH LMS')
@section('subtitle', 'Masuk ke Akun AL-HIKMAH Anda')

@section('auth-content')
@if (session('status'))
    <div class="alert alert-success border-0 shadow-sm mb-4 rounded-3" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
    @csrf

    <!-- Email Field -->
    <div class="mb-3">
        <label for="email" class="form-label fw-semibold text-secondary small">Alamat Email</label>
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" id="email" 
                   class="form-control border-start-0 @error('email') is-invalid @enderror" 
                   value="{{ old('email') }}" required autofocus placeholder="nama@email.com"
                   autocomplete="email">
        </div>
        @error('email')
            <div class="invalid-feedback d-block mt-1 small">{{ $message }}</div>
        @enderror
    </div>

    <!-- Password Field -->
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="form-label fw-semibold text-secondary small mb-0">Kata Sandi</label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-success text-decoration-none fw-medium" style="font-size: 0.8rem;">Lupa kata sandi?</a>
            @endif
        </div>
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" id="password" 
                   class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror" 
                   required placeholder="••••••••" autocomplete="current-password">
            <button type="button" class="btn-password-toggle rounded-end" aria-label="Tampilkan atau sembunyikan kata sandi">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        @error('password')
            <div class="invalid-feedback d-block mt-1 small">{{ $message }}</div>
        @enderror
    </div>

    <!-- Remember Me -->
    <div class="form-check mb-4">
        <input type="checkbox" name="remember" id="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
        <label for="remember" class="form-check-label text-secondary small">Ingat saya di perangkat ini</label>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-daftar w-100 py-2.5 justify-content-center mb-3">
        <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Sekarang
    </button>

    <!-- Navigation to Register -->
    <div class="text-center text-secondary small mt-4 pt-2 border-top">
        Belum memiliki akun santri atau wali? 
        <a href="{{ route('register') }}" class="text-success fw-bold text-decoration-none ms-1">Daftar Akun Baru</a>
    </div>
</form>
@endsection
