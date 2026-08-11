@extends('layouts.auth-layout')

@section('title', 'AL-HIKMAH | Pendaftaran Akun')
@section('subtitle', 'Daftar Akun Baru Belajar Al-Qur\'an')

@section('auth-content')
<form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
    @csrf

    <!-- Role Selection (Orang Tua vs Santri) -->
    <div class="mb-3">
        <label class="form-label fw-semibold text-secondary">Daftar Sebagai</label>
        <div class="row g-2">
            <div class="col-6">
                <input type="radio" class="btn-check" name="role" id="roleParent" value="parent" {{ old('role', 'parent') === 'parent' ? 'checked' : '' }}>
                <label class="btn btn-outline-success w-100 py-2 d-flex flex-column align-items-center rounded-3" for="roleParent">
                    <i class="bi bi-people-fill fs-5 mb-1"></i>
                    <span class="small fw-bold">Orang Tua / Wali</span>
                </label>
            </div>
            <div class="col-6">
                <input type="radio" class="btn-check" name="role" id="roleStudent" value="student" {{ old('role') === 'student' ? 'checked' : '' }}>
                <label class="btn btn-outline-success w-100 py-2 d-flex flex-column align-items-center rounded-3" for="roleStudent">
                    <i class="bi bi-person-workspace fs-5 mb-1"></i>
                    <span class="small fw-bold">Murid / Santri</span>
                </label>
            </div>
        </div>
        @error('role')
            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Nama Lengkap -->
    <div class="mb-3">
        <label for="name" class="form-label fw-semibold text-secondary">Nama Lengkap</label>
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-person"></i></span>
            <input type="text" name="name" id="name" 
                   class="form-control border-start-0 @error('name') is-invalid @enderror" 
                   value="{{ old('name') }}" required autofocus placeholder="Nama lengkap Anda">
        </div>
        @error('name')
            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Email -->
    <div class="mb-3">
        <label for="email" class="form-label fw-semibold text-secondary">Alamat Email</label>
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" id="email" 
                   class="form-control border-start-0 @error('email') is-invalid @enderror" 
                   value="{{ old('email') }}" required placeholder="nama@email.com">
        </div>
        @error('email')
            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- No. Telepon / WA (Optional) -->
    <div class="mb-3">
        <label for="phone" class="form-label fw-semibold text-secondary">No. WhatsApp / HP</label>
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-whatsapp"></i></span>
            <input type="text" name="phone" id="phone" 
                   class="form-control border-start-0 @error('phone') is-invalid @enderror" 
                   value="{{ old('phone') }}" placeholder="08123456789">
        </div>
        @error('phone')
            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Kata Sandi -->
    <div class="mb-3">
        <label for="password" class="form-label fw-semibold text-secondary">Kata Sandi</label>
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" id="password" 
                   class="form-control border-start-0 @error('password') is-invalid @enderror" 
                   required placeholder="Minimal 8 karakter">
        </div>
        @error('password')
            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Konfirmasi Kata Sandi -->
    <div class="mb-4">
        <label for="password_confirmation" class="form-label fw-semibold text-secondary">Konfirmasi Kata Sandi</label>
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-shield-check"></i></span>
            <input type="password" name="password_confirmation" id="password_confirmation" 
                   class="form-control border-start-0" 
                   required placeholder="Ulangi kata sandi">
        </div>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-daftar w-100 py-2.5 justify-content-center mb-3">
        <i class="bi bi-person-plus me-2"></i> Daftar Akun Sekarang
    </button>

    <!-- Navigation to Mentor Registration -->
    <div class="p-3 bg-light rounded-3 text-center mb-3 border">
        <span class="small text-muted d-block mb-1">Ingin mendaftar sebagai Pengajar / Guru Al-Qur'an?</span>
        <a href="{{ route('bergabung') }}" class="fw-bold text-success text-decoration-none small">
            <i class="bi bi-person-badge me-1"></i> Daftar Sebagai Pendamping di Sini
        </a>
    </div>

    <!-- Navigation to Login -->
    <div class="text-center text-secondary small">
        Sudah memiliki akun? 
        <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none ms-1">Masuk di Sini</a>
    </div>
</form>
@endsection
