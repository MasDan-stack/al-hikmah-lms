@extends('layouts.auth-layout')

@section('title', 'AL-HIKMAH | Lupa Kata Sandi')
@section('subtitle', 'Pemulihan Kata Sandi Akun')

@section('auth-content')
@if (session('status'))
    <div class="alert alert-success border-0 shadow-sm mb-4 small" role="alert">
        {{ session('status') }}
    </div>
@endif

<p class="text-secondary small mb-4">
    Lupa kata sandi Anda? Masukkan alamat email yang terdaftar, kami akan mengirimkan tautan pemulihan kata sandi.
</p>

<form method="POST" action="{{ route('password.email') }}" class="needs-validation" novalidate>
    @csrf

    <div class="mb-4">
        <label for="email" class="form-label fw-semibold text-secondary">Alamat Email</label>
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" id="email" 
                   class="form-control border-start-0 @error('email') is-invalid @enderror" 
                   value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
        </div>
        @error('email')
            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-daftar w-100 py-2.5 justify-content-center mb-3">
        <i class="bi bi-send me-2"></i> Kirim Tautan Pemulihan
    </button>

    <div class="text-center text-secondary small mt-4">
        Sudah ingat kata sandi? 
        <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none ms-1">Kembali ke Login</a>
    </div>
</form>
@endsection
