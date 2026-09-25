@extends('layouts.mentor')

@section('title', 'Profil & Rekening Guru | AL-HIKMAH')
@section('header', 'Pengaturan Profil & Rekening Guru')
@section('subheader', 'Kelola biodata ustadz/ustazah, kualifikasi pengajaran, berkas sanad, dan rekening pencairan honor bimbingan')

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

    @php
        $cvDoc = $mentor?->getCvDocument();
        $certDoc = $mentor?->getCertificateDocument();
        $app = $mentor?->application;
    @endphp

    <div class="row g-4">
        <!-- Kolom Kiri: Profil, Portofolio Rekrutmen & Rekening Bank -->
        <div class="col-lg-8">
            <form action="{{ route('mentor.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- CARD 1: IDENTITAS UTAMA & AVATAR -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4 flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">
                                <i class="bi bi-person-badge text-success me-2"></i>Identitas & Akun Guru Pembimbing
                            </h5>
                            <small class="text-muted">Sinkronisasi data identitas resmi sesuai portal pendaftaran AL-HIKMAH LMS.</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if($mentor?->status === 'probation')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1">
                                    <i class="bi bi-hourglass-split me-1"></i>Masa Orientasi (Probation)
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                    <i class="bi bi-check-circle-fill me-1"></i>Guru Terdaftar Aktif
                                </span>
                            @endif
                            <span class="badge bg-light text-dark border rounded-pill px-3 py-1 fw-semibold">
                                ⭐ {{ number_format($mentor?->rating ?? 5.0, 1) }} / 5.0
                            </span>
                        </div>
                    </div>

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
                            <div class="d-flex align-items-center justify-content-center gap-2 mt-1 flex-wrap">
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">
                                    <i class="bi bi-mortarboard-fill me-1"></i>Guru / Mentor Pembimbing
                                </span>
                                @if($app?->application_code)
                                    <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-qr-code me-1"></i>{{ $app->application_code }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <small class="text-muted mt-1">Format: JPG, PNG, atau WEBP. Ukuran berkas maksimal 2MB.</small>
                    </div>

                    <!-- Formulir Data Diri -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $user->name) }}" placeholder="Contoh: Ustadz Ahmad Fauzi, S.Pd.I" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Alamat Email Login <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control rounded-3" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Nomor WhatsApp Aktif <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control rounded-3" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-secondary">Tanggal Lahir</label>
                            <input type="date" name="birth_date" class="form-control rounded-3" value="{{ old('birth_date', $mentor?->birth_date ? \Carbon\Carbon::parse($mentor->birth_date)->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-secondary">Jenis Kelamin</label>
                            @php
                                $genderVal = old('gender', $mentor?->gender ?? ($app?->gender === 'female' ? 'P' : 'L'));
                            @endphp
                            <select name="gender" class="form-select rounded-3">
                                <option value="L" {{ in_array($genderVal, ['L', 'male']) ? 'selected' : '' }}>Laki-laki (Ikhwan)</option>
                                <option value="P" {{ in_array($genderVal, ['P', 'female']) ? 'selected' : '' }}>Perempuan (Akhwat)</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small text-secondary">Alamat Domisili Tinggal</label>
                            <input type="text" name="address" class="form-control rounded-3" value="{{ old('address', $mentor?->address) }}" placeholder="Nama Jalan, RT/RW, Kelurahan, Kecamatan">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-secondary">Kota / Kabupaten</label>
                            <input type="text" name="city" class="form-control rounded-3" value="{{ old('city', $mentor?->city) }}" placeholder="Contoh: Jakarta Selatan">
                        </div>
                    </div>
                </div>

                <!-- CARD 2: KUALIFIKASI PENDIDIKAN, HAFALAN & PORTOFOLIO BIMBINGAN -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                    <div class="border-bottom pb-3 mb-4">
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-book-half text-primary me-2"></i>Kualifikasi Pendidikan, Hafalan & Portofolio
                        </h5>
                        <small class="text-muted">Data kompetensi mengajar Al-Qur'an yang tersinkronisasi dari formulir pendaftaran guru.</small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Pendidikan Terakhir</label>
                            <input type="text" name="education" class="form-control rounded-3" value="{{ old('education', $mentor?->education) }}" placeholder="Contoh: S1 Ilmu Al-Qur'an dan Tafsir">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Institusi / Ma'had / Kampus</label>
                            <input type="text" name="institution" class="form-control rounded-3" value="{{ old('institution', $mentor?->institution) }}" placeholder="Contoh: PTIQ Jakarta / LIPIA / UIN">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Peminatan Spesialisasi Bimbingan <span class="text-danger">*</span></label>
                            @php
                                $specVal = old('specialization', $mentor?->specialization ?? 'Tahfidz');
                            @endphp
                            <select name="specialization" class="form-select rounded-3" required>
                                <option value="Tahfidz" {{ str_contains($specVal, 'Tahfidz') ? 'selected' : '' }}>Tahfidz Al-Qur'an (Hafalan 30 Juz)</option>
                                <option value="Tahsin" {{ str_contains($specVal, 'Tahsin') ? 'selected' : '' }}>Tahsin & Matan Tajwid (Kaidah Bacaan)</option>
                                <option value="Iqra" {{ str_contains($specVal, 'Iqra') ? 'selected' : '' }}>Iqra' & Pra-Tahfidz Anak</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-secondary">Hafalan Terverifikasi (Juz)</label>
                            <input type="number" name="hifz_total_juz" class="form-control rounded-3" min="0" max="30" value="{{ old('hifz_total_juz', $mentor?->hifz_total_juz ?? 0) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-secondary">Pengalaman Mengajar (Thn)</label>
                            <input type="number" name="experience_years" class="form-control rounded-3" min="0" max="50" value="{{ old('experience_years', $mentor?->experience_years ?? 0) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-secondary">Biografi / Deskripsi Pengalaman Mengajar</label>
                            <textarea name="bio" class="form-control rounded-3" rows="3" placeholder="Sebutkan lembaga/TPQ tempat pernah mengajar dan metode bimbingan...">{{ old('bio', $mentor?->bio) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-secondary">Silsilah Sanad Al-Qur'an (Jika Memiliki)</label>
                            <textarea name="sanad_chain" class="form-control rounded-3" rows="2" placeholder="Contoh: Sanad Qira'at 'Ashim Riwayat Hafsh Thariq Asy-Syathibiyyah melalui Syaikh...">{{ old('sanad_chain', $mentor?->sanad_chain) }}</textarea>
                            <small class="text-muted" style="font-size: 0.72rem;">Kosongkan jika belum memiliki sanad muttashil resmi.</small>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: BERKAS DOKUMEN PERSYARATAN (CV & SERTIFIKAT SANAD) -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                    <div class="border-bottom pb-3 mb-4">
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-file-earmark-pdf text-danger me-2"></i>Berkas Dokumen Rekrutmen & Syahadah
                        </h5>
                        <small class="text-muted">Dokumen portofolio resmi yang diunggah saat pendaftaran guru di AL-HIKMAH LMS.</small>
                    </div>

                    <div class="row g-3">
                        <!-- Dokumen CV -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 border h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label class="form-label fw-semibold small text-danger mb-0">
                                            <i class="bi bi-file-pdf me-1"></i>Curriculum Vitae (CV)
                                        </label>
                                        @if($cvDoc)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.68rem;">
                                                <i class="bi bi-check-circle me-1"></i>Tersedia
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 0.68rem;">
                                                Belum Ada Berkas
                                            </span>
                                        @endif
                                    </div>

                                    @if($cvDoc)
                                        <div class="p-2.5 bg-white rounded-3 border mb-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="overflow-hidden me-2">
                                                    <div class="small fw-semibold text-truncate text-dark" title="{{ $cvDoc->file_name }}">
                                                        {{ $cvDoc->file_name }}
                                                    </div>
                                                    <small class="text-muted" style="font-size: 0.7rem;">
                                                        Ukuran: {{ number_format($cvDoc->file_size ?? 0, 1) }} KB &bull; {{ $cvDoc->created_at?->format('d/m/Y') }}
                                                    </small>
                                                </div>
                                                <a href="{{ route('mentor.profile.document.download', $cvDoc->id) }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 text-nowrap"
                                                   style="font-size: 0.75rem;" title="Buka / Unduh Berkas CV">
                                                    <i class="bi bi-download me-1"></i>Unduh
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <label class="form-label small text-muted mb-1">{{ $cvDoc ? 'Unggah Pembaruan CV (Opsional)' : 'Unggah Berkas CV *' }}</label>
                                    <input type="file" name="cv" class="form-control form-control-sm rounded-3" accept=".pdf">
                                    <small class="text-muted d-block mt-1" style="font-size: 0.68rem;">Format: PDF. Maksimal 2MB.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Dokumen Sertifikat -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 border h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label class="form-label fw-semibold small text-primary mb-0">
                                            <i class="bi bi-award me-1"></i>Sertifikat / Syahadah Sanad
                                        </label>
                                        @if($certDoc)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.68rem;">
                                                <i class="bi bi-check-circle me-1"></i>Tersedia
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 0.68rem;">
                                                Belum Ada Berkas
                                            </span>
                                        @endif
                                    </div>

                                    @if($certDoc)
                                        <div class="p-2.5 bg-white rounded-3 border mb-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="overflow-hidden me-2">
                                                    <div class="small fw-semibold text-truncate text-dark" title="{{ $certDoc->file_name }}">
                                                        {{ $certDoc->file_name }}
                                                    </div>
                                                    <small class="text-muted" style="font-size: 0.7rem;">
                                                        Ukuran: {{ number_format($certDoc->file_size ?? 0, 1) }} KB &bull; {{ $certDoc->created_at?->format('d/m/Y') }}
                                                    </small>
                                                </div>
                                                <a href="{{ route('mentor.profile.document.download', $certDoc->id) }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 text-nowrap"
                                                   style="font-size: 0.75rem;" title="Buka / Unduh Berkas Sertifikat">
                                                    <i class="bi bi-download me-1"></i>Unduh
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <label class="form-label small text-muted mb-1">{{ $certDoc ? 'Unggah Pembaruan Sertifikat' : 'Unggah Berkas Sertifikat (Opsional)' }}</label>
                                    <input type="file" name="certificate" class="form-control form-control-sm rounded-3" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted d-block mt-1" style="font-size: 0.68rem;">Format: PDF, JPG, PNG. Maksimal 2MB.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 4: REKENING BANK PENCAIRAN HONOR -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3 flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">
                                <i class="bi bi-bank2 text-primary me-2"></i>Rekening Pencairan Honor Bimbingan
                            </h5>
                            <small class="text-muted">Honor bimbingan privat akan otomatis ditransfer ke rekening di bawah ini.</small>
                        </div>
                        <span class="badge bg-info-subtle text-info rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.75rem;">
                            <i class="bi bi-lightning-charge-fill me-1"></i>Input Mandiri Langsung Aktif
                        </span>
                    </div>

                    <div class="p-3 bg-light rounded-4 border mb-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-secondary">Nama Bank / E-Wallet</label>
                                <input type="text" name="bank_name" class="form-control rounded-3" value="{{ old('bank_name', $mentor?->bank_name) }}" placeholder="BSI / BCA / Mandiri / BRI">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-secondary">Nomor Rekening</label>
                                <input type="text" name="bank_account_number" class="form-control rounded-3" value="{{ old('bank_account_number', $mentor?->bank_account_number) }}" placeholder="Contoh: 7123456789">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-secondary">Atas Nama Pemilik Rekening</label>
                                <input type="text" name="bank_account_name" class="form-control rounded-3" value="{{ old('bank_account_name', $mentor?->bank_account_name) }}" placeholder="Nama sesuai buku tabungan">
                            </div>
                        </div>
                        <div class="small text-muted mt-2">
                            <i class="bi bi-info-circle me-1"></i> Anda dapat memperbarui data rekening kapan saja tanpa perlu menunggu konfirmasi admin.
                        </div>
                    </div>

                    <div class="text-end pt-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-save me-1"></i> Simpan Seluruh Perubahan Profil & Portofolio
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Kolom Kanan: Status Rekrutmen, Keamanan Password & Log Aktivitas -->
        <div class="col-lg-4">
            <!-- Status Lamaran Rekrutmen (Jika Ada) -->
            @if($app)
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                    <h6 class="fw-bold text-dark border-bottom pb-3 mb-3 d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-clipboard-check-fill text-success me-2"></i>Status Pendaftaran Guru</span>
                        {!! $app->status_badge !!}
                    </h6>
                    <div class="small">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Kode Registrasi</span>
                            <span class="fw-bold font-monospace">{{ $app->application_code }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Tanggal Pendaftaran</span>
                            <span>{{ $app->submitted_at ? $app->submitted_at->format('d M Y') : '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Tahap Saat Ini</span>
                            <span class="fw-semibold text-primary">Tahap {{ $app->current_stage }} / 4</span>
                        </div>
                        @if($app->final_score)
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Skor Kelulusan</span>
                                <span class="badge bg-success-subtle text-success">{{ number_format($app->final_score, 1) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Ubah Password -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-3 mb-3">
                    <i class="bi bi-key-fill text-warning me-2"></i>Ubah Password Akun
                </h5>
                <form action="{{ route('mentor.profile.password') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Password Lama <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control rounded-3" required placeholder="Masukkan password saat ini">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control rounded-3" required placeholder="Minimal 8 karakter">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control rounded-3" required placeholder="Ulangi password baru">
                    </div>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm text-dark w-100 mt-2">
                        <i class="bi bi-lock-fill me-1"></i> Update Password
                    </button>
                </form>
            </div>

            <!-- Aktivitas Terkini -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h6 class="fw-bold text-dark border-bottom pb-3 mb-3">
                    <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Aktivitas Guru
                </h6>
                @if($recentActivities->isEmpty())
                    <p class="text-muted small mb-0">Belum ada rekam jejak aktivitas operasional.</p>
                @else
                    <div class="d-flex flex-column gap-2">
                        @foreach($recentActivities->take(5) as $activity)
                            <div class="p-2 rounded-3 bg-light border small">
                                <div class="fw-semibold text-dark">{{ $activity->activity ?? 'Aktivitas' }}</div>
                                <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $activity->created_at?->diffForHumans() }}</small>
                            </div>
                        @endforeach
                    </div>
                @endif
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
