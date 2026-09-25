@extends('layouts.student')

@section('title', 'Ganti Password Santri')
@section('header', 'Keamanan Akun & Password')
@section('subheader', 'Perbarui password akun Anda secara berkala demi keamanan ruang belajar.')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="text-center mb-4">
                <div class="rounded-circle bg-secondary-subtle text-secondary mx-auto d-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px;">
                    <i class="bi bi-shield-lock-fill fs-2"></i>
                </div>
                <h5 class="fw-bold mb-1">Ganti Password Akun</h5>
                <p class="text-muted small">Pastikan menggunakan kombinasi password yang kuat dan mudah Anda ingat.</p>
            </div>

            <form action="{{ route('student.password.reset') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password Saat Ini</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-key"></i></span>
                        <input type="password" name="current_password" class="form-control border-start-0" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password Baru</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                        <input type="password" name="new_password" class="form-control border-start-0" placeholder="Minimal 8 karakter kuat" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Konfirmasi Password Baru</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-check"></i></span>
                        <input type="password" name="new_password_confirmation" class="form-control border-start-0" placeholder="Ulangi password baru" required>
                    </div>
                </div>

                <div class="p-3 rounded-3 bg-light-subtle border mb-4">
                    <small class="fw-bold d-block text-secondary mb-1"><i class="bi bi-info-circle me-1"></i> Kebijakan Keamanan Password:</small>
                    <ul class="text-muted small mb-0 ps-3" style="font-size: 0.75rem;">
                        <li>Minimal 8 karakter</li>
                        <li>Mengandung huruf besar (A-Z) & huruf kecil (a-z)</li>
                        <li>Mengandung angka (0-9)</li>
                        <li>Mengandung simbol khusus (contoh: <code>!@#$%^&*</code>)</li>
                    </ul>
                </div>

                <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-semibold">
                    <i class="bi bi-check2-circle me-1"></i> Perbarui Password Saya
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
