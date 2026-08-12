@extends('layouts.parent')

@section('title', 'Profil & Pengaturan Akun')
@section('header', 'Profil Saya')
@section('subheader', 'Kelola informasi diri dan kontak wali santri')

@section('content')
<div class="container-fluid p-0">
    <div class="row g-4">
        <!-- Submenu Profil -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                <div class="nav flex-column nav-pills">
                    <a href="{{ route('parent.profile.edit') }}" class="nav-link active rounded-pill mb-1">
                        <i class="bi bi-person-gear me-2"></i> Edit Profil Diri
                    </a>
                    <a href="{{ route('parent.profile.notifications') }}" class="nav-link text-dark rounded-pill mb-1">
                        <i class="bi bi-bell me-2"></i> Preferensi Notifikasi
                    </a>
                    <a href="{{ route('parent.profile.children') }}" class="nav-link text-dark rounded-pill mb-1">
                        <i class="bi bi-people me-2"></i> Kelola Data Anak
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Form -->
        <div class="col-lg-9">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-3 mb-3">Informasi Utama Wali Santri</h5>
                <form action="{{ route('parent.profile.update') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Alamat Email *</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor Darurat / WhatsApp</label>
                            <input type="text" name="emergency_phone" class="form-control" value="{{ old('emergency_phone', $parent?->emergency_phone) }}" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Alamat Tempat Tinggal</label>
                            <textarea name="address" class="form-control" rows="3">{{ old('address', $parent?->address) }}</textarea>
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>

            <!-- Form Ubah Password -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h5 class="fw-bold text-dark border-bottom pb-3 mb-3"><i class="bi bi-key text-warning me-2"></i>Ubah Password Akun</h5>
                <form action="{{ route('parent.profile.password') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Password Lama *</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Password Baru *</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Konfirmasi Password Baru *</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm text-dark">
                            <i class="bi bi-lock me-1"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
