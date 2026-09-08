@extends('layouts.landing')

@section('title', 'Pendaftaran Guru Pembimbing Al-Qur\'an - AL-HIKMAH LMS')
@section('meta_description', 'Bergabunglah menjadi guru pembimbing Al-Qur\'an di AL-HIKMAH LMS. Dapatkan fleksibilitas waktu, insentif syar\'i yang transparan, dan ruang dakwah yang berkah.')

@section('content')
<div class="mentor-split-wrapper section-alt" style="padding-top: 110px; padding-bottom: 70px;">
    <div class="container-xl">
        <!-- Top Breadcrumb & Header Brief -->
        <div class="text-center mb-4">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small fw-semibold">
                <i class="bi bi-mortarboard-fill me-1"></i> Rekrutmen Guru Mitra &amp; Pendamping
            </span>
            <h1 class="h2 fw-bold mt-2 mb-1" style="color: var(--text-primary);">Pendaftaran Calon Guru Pembimbing</h1>
            <p class="text-secondary small mx-auto mb-0" style="max-width: 650px;">
                Mari berkhidmah menemani santri dan keluarga muslim mempelajari Al-Qur'an dengan bacaan yang mutqin, fasih, dan berakhlak mulia.
            </p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 mb-4" role="alert">
                <div class="fw-semibold small mb-1"><i class="bi bi-exclamation-octagon-fill me-2"></i>Mohon periksa kembali isian formulir:</div>
                <ul class="mb-0 small ps-3">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Split Screen Card: Form Kiri, Visual & Manfaat Kanan -->
        <div class="mentor-split-card shadow-lg">
            <!-- SISI KIRI: Formulir Pendaftaran Lengkap -->
            <div class="mentor-form-side">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-1" style="color: var(--text-primary);">Formulir Pendaftaran Guru</h4>
                        <span class="text-secondary small">Lengkapi data pribadi dan kualifikasi untuk proses kurasi berkas.</span>
                    </div>
                    <span class="badge bg-success text-white px-3 py-1 rounded-pill small">Langkah 1 dari 2</span>
                </div>

                <form action="{{ route('mentor.recruitment.store') }}" method="POST" enctype="multipart/form-data" id="recruitmentForm" class="needs-validation" novalidate>
                    @csrf

                    <!-- 1. DATA PRIBADI & KATA SANDI -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px; font-size: 0.8rem;">1</span>
                            <h6 class="fw-bold mb-0" style="color: var(--text-primary);">Informasi Pribadi &amp; Akun Login</h6>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small" style="color: var(--text-primary);">Nama Lengkap &amp; Gelar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" name="full_name" class="form-control border-start-0" placeholder="Contoh: Ustadz Ahmad Fauzi, S.Pd.I" value="{{ old('full_name') }}" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small" style="color: var(--text-primary);">Alamat Email Aktif <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control border-start-0" placeholder="nama@email.com" value="{{ old('email') }}" required autocomplete="email">
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;">Digunakan untuk login ke portal guru.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small" style="color: var(--text-primary);">Nomor WhatsApp Aktif <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-whatsapp text-success"></i></span>
                                    <input type="tel" name="phone" class="form-control border-start-0" placeholder="081234567890" value="{{ old('phone') }}" required autocomplete="tel">
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;">Untuk koordinasi jadwal wawancara.</small>
                            </div>
                        </div>

                        <div class="row g-3 mb-3 p-3 bg-body-tertiary rounded-3 border">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-success"><i class="bi bi-key-fill me-1"></i>Kata Sandi Akun <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="password" id="inputPwd" class="form-control" placeholder="Minimal 8 karakter" required autocomplete="new-password">
                                    <button class="btn btn-outline-secondary btn-password-toggle" type="button" aria-label="Tampilkan kata sandi">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-success"><i class="bi bi-shield-check me-1"></i>Ulangi Sandi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" id="inputPwdConf" class="form-control" placeholder="Ulangi kata sandi" required autocomplete="new-password">
                                    <button class="btn btn-outline-secondary btn-password-toggle" type="button" aria-label="Tampilkan kata sandi">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small" style="color: var(--text-primary);">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small" style="color: var(--text-primary);">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select" required>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki (Ikhwan / Ustadz)</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan (Akhwat / Ustadzah)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <label class="form-label fw-semibold small" style="color: var(--text-primary);">Alamat Domisili <span class="text-danger">*</span></label>
                                <input type="text" name="address" class="form-control" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan" value="{{ old('address') }}" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold small" style="color: var(--text-primary);">Kota / Kabupaten <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control" placeholder="Contoh: Jakarta Selatan" value="{{ old('city') }}" required>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- 2. KUALIFIKASI PENDIDIKAN & SANAD -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px; font-size: 0.8rem;">2</span>
                            <h6 class="fw-bold mb-0" style="color: var(--text-primary);">Kualifikasi Pendidikan, Hafalan &amp; Sanad</h6>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small" style="color: var(--text-primary);">Pendidikan Terakhir <span class="text-danger">*</span></label>
                                <input type="text" name="education" class="form-control" placeholder="Contoh: S1 Pendidikan Agama Islam / LIPIA" value="{{ old('education') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small" style="color: var(--text-primary);">Institusi / Ma'had / Kampus <span class="text-danger">*</span></label>
                                <input type="text" name="institution" class="form-control" placeholder="Contoh: PTIQ Jakarta / UIN / Ma'had Aly" value="{{ old('institution') }}" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small" style="color: var(--text-primary);">Peminatan Spesialisasi Bimbingan <span class="text-danger">*</span></label>
                                <select name="specialization" class="form-select" required>
                                    <option value="Tahfidz" {{ old('specialization') == 'Tahfidz' ? 'selected' : '' }}>Tahfidz Al-Qur'an (Hafalan Terarah)</option>
                                    <option value="Tahsin" {{ old('specialization', 'Tahsin') == 'Tahsin' ? 'selected' : '' }}>Tahsin &amp; Matan Tajwid (Kaidah Bacaan)</option>
                                    <option value="Iqra" {{ old('specialization') == 'Iqra' ? 'selected' : '' }}>Iqra' &amp; Pra-Tahfidz Anak</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small" style="color: var(--text-primary);">Jumlah Hafalan Al-Qur'an (Juz) <span class="text-danger">*</span></label>
                                <input type="number" name="hifz_total_juz" class="form-control" min="0" max="30" value="{{ old('hifz_total_juz', 0) }}" required>
                                <small class="text-muted" style="font-size: 0.75rem;">Isi 0 jika fokus pada tahsin iqra.</small>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small" style="color: var(--text-primary);">Pengalaman Mengajar (Tahun) <span class="text-danger">*</span></label>
                                <input type="number" name="experience_years" class="form-control" min="0" max="50" value="{{ old('experience_years', 0) }}" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold small" style="color: var(--text-primary);">Deskripsi Singkat Pengalaman Bimbingan <span class="text-danger">*</span></label>
                                <textarea name="experience_description" class="form-control" rows="2" placeholder="Sebutkan lembaga, halaqah, atau TPQ tempat pernah mengajar..." required>{{ old('experience_description') }}</textarea>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small" style="color: var(--text-primary);">Silsilah Sanad Al-Qur'an (Jika Memiliki)</label>
                            <textarea name="sanad_chain" class="form-control" rows="2" placeholder="Contoh: Sanad Qira'at Hafsh 'an 'Ashim Thariq Asy-Syathibiyyah melalui Syaikh...">{{ old('sanad_chain') }}</textarea>
                            <small class="text-muted" style="font-size: 0.75rem;">Kosongkan jika belum memiliki syahadah sanad resmi muttashil.</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- 3. UNGGAH BERKAS -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px; font-size: 0.8rem;">3</span>
                            <h6 class="fw-bold mb-0" style="color: var(--text-primary);">Unggah Berkas Persyaratan</h6>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 bg-body-tertiary">
                                    <label class="form-label fw-semibold small text-danger mb-1">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> Curriculum Vitae (CV) <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" name="cv" class="form-control form-control-sm" accept=".pdf" required>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Format PDF, maksimal 2MB.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 bg-body-tertiary">
                                    <label class="form-label fw-semibold small text-success mb-1">
                                        <i class="bi bi-award me-1"></i> Sertifikat / Syahadah (Opsional)
                                    </label>
                                    <input type="file" name="certificate" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Format PDF/JPG/PNG, maksimal 2MB.</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="agreeTerm" required>
                            <label class="form-check-label small text-secondary" for="agreeTerm">
                                Saya bersaksi bahwa data yang saya kirimkan adalah benar dan saya bersedia mengikuti proses seleksi tilawah &amp; wawancara di AL-HIKMAH LMS.
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-send-check me-2"></i> Kirim Berkas Pendaftaran Guru
                    </button>

                    <div class="text-center mt-3">
                        <span class="small text-secondary">Sudah pernah mengirimkan berkas?</span>
                        <a href="{{ route('mentor.recruitment.status') }}" class="small fw-semibold text-success ms-1">
                            Cek Status Lamaran <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </form>
            </div>

            <!-- SISI KANAN: Visual & Showcase Nilai Dakwah -->
            <div class="mentor-image-side">
                <div class="mentor-image-overlay"></div>
                <div class="mentor-image-content">
                    <!-- Top: Hadits Keutamaan -->
                    <div>
                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-semibold small mb-3">
                            <i class="bi bi-star-fill me-1"></i> Keutamaan Guru Al-Qur'an
                        </span>
                        <blockquote class="fst-italic fs-5 lh-base mb-2 text-white" style="opacity: 0.96;">
                            "خَيْرُكُمْ مَنْ تَعَلَّمَ الْقُرْآنَ وَعَلَّمَهُ"
                        </blockquote>
                        <p class="small text-white-50 mb-0">
                            "Sebaik-baik kalian adalah orang yang mempelajari Al-Qur'an dan mengajarkannya." <br>
                            <span class="text-warning fw-semibold">(HR. Al-Bukhari No. 5027)</span>
                        </p>
                    </div>

                    <!-- Middle: 3 Manfaat Bermitra -->
                    <div class="my-4">
                        <div class="mentor-benefit-pill">
                            <div class="mentor-benefit-icon">
                                <i class="bi bi-award-fill"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-white">Bimbingan Bersanad &amp; Terarah</div>
                                <div class="text-white-50" style="font-size: 0.76rem;">Kurikulum terstandar dilengkapi lembar mutabaah digital.</div>
                            </div>
                        </div>

                        <div class="mentor-benefit-pill">
                            <div class="mentor-benefit-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-white">Fleksibilitas Waktu &amp; Wilayah</div>
                                <div class="text-white-50" style="font-size: 0.76rem;">Pilihan mengajar privat online maupun home visit ke rumah santri.</div>
                            </div>
                        </div>

                        <div class="mentor-benefit-pill mb-0">
                            <div class="mentor-benefit-icon">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-white">Ujrah Profesional &amp; Amanah</div>
                                <div class="text-white-50" style="font-size: 0.76rem;">Pemberian insentif transparan tepat waktu setiap bulannya.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom: 3 Tahap Rekrutmen & WhatsApp Support -->
                    <div class="pt-3 border-top border-white-50">
                        <div class="small fw-semibold text-white mb-2"><i class="bi bi-diagram-3 me-1 text-warning"></i> Alur Seleksi Calon Guru:</div>
                        <ol class="small text-white-50 ps-3 mb-3" style="font-size: 0.78rem;">
                            <li>Pengisian berkas administrasi online</li>
                            <li>Uji tilawah tahsin &amp; wawancara pedagogis</li>
                            <li>Aktivasi portal &amp; penugasan santri</li>
                        </ol>

                        <div class="d-flex align-items-center justify-content-between pt-2 border-top border-white-50">
                            <div>
                                <div class="small fw-semibold text-white">Butuh bantuan pendaftaran?</div>
                                <div class="text-white-50" style="font-size: 0.75rem;">Tim sekretariat kami siap membantu.</div>
                            </div>
                            <a href="{{ wa_url('Assalamualaikum admin AL-HIKMAH, saya ingin menanyakan perihal rekrutmen guru') }}" target="_blank" class="btn btn-sm btn-light rounded-pill px-3 fw-semibold">
                                <i class="bi bi-whatsapp text-success me-1"></i> Tanya Admin
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
