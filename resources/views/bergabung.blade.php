@extends('layouts.landing')

@section('title', 'Bergabung Sebagai Pendamping | AL-HIKMAH')
@section('description', 'Bergabung bersama AL-HIKMAH — Kesempatan menjadi pendamping dalam perjalanan belajar Al-Qur\'an.')

@section('content')
    <!-- Page Header -->
    <section class="page-header section-padding"
        style="padding-top:120px;background:linear-gradient(170deg,var(--bg-primary)0%,var(--primary-lighter)100%)">
        <div class="container text-center">
            <div class="section-badge mx-auto" data-reveal><i class="bi bi-person-plus"></i> Bergabung</div>
            <h1 class="section-title" data-reveal>Menjadi Bagian dari<br><span class="text-gradient">Perjalanan Ini</span></h1>
            <p class="section-description mx-auto" data-reveal>AL-HIKMAH membuka kesempatan bagi pengajar dan pendamping yang ingin menemani perjalanan belajar Al-Qur'an anak dan keluarga.</p>
        </div>
    </section>

    <!-- Kriteria & Kualifikasi -->
    <section class="section-padding" aria-label="Kriteria">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-4" data-reveal>
                    <div class="why-card h-100">
                        <div class="why-icon"><i class="bi bi-book"></i></div>
                        <h4>Kompetensi</h4>
                        <p>Memiliki bacaan Al-Qur'an yang baik, menguasai tajwid dasar, dan makhraj yang fasih.</p>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="100">
                    <div class="why-card h-100">
                        <div class="why-icon"><i class="bi bi-heart"></i></div>
                        <h4>Karakter</h4>
                        <p>Sabar, menyayangi anak-anak, beradab, dan mampu menjadi teladan yang baik bagi santri.</p>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="200">
                    <div class="why-card h-100">
                        <div class="why-icon"><i class="bi bi-shield-check"></i></div>
                        <h4>Komitmen</h4>
                        <p>Bersedia menjaga amanah pendampingan, konsisten, dan terus belajar mengembangkan diri.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Formulir Registrasi Pendamping / Guru -->
    <section class="section-padding section-alt" id="formDaftarMentor">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-header bg-success text-white p-4 text-center border-0" style="background: linear-gradient(135deg, var(--primary) 0%, #09592d 100%) !important;">
                            <h4 class="fw-bold mb-1"><i class="bi bi-person-badge me-2"></i>Formulir Pendaftaran Pendamping / Guru</h4>
                            <p class="small text-white-50 mb-0">Daftarkan diri Anda untuk menjadi bagian dari pengajar AL-HIKMAH LMS</p>
                        </div>
                        <div class="card-body p-4 p-md-5">
                            @if ($errors->any())
                                <div class="alert alert-danger rounded-3 mb-4">
                                    <ul class="mb-0 small ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('bergabung') }}" class="needs-validation" novalidate>
                                @csrf

                                <div class="row g-3">
                                    <!-- Nama Lengkap -->
                                    <div class="col-md-6">
                                        <label for="name" class="form-label fw-semibold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                            <input type="text" name="name" id="name" class="form-control border-start-0 @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Ustadz / Ustadzah...">
                                        </div>
                                        @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-semibold text-secondary">Alamat Email <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                            <input type="email" name="email" id="email" class="form-control border-start-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="email@domain.com">
                                        </div>
                                        @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Telepon / WA -->
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label fw-semibold text-secondary">No. WhatsApp / Telepon</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-whatsapp"></i></span>
                                            <input type="text" name="phone" id="phone" class="form-control border-start-0 @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="08123456789">
                                        </div>
                                        @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Spesialisasi -->
                                    <div class="col-md-6">
                                        <label for="specialization" class="form-label fw-semibold text-secondary">Spesialisasi Mengajar</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-journal-bookmark"></i></span>
                                            <input type="text" name="specialization" id="specialization" class="form-control border-start-0 @error('specialization') is-invalid @enderror" value="{{ old('specialization', 'Tahsin & Tajwid Al-Qur\'an') }}" placeholder="Tahsin, Tahfidz 30 Juz, Iqra">
                                        </div>
                                        @error('specialization') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Kata Sandi -->
                                    <div class="col-md-6">
                                        <label for="password" class="form-label fw-semibold text-secondary">Kata Sandi <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                            <input type="password" name="password" id="password" class="form-control border-start-0 @error('password') is-invalid @enderror" required placeholder="Minimal 8 karakter">
                                        </div>
                                        @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Konfirmasi Kata Sandi -->
                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label fw-semibold text-secondary">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-shield-check"></i></span>
                                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0" required placeholder="Ulangi kata sandi">
                                        </div>
                                    </div>

                                    <!-- Biografi & Pengalaman -->
                                    <div class="col-12">
                                        <label for="bio" class="form-label fw-semibold text-secondary">Biografi & Latar Belakang Pendidikan</label>
                                        <textarea name="bio" id="bio" rows="3" class="form-control @error('bio') is-invalid @enderror" placeholder="Ceritakan riwayat pendidikan Al-Qur'an atau pengalaman mengajar Anda...">{{ old('bio') }}</textarea>
                                        @error('bio') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-bold mt-4 rounded-3 fs-5">
                                    <i class="bi bi-send-check me-2"></i> Daftar Sebagai Pendamping
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
