@extends('layouts.parent')

@section('title', 'Profil & Pengaturan Akun')
@section('header', 'Profil Saya')
@section('subheader', 'Kelola informasi diri, kontak darurat, dan tautan titik peta rumah keluarga')

@section('content')
<div class="container-fluid p-0">
    <div class="row g-4">
        <!-- Submenu Profil -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="nav flex-column nav-pills gap-1">
                    <a href="{{ route('parent.profile.edit') }}" class="nav-link rounded-pill {{ request()->routeIs('parent.profile.edit') ? 'active' : '' }}">
                        <i class="bi bi-person-gear me-2"></i> Edit Profil Diri
                    </a>
                    <a href="{{ route('parent.profile.notifications') }}" class="nav-link rounded-pill {{ request()->routeIs('parent.profile.notifications') ? 'active' : '' }}">
                        <i class="bi bi-bell me-2"></i> Preferensi Notifikasi
                    </a>
                    <a href="{{ route('parent.profile.children') }}" class="nav-link rounded-pill {{ request()->routeIs('parent.profile.children') ? 'active' : '' }}">
                        <i class="bi bi-people me-2"></i> Kelola Data Anak
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Form -->
        <div class="col-lg-9">
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

            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-person-vcard-fill text-primary me-2"></i>Informasi Utama Wali Santri
                    </h5>
                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">
                        <i class="bi bi-house-heart-fill me-1"></i>Pusat Data Keluarga
                    </span>
                </div>

                <form action="{{ route('parent.profile.update') }}" method="POST" enctype="multipart/form-data">
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
                                <i class="bi bi-people-fill me-1"></i>Orang Tua / Wali Santri
                            </span>
                        </div>
                        <small class="text-muted mt-1">Format: JPG, PNG, atau WEBP. Maksimal 2MB.</small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Nama Lengkap Wali *</label>
                            <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Alamat Email Login *</label>
                            <input type="email" name="email" class="form-control rounded-3" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-secondary">Nomor WhatsApp / Kontak Darurat</label>
                            <input type="text" name="emergency_phone" class="form-control rounded-3" value="{{ old('emergency_phone', $parent?->emergency_phone ?? $user->phone) }}" placeholder="Contoh: 081234567890">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-secondary">Alamat Lengkap Tempat Tinggal</label>
                            <textarea name="address" class="form-control rounded-3" rows="3" placeholder="Masukkan nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan">{{ old('address', $parent?->address) }}</textarea>
                        </div>

                        <!-- Komponen Input Link Share Lokasi Fleksibel -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> Tautan Share Lokasi Rumah (Google Maps / Waze / Apple Maps)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="bi bi-link-45deg"></i>
                                </span>
                                <input type="url" 
                                       name="maps_link" 
                                       id="mapsLinkInput"
                                       class="form-control border-start-0 ps-0" 
                                       placeholder="Contoh: https://maps.app.goo.gl/... atau https://waze.com/ul/..."
                                       value="{{ old('maps_link', $parent?->maps_link) }}">
                                <button type="button" 
                                        class="btn btn-outline-success px-3" 
                                        id="btnTestMap"
                                        onclick="testMapLink()">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Uji Titik Peta
                                </button>
                            </div>
                            <div class="form-text small text-muted mt-2">
                                <i class="bi bi-info-circle-fill text-success me-1"></i> <strong>Satu link untuk sekeluarga:</strong> Tautan ini otomatis tersinkron ke seluruh profil anak/santri Anda dan digunakan Ustadz/Ustazah pembimbing untuk navigasi kunjungan tatap muka (Home Visit).
                            </div>
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
                <h5 class="fw-bold text-dark border-bottom pb-3 mb-3"><i class="bi bi-key-fill text-warning me-2"></i>Ubah Password Akun</h5>
                <form action="{{ route('parent.profile.password') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-secondary">Password Lama *</label>
                            <input type="password" name="current_password" class="form-control rounded-3" required placeholder="Password saat ini">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-secondary">Password Baru *</label>
                            <input type="password" name="password" class="form-control rounded-3" required placeholder="Minimal 8 karakter">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-secondary">Konfirmasi Password *</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3" required placeholder="Ulangi password baru">
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm text-dark">
                            <i class="bi bi-lock-fill me-1"></i> Update Password
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
    // Instant Avatar Preview
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

    // Test Map Link Opener
    function testMapLink() {
        const url = document.getElementById('mapsLinkInput')?.value.trim();
        if (!url) {
            alert('Silakan masukkan tautan share lokasi peta terlebih dahulu.');
            return;
        }
        if (!url.startsWith('http://') && !url.startsWith('https://')) {
            alert('Tautan peta harus diawali dengan http:// atau https://');
            return;
        }
        window.open(url, '_blank');
    }
</script>
@endpush
