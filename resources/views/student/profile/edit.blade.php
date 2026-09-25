@extends('layouts.student')

@section('title', 'Profil Belajar Santri | AL-HIKMAH')
@section('header', 'Profil Belajar Santri')
@section('subheader', 'Kelola foto avatar, nama panggilan, dan lihat data domisili keluarga tersinkron')

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

    <!-- KPI Summary Row -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Total Poin Belajar</span>
                    <h4 class="fw-bold text-warning mb-0">{{ number_format($totalPoints) }} Pts</h4>
                </div>
                <div class="badge bg-warning-subtle text-warning p-3 rounded-circle fs-4">
                    <i class="bi bi-star-fill"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Streak Istiqomah</span>
                    <h4 class="fw-bold text-danger mb-0">{{ $currentStreak }} Hari</h4>
                </div>
                <div class="badge bg-danger-subtle text-danger p-3 rounded-circle fs-4">
                    <i class="bi bi-fire"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Program Belajar</span>
                    <h5 class="fw-bold text-success mb-0 text-truncate" style="max-width: 140px;">
                        {{ $programs->first()?->name ?? 'Tahfidz Al-Qur\'an' }}
                    </h5>
                </div>
                <div class="badge bg-success-subtle text-success p-3 rounded-circle fs-4">
                    <i class="bi bi-book-half"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Ustadz / Pembimbing</span>
                    <h5 class="fw-bold text-primary mb-0 text-truncate" style="max-width: 140px;">
                        {{ $mentor ? $mentor->getDisplayName() : 'Belum Ditugaskan' }}
                    </h5>
                </div>
                <div class="badge bg-primary-subtle text-primary p-3 rounded-circle fs-4">
                    <i class="bi bi-person-video3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Kolom Kiri: Form Profil Santri -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-person-lines-fill text-success me-2"></i>Data Identitas Santri
                        </h5>
                        <small class="text-muted">Personalisasi nama panggilan dan foto avatar santri.</small>
                    </div>
                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">
                        {{ $student ? $student->age . ' Tahun' : 'Santri' }} ({{ $student?->gender === 'P' ? 'Perempuan' : 'Laki-laki' }})
                    </span>
                </div>

                <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
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
                                   class="position-absolute bottom-0 end-0 bg-success text-white rounded-circle p-2 shadow cursor-pointer d-flex align-items-center justify-content-center"
                                   style="width: 38px; height: 38px; cursor: pointer;"
                                   title="Ubah Foto Avatar">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                            <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/png, image/jpeg, image/webp">
                        </div>
                        <div class="mt-2">
                            <h5 class="fw-bold text-dark mb-0">{{ $user->name }}</h5>
                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 mt-1">
                                <i class="bi bi-mortarboard-fill me-1"></i>Santri Binaan AL-HIKMAH
                            </span>
                        </div>
                        <small class="text-muted mt-1">Format: JPG, PNG, atau WEBP. Maksimal 2MB.</small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Nama Lengkap Santri *</label>
                            <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Nama Panggilan (Nickname)</label>
                            <input type="text" name="nickname" class="form-control rounded-3" value="{{ old('nickname', $student?->nickname) }}" placeholder="Contoh: Ahmad">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Email Belajar (Login)</label>
                            <input type="email" class="form-control rounded-3 bg-light" value="{{ $user->email }}" readonly title="Email santri dikelola oleh sistem">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Nomor Telepon / WhatsApp Santri (Opsional)</label>
                            <input type="text" name="phone" class="form-control rounded-3" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                        </div>
                    </div>

                    <!-- Informasi Domisili & Titik Peta (Sinkron dari Orang Tua) -->
                    <div class="mt-4 pt-4 border-top">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> Alamat & Titik Peta Rumah
                            </h6>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1" style="font-size: 0.72rem;">
                                <i class="bi bi-arrow-repeat me-1"></i> Otomatis Sinkron dari Orang Tua
                            </span>
                        </div>

                        <div class="p-3 bg-light rounded-4 border">
                            <div class="mb-2">
                                <small class="text-muted d-block">Alamat Domisili Keluarga:</small>
                                <div class="small fw-semibold text-dark mt-1">
                                    {{ $student?->effective_address ?? 'Alamat belum diatur oleh orang tua.' }}
                                </div>
                            </div>
                            <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="small text-muted">
                                    <i class="bi bi-shield-lock me-1"></i> Alamat diedit terpusat oleh akun Orang Tua.
                                </div>
                                @if($student?->maps_link)
                                    <a href="{{ $student->maps_link }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold">
                                        <i class="bi bi-pin-map-fill me-1"></i> Buka Titik Peta di Maps
                                        <i class="bi bi-box-arrow-up-right ms-1" style="font-size: 0.72rem;"></i>
                                    </a>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1" style="font-size: 0.72rem;">
                                        Titik peta belum disematkan wali
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-save me-1"></i> Simpan Profil Santri
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kolom Kanan: Keamanan Password Santri -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="border-bottom pb-3 mb-4">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-shield-lock-fill text-warning me-2"></i>Keamanan Password Santri
                    </h5>
                    <small class="text-muted">Ganti password secara berkala agar akun belajar tetap aman dan privat.</small>
                </div>

                <form action="{{ route('student.profile.password') }}" method="POST">
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
                        <i class="bi bi-info-circle-fill text-success me-1"></i>
                        Jika Anda masih menggunakan password bawaan <code>santri123</code>, segera ganti dengan password rahasia yang mudah Anda ingat.
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm text-dark w-100">
                            <i class="bi bi-lock-fill me-1"></i> Simpan Password Baru
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
