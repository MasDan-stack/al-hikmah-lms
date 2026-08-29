@extends('layouts.landing')

@section('title', 'Pendaftaran Guru Pembimbing Al-Qur\'an - AL-HIKMAH LMS')

@section('content')
<!-- Page Header -->
<section class="page-header section-padding" style="padding-top: 130px; background: linear-gradient(170deg, var(--bg-primary) 0%, var(--primary-lighter) 100%);">
    <div class="container text-center">
        <div class="section-badge mx-auto mb-3"><i class="bi bi-mortarboard-fill me-1"></i> Rekrutmen Guru</div>
        <h1 class="section-title fw-bold text-primary">Formulir Pendaftaran Guru Pembimbing</h1>
        <p class="section-description mx-auto text-muted">Bergabunglah bersama keluarga besar AL-HIKMAH LMS dalam membimbing generasi Qur'ani yang mutqin dan berakhlak mulia.</p>
    </div>
</section>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-11">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill me-2"></i>Mohon periksa kembali isian formulir:</h6>
                    <ul class="mb-0 small ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <!-- Header Card Info -->
                <div class="card-header bg-light border-bottom p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary-subtle text-primary p-3 fs-4">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Registrasi Akun Calon Guru & Data Portofolio</h5>
                            <small class="text-muted">Setelah mengisi data & kata sandi, Anda langsung memiliki akses ke Dashboard Portal Calon Guru untuk mengikuti ujian tes seleksi.</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('mentor.recruitment.store') }}" method="POST" enctype="multipart/form-data" id="recruitmentForm">
                        @csrf
                        
                        <!-- 1. DATA PRIBADI & AKUN LOGIN -->
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-person-circle me-2"></i>1. Informasi Pribadi & Akun Login Dashboard</h5>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" placeholder="Contoh: Ustadz Ahmad Fauzi, S.Pd.I" value="{{ old('full_name') }}" required>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Alamat Email Aktif <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required>
                                    <small class="text-muted">Akan digunakan sebagai username login ke Dashboard.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nomor WhatsApp Aktif <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" placeholder="081234567890" value="{{ old('phone') }}" required>
                                    <small class="text-muted">Nomor kontak untuk koordinasi wawancara & konfirmasi.</small>
                                </div>
                            </div>

                            <div class="row g-3 mb-3 p-3 bg-light rounded-3 border">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-primary"><i class="bi bi-key-fill me-1"></i>Kata Sandi (Password Login) <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
                                    <small class="text-muted">Gunakan kata sandi yang mudah Anda ingat.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-primary"><i class="bi bi-shield-check me-1"></i>Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi" required>
                                    <small class="text-muted">Pastikan kata sandi sama persis.</small>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select name="gender" class="form-select" required>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki (Ikhwan)</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan (Akhwat)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Alamat Domisili <span class="text-danger">*</span></label>
                                    <input type="text" name="address" class="form-control" placeholder="Nama Jalan, RT/RW, Kelurahan, Kecamatan" value="{{ old('address') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Kota / Kabupaten <span class="text-danger">*</span></label>
                                    <input type="text" name="city" class="form-control" placeholder="Contoh: Jakarta Selatan" value="{{ old('city') }}" required>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- 2. PENDIDIKAN & SANAD -->
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-book-half me-2"></i>2. Kualifikasi Pendidikan, Hafalan & Sanad</h5>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Pendidikan Terakhir <span class="text-danger">*</span></label>
                                    <input type="text" name="education" class="form-control" placeholder="Contoh: S1 Ilmu Al-Qur'an dan Tafsir" value="{{ old('education') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Institusi / Ma'had / Kampus <span class="text-danger">*</span></label>
                                    <input type="text" name="institution" class="form-control" placeholder="Contoh: PTIQ Jakarta / LIPIA / UIN" value="{{ old('institution') }}" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Peminatan Spesialisasi Bimbingan <span class="text-danger">*</span></label>
                                    <select name="specialization" class="form-select" required>
                                        <option value="Tahfidz" {{ old('specialization') == 'Tahfidz' ? 'selected' : '' }}>Tahfidz Al-Qur'an (Hafalan 30 Juz)</option>
                                        <option value="Tahsin" {{ old('specialization') == 'Tahsin' ? 'selected' : '' }}>Tahsin & Matan Tajwid (Kaidah Bacaan)</option>
                                        <option value="Iqra" {{ old('specialization') == 'Iqra' ? 'selected' : '' }}>Iqra' & Pra-Tahfidz Anak</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jumlah Hafalan Terverifikasi (Juz) <span class="text-danger">*</span></label>
                                    <input type="number" name="hifz_total_juz" class="form-control" min="0" max="30" value="{{ old('hifz_total_juz', 0) }}" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Pengalaman Mengajar (Tahun) <span class="text-danger">*</span></label>
                                    <input type="number" name="experience_years" class="form-control" min="0" max="50" value="{{ old('experience_years', 0) }}" required>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Deskripsi Singkat Pengalaman Bimbingan <span class="text-danger">*</span></label>
                                    <textarea name="experience_description" class="form-control" rows="2" placeholder="Sebutkan lembaga/TPQ tempat pernah mengajar sebelumnya..." required>{{ old('experience_description') }}</textarea>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Silsilah Sanad Al-Qur'an (Jika Memiliki)</label>
                                <textarea name="sanad_chain" class="form-control" rows="2" placeholder="Contoh: Sanad Qira'at 'Ashim Riwayat Hafsh Thariq Asy-Syathibiyyah melalui Syaikh...">{{ old('sanad_chain') }}</textarea>
                                <small class="text-muted">Kosongkan jika belum memiliki sanad resmi muttashil.</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- 3. UNGGAH DOKUMEN -->
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-file-earmark-arrow-up me-2"></i>3. Unggah Berkas Persyaratan</h5>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 bg-light">
                                        <label class="form-label fw-semibold text-danger"><i class="bi bi-file-pdf me-1"></i>Curriculum Vitae (CV) <span class="text-danger">*</span></label>
                                        <input type="file" name="cv" class="form-control" accept=".pdf" required>
                                        <small class="text-muted d-block mt-1">Format wajib PDF, ukuran maksimal 2MB.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 bg-light">
                                        <label class="form-label fw-semibold text-primary"><i class="bi bi-award me-1"></i>Sertifikat / Syahadah Sanad (Opsional)</label>
                                        <input type="file" name="certificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="text-muted d-block mt-1">Format PDF/JPG/PNG, ukuran maksimal 2MB.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="agreeTerm" required>
                                <label class="form-check-label small" for="agreeTerm">
                                    Saya menyatakan bahwa seluruh data yang diisikan adalah benar dan bersedia mengikuti seluruh tahapan seleksi kompetensi & wawancara di AL-HIKMAH LMS.
                                </label>
                            </div>
                        </div>

                        <div class="d-grid pt-2">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold py-3 shadow-sm rounded-pill">
                                <i class="bi bi-send-fill me-2"></i>Kirim Pendaftaran & Masuk ke Dashboard
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
