@extends('layouts.admin')

@section('title', 'Pengaturan Profil Admin')
@section('header', 'Pengaturan Profil Administrator')
@section('subheader', 'Kelola identitas resmi pengelola dan keamanan akun platform')

@section('content')
<div class="container-fluid p-0">
    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                <strong class="text-danger">Terdapat kesalahan pengisian formulir:</strong>
            </div>
            <ul class="mb-0 small ps-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Kolom Kiri: Form Identitas & Avatar Profil -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-person-gear text-primary me-2"></i>Data Identitas Administrator
                    </h5>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1">
                        <i class="bi bi-shield-check me-1"></i>Hak Akses Penuh
                    </span>
                </div>

                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Avatar Uploader Component -->
                    <div class="d-flex flex-column align-items-center text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <img id="avatarPreview" 
                                 src="{{ $user->avatar_url }}" 
                                 alt="Avatar {{ $user->name }}" 
                                 class="rounded-circle shadow-sm border border-3 border-success-subtle object-fit-cover"
                                 style="width: 120px; height: 120px;">
                            <label for="avatarInput" 
                                   class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 shadow cursor-pointer d-flex align-items-center justify-content-center"
                                   style="width: 38px; height: 38px; cursor: pointer;"
                                   title="Ubah Foto Profil">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                            <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/png, image/jpeg, image/webp">
                        </div>
                        <div class="mt-2">
                            <h5 class="fw-bold text-dark mb-0">{{ $user->name }}</h5>
                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 mt-1">
                                {{ $user->role?->label ?? 'Administrator' }}
                            </span>
                        </div>
                        <small class="text-muted mt-1">Format: JPG, PNG, atau WEBP. Maksimal 2MB.</small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-secondary">Nama Lengkap Penanggung Jawab *</label>
                            <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Email Akun Admin *</label>
                            <input type="email" name="email" class="form-control rounded-3" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Nomor Telepon / WhatsApp</label>
                            <input type="text" name="phone" class="form-control rounded-3" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kolom Kanan: Keamanan Akun & Ganti Password -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="border-bottom pb-3 mb-4">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-shield-lock-fill text-warning me-2"></i>Keamanan Akun
                    </h5>
                    <small class="text-muted">Perbarui kata sandi akun administrator secara berkala demi keamanan platform.</small>
                </div>

                <form action="{{ route('admin.profile.password') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Password Saat Ini *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key"></i></span>
                            <input type="password" name="current_password" class="form-control border-start-0 rounded-end-3" required placeholder="Masukkan password lama">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Password Baru *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control border-start-0 rounded-end-3" required placeholder="Minimal 8 karakter">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-secondary">Konfirmasi Password Baru *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-check2-circle"></i></span>
                            <input type="password" name="password_confirmation" class="form-control border-start-0 rounded-end-3" required placeholder="Ulangi password baru">
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 small text-muted mb-4 border">
                        <i class="bi bi-info-circle-fill text-primary me-1"></i>
                        Gunakan kombinasi minimal 8 karakter dengan huruf besar, huruf kecil, dan angka.
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm text-dark w-100">
                            <i class="bi bi-lock-fill me-1"></i> Perbarui Password Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('avatarInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran berkas foto maksimal adalah 2MB!');
                this.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('avatarPreview').src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
