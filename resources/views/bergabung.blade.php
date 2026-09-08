@extends('layouts.landing')

@section('title', 'Bergabung Sebagai Pendamping | AL-HIKMAH')
@section('description', 'Bergabung bersama AL-HIKMAH. Kesempatan menjadi pendamping dalam perjalanan belajar Al-Qur\'an.')

@section('content')
    <div class="text-center pt-5 pb-2">
        <div class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small fw-semibold mb-2">
            <i class="bi bi-person-workspace me-1"></i> Karir Guru &amp; Mitra Pendamping
        </div>
        <h1 class="h2 fw-bold mb-1" style="color: var(--text-primary);">Pendaftaran Calon Guru Pembimbing</h1>
        <p class="text-secondary small mx-auto mb-0" style="max-width: 650px;">
            Bergabunglah bersama keluarga besar AL-HIKMAH dalam membimbing generasi Qur'ani dengan bacaan yang mutqin, fasih, dan berakhlak mulia.
        </p>
    </div>

    <!-- Formulir Registrasi Pendamping / Guru (Split Layout: Form Kiri, Gambar Kanan) -->
    <section class="mentor-split-wrapper section-alt" id="formDaftarMentor" aria-label="Formulir Pendaftaran Guru">
        <div class="container">
            <div class="mentor-split-card shadow-lg">
                <!-- Sisi Kiri: Formulir Pendaftaran -->
                <div class="mentor-form-side">
                    <div class="mb-4">
                        <div class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small mb-2">
                            <i class="bi bi-person-workspace me-1"></i> Rekrutmen Pengajar Baru
                        </div>
                        <h3 class="fw-bold mb-1" style="color: var(--text-primary);">Formulir Calon Pendamping</h3>
                        <p class="small text-secondary mb-0">Isi data diri dan kualifikasi Anda untuk proses seleksi administrasi awal.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 mb-4 py-2 px-3">
                            <div class="fw-semibold small mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Mohon periksa kembali isian berikut:</div>
                            <ul class="mb-0 small ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('bergabung') }}" class="needs-validation" novalidate>
                        @csrf

                        <!-- Grup 1: Data Diri & Kontak -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold small" style="color: var(--text-primary);">Nama Lengkap (Beserta Gelar/Sapaan) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" name="name" id="name" class="form-control border-start-0 @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Contoh: Ustadz Ahmad Fauzan, S.Pd.I">
                            </div>
                            @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold small" style="color: var(--text-primary);">Alamat Email Aktif <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control border-start-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="nama@email.com" autocomplete="email">
                                </div>
                                @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold small" style="color: var(--text-primary);">No. WhatsApp Aktif <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-whatsapp"></i></span>
                                    <input type="tel" name="phone" id="phone" class="form-control border-start-0 @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="08123456789" autocomplete="tel">
                                </div>
                                @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Grup 2: Kompetensi & Pengalaman -->
                        <div class="mb-3">
                            <label for="specialization" class="form-label fw-semibold small" style="color: var(--text-primary);">Spesialisasi Bimbingan Utama</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-journal-check"></i></span>
                                <input type="text" name="specialization" id="specialization" class="form-control border-start-0 @error('specialization') is-invalid @enderror" value="{{ old('specialization', 'Tahsin & Tajwid Al-Qur\'an') }}" placeholder="Contoh: Tahsin, Tahfidz 30 Juz, atau Iqra Pemula">
                            </div>
                            @error('specialization') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label fw-semibold small" style="color: var(--text-primary);">Riwayat Pendidikan Al-Qur'an &amp; Pengalaman</label>
                            <textarea name="bio" id="bio" rows="2" class="form-control @error('bio') is-invalid @enderror" placeholder="Sebutkan pondok pesantren, sertifikasi sanad, atau pengalaman mengajar sebelumnya...">{{ old('bio') }}</textarea>
                            @error('bio') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <!-- Grup 3: Keamanan Akun LMS -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold small" style="color: var(--text-primary);">Kata Sandi Akun <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror" required placeholder="Minimal 8 karakter" autocomplete="new-password">
                                    <button class="btn btn-outline-secondary border-start-0 btn-password-toggle" type="button" aria-label="Tampilkan kata sandi">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold small" style="color: var(--text-primary);">Ulangi Kata Sandi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-shield-check"></i></span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0 border-end-0" required placeholder="Ketik ulang sandi" autocomplete="new-password">
                                    <button class="btn btn-outline-secondary border-start-0 btn-password-toggle" type="button" aria-label="Tampilkan konfirmasi kata sandi">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-bold rounded-pill shadow-sm">
                            <i class="bi bi-send-check me-2"></i> Kirim Berkas Pendaftaran
                        </button>

                        <div class="text-center mt-3">
                            <span class="small text-secondary">Sudah pernah mengirim lamaran?</span>
                            <a href="{{ route('mentor.recruitment.status') }}" class="small fw-semibold text-success ms-1">
                                Cek Status Lamaran <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Sisi Kanan: Visual & Keunggulan Mengajar -->
                <div class="mentor-image-side">
                    <div class="mentor-image-overlay"></div>
                    <div class="mentor-image-content">
                        <!-- Bagian Atas: Kutipan Hadits -->
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-semibold small">
                                    <i class="bi bi-star-fill me-1"></i> Keutamaan Guru
                                </span>
                            </div>
                            <blockquote class="fst-italic fs-6 lh-base mb-2 text-white" style="opacity: 0.95;">
                                "خَيْرُكُمْ مَنْ تَعَلَّمَ الْقُرْآنَ وَعَلَّمَهُ"
                            </blockquote>
                            <p class="small text-white-50 mb-0">
                                "Sebaik-baik kalian adalah orang yang mempelajari Al-Qur'an dan mengajarkannya." <br>
                                <span class="text-warning fw-semibold">(HR. Al-Bukhari No. 5027)</span>
                            </p>
                        </div>

                        <!-- Bagian Tengah: 3 Kartu Manfaat -->
                        <div class="my-4">
                            <div class="mentor-benefit-pill">
                                <div class="mentor-benefit-icon">
                                    <i class="bi bi-award-fill"></i>
                                </div>
                                <div>
                                    <div class="fw-bold small text-white">Bimbingan Bersanad &amp; Terarah</div>
                                    <div class="text-white-50" style="font-size: 0.76rem;">Kurikulum terstandar dengan buku mutabaah digital.</div>
                                </div>
                            </div>

                            <div class="mentor-benefit-pill">
                                <div class="mentor-benefit-icon">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                                <div>
                                    <div class="fw-bold small text-white">Fleksibilitas Waktu &amp; Wilayah</div>
                                    <div class="text-white-50" style="font-size: 0.76rem;">Pilih jam bimbingan online maupun home visit privat.</div>
                                </div>
                            </div>

                            <div class="mentor-benefit-pill mb-0">
                                <div class="mentor-benefit-icon">
                                    <i class="bi bi-heart-pulse-fill"></i>
                                </div>
                                <div>
                                    <div class="fw-bold small text-white">Insentif Profesional &amp; Amanah</div>
                                    <div class="text-white-50" style="font-size: 0.76rem;">Sistem honor transparan tepat waktu setiap bulan.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Bagian Bawah: Bantuan & Kontak Cepat -->
                        <div class="pt-3 border-top border-white-50">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="small fw-semibold text-white">Butuh bantuan pendaftaran?</div>
                                    <div class="text-white-50" style="font-size: 0.75rem;">Tim admin kami siap menjawab via WhatsApp.</div>
                                </div>
                                <a href="{{ wa_url('Assalamualaikum admin AL-HIKMAH, saya ingin menanyakan proses rekrutmen guru') }}" target="_blank" class="btn btn-sm btn-light rounded-pill px-3 fw-semibold">
                                    <i class="bi bi-whatsapp text-success me-1"></i> Tanya Admin
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
