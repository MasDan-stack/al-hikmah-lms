@extends('layouts.landing')

@section('title', 'Hubungi Kami | AL-HIKMAH LMS')
@section('meta_description', 'Kirim pesan dan konsultasi seputar bimbingan belajar Al-Qur\'an AL-HIKMAH. Tim kami siap merespons via WhatsApp.')

@section('content')
<!-- ============================================ -->
<!-- 1. ETRAIN BREADCRUMB HEADER -->
<!-- ============================================ -->
<section class="breadcrumb_bg" aria-label="Header Kontak">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb_iner_item" data-reveal>
                    <div class="section-badge mx-auto mb-2"><i class="bi bi-envelope-paper"></i> Layanan Konsultasi</div>
                    <h2>Mari Berbincang <span class="text-gradient">Dengan Tim AL-HIKMAH</span></h2>
                    <p>Sampaikan pertanyaan, permintaan jadwal khusus, atau konsultasi kebutuhan belajar ananda kepada tim pengelola kami.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CONTACT SECTION -->
<section class="section-padding" aria-label="Formulir Kontak">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <!-- Left Column: Form -->
            <div class="col-lg-7" data-reveal>
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
                    <h4 class="fw-bold text-dark mb-2"><i class="bi bi-chat-left-text text-success me-2"></i>Formulir Konsultasi &amp; Pesan</h4>
                    <p class="text-muted small mb-4">Isi data di bawah ini, admin kami akan membaca pesan Anda dan langsung menghubungi melalui WhatsApp.</p>

                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-4 p-4 mb-4 shadow-sm">
                            <div class="d-flex align-items-start gap-3">
                                <i class="bi bi-check-circle-fill fs-3 text-success"></i>
                                <div>
                                    <h6 class="fw-bold mb-1 text-success">Pesan Berhasil Terkirim!</h6>
                                    <p class="small mb-0 text-success-emphasis">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger border-0 rounded-4 p-3 mb-4">
                            <ul class="mb-0 small ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST" class="row g-3">
                        @csrf
                        <!-- 1. Nama Orang Tua -->
                        <div class="col-12">
                            <label for="name" class="form-label small fw-bold text-secondary">
                                Nama Orang Tua / Wali <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                <input type="text" name="name" id="name" class="form-control border-start-0 @error('name') is-invalid @enderror" 
                                       placeholder="Contoh: Ayah Hendra / Bunda Fatimah" value="{{ old('name', auth()->user()?->name) }}" required>
                            </div>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 2. Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label small fw-bold text-secondary">
                                Alamat Email <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" name="email" id="email" class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                       placeholder="nama@email.com" value="{{ old('email', auth()->user()?->email) }}" required>
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 3. WhatsApp -->
                        <div class="col-md-6">
                            <label for="phone" class="form-label small fw-bold text-secondary">
                                Nomor WhatsApp <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-whatsapp text-muted"></i></span>
                                <input type="tel" name="phone" id="phone" class="form-control border-start-0 @error('phone') is-invalid @enderror" 
                                       placeholder="081234567890" value="{{ old('phone', auth()->user()?->phone) }}" required>
                            </div>
                            @error('phone')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 4. Alamat Lengkap -->
                        <div class="col-12">
                            <label for="address" class="form-label small fw-bold text-secondary">
                                Alamat Lengkap / Kota Domisili <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-geo-alt text-muted"></i></span>
                                <textarea name="address" id="address" rows="2" class="form-control border-start-0 @error('address') is-invalid @enderror" 
                                          placeholder="Contoh: Jl. Sukajadi No. 45, Kecamatan Sukasari, Kota Bandung" required>{{ old('address') }}</textarea>
                            </div>
                            @error('address')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 5. Pesan -->
                        <div class="col-12">
                            <label for="message" class="form-label small fw-bold text-secondary">
                                Pesan / Kebutuhan Bimbingan Ananda <span class="text-danger">*</span>
                            </label>
                            <textarea name="message" id="message" rows="4" class="form-control @error('message') is-invalid @enderror" 
                                      placeholder="Tuliskan pertanyaan atau kebutuhan belajar ananda (misal: ingin guru perempuan datang hari Rabu jam 16:00, ananda usia 12 tahun belum lancar tajwid)..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn_1 w-100 text-center border-0">
                                <i class="bi bi-send-fill me-1"></i> Kirim Pesan Konsultasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Info Lembaga -->
            <div class="col-lg-5" data-reveal data-reveal-delay="150">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4">
                    <h5 class="fw-bold text-dark mb-4"><i class="bi bi-building-check text-success me-2"></i>Informasi Lembaga</h5>

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; min-width: 45px;">
                            <i class="bi bi-whatsapp fs-5"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">WhatsApp Customer Service</span>
                            <a href="{{ wa_url() }}" target="_blank" class="fw-bold text-success text-decoration-none">
                                +{{ site_setting('whatsapp_number', '6285786689008') }}
                            </a>
                            <small class="text-muted d-block">Senin – Ahad: 08:00 – 21:00 WIB</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; min-width: 45px;">
                            <i class="bi bi-envelope fs-5"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Email Resmi</span>
                            <span class="fw-bold text-dark">{{ site_setting('email_contact', 'belajarquranalhikmah@gmail.com') }}</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="rounded-circle bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; min-width: 45px;">
                            <i class="bi bi-instagram fs-5"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Instagram Resmi</span>
                            <a href="https://www.instagram.com/{{ site_setting('instagram_handle', 'houseofalhikmah') }}/" target="_blank" class="fw-bold text-dark text-decoration-none">
                                @<span>{{ site_setting('instagram_handle', 'houseofalhikmah') }}</span>
                            </a>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; min-width: 45px;">
                            <i class="bi bi-geo-alt fs-5"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Kantor Layanan</span>
                            <span class="text-secondary small">{{ site_setting('address', 'Indonesia — Melayani Area Jabodetabek & Online Nasional') }}</span>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary-subtle text-primary-emphasis">
                    <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>Butuh Jawaban Cepat?</h6>
                    <p class="small mb-3">Sebagian besar pertanyaan tentang jadwal, guru datang ke rumah, dan biaya telah dijawab lengkap pada halaman FAQ.</p>
                    <a href="{{ route('faq') }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold">
                        <i class="bi bi-question-circle me-1"></i> Buka Halaman FAQ
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
