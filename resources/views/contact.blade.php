@extends('layouts.landing')

@section('title', 'Hubungi Kami | AL-HIKMAH LMS')
@section('meta_description', 'Kirim pesan dan konsultasi seputar bimbingan belajar Al-Qur\'an AL-HIKMAH. Tim kami siap merespons via WhatsApp.')

@push('styles')
<style>
    .contact-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl, 20px);
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        height: auto;
    }
    .contact-card:hover {
        border-color: var(--border-color-strong);
    }
    .contact-input {
        height: 48px;
        font-size: 0.92rem;
        border-radius: var(--radius-md, 10px);
        border: 1px solid var(--border-color);
        background-color: var(--card-bg);
        color: var(--text-primary);
        padding: 0.65rem 1rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .contact-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(6, 78, 59, 0.12);
        background-color: var(--card-bg);
        color: var(--text-primary);
    }
    .contact-textarea {
        font-size: 0.92rem;
        border-radius: var(--radius-md, 10px);
        border: 1px solid var(--border-color);
        background-color: var(--card-bg);
        color: var(--text-primary);
        padding: 0.75rem 1rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .contact-textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(6, 78, 59, 0.12);
        background-color: var(--card-bg);
        color: var(--text-primary);
    }
    .contact-info-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding-bottom: 1.25rem;
        margin-bottom: 1.25rem;
        border-bottom: 1px solid var(--border-color);
    }
    .contact-info-item:last-child {
        padding-bottom: 0;
        margin-bottom: 0;
        border-bottom: none;
    }
    .contact-icon-box {
        width: 44px;
        height: 44px;
        min-width: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        background: rgba(6, 78, 59, 0.08);
        color: var(--primary);
        border: 1px solid rgba(6, 78, 59, 0.12);
    }
    [data-bs-theme="dark"] .contact-icon-box {
        background: rgba(16, 185, 129, 0.12);
        color: var(--primary-light);
        border-color: rgba(16, 185, 129, 0.2);
    }
    .contact-faq-callout {
        background: var(--card-bg);
        border: 1.5px solid var(--primary);
        border-radius: var(--radius-xl, 20px);
        box-shadow: 0 10px 25px -5px rgba(6, 78, 59, 0.08);
        height: auto;
    }
</style>
@endpush

@section('content')
<!-- ============================================ -->
<!-- 1. PAGE HEADER - EDITORIAL MINIMALIST -->
<!-- ============================================ -->
<section class="editorial-page-header text-center" aria-label="Header Kontak">
    <div class="container py-2">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div data-reveal>
                    <div class="section-badge mx-auto mb-3">
                        <i class="bi bi-chat-heart-fill me-1"></i> Layanan Konsultasi
                    </div>
                    <h1 class="editorial-title mb-3">Layanan Konsultasi &amp; <span class="text-emerald-deep">Hubungi Kami</span></h1>
                    <p class="editorial-subtitle mx-auto">
                        Sampaikan pertanyaan, kebutuhan jadwal privat, atau konsultasi evaluasi awal ananda langsung kepada konselor pendidikan AL-HIKMAH.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 2. CONTACT FORM & INFO SECTION -->
<!-- ============================================ -->
<section class="py-5" aria-label="Formulir Kontak">
    <div class="container">
        <div class="row g-4 justify-content-center align-items-start">
            <!-- Left Column: Form -->
            <div class="col-lg-7" data-reveal>
                <div class="contact-card p-4 p-md-5">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="contact-icon-box">
                            <i class="bi bi-chat-left-text-fill"></i>
                        </div>
                        <div>
                            <h2 class="fs-4 fw-bold text-heading mb-0">Formulir Konsultasi &amp; Pesan</h2>
                            <span class="small text-muted">Respons cepat dalam hitungan jam</span>
                        </div>
                    </div>
                    <p class="text-secondary small mb-4 mt-2">
                        Isi formulir di bawah ini. Konselor pendidikan kami akan membaca pesan Anda dan merespons dengan ramah melalui WhatsApp.
                    </p>

                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-3 p-4 mb-4 shadow-sm" role="alert">
                            <div class="d-flex align-items-start gap-3">
                                <i class="bi bi-check-circle-fill fs-4 text-emerald-deep flex-shrink-0 mt-0.5"></i>
                                <div>
                                    <h3 class="fs-6 fw-bold mb-1 text-emerald-deep">Pesan Berhasil Terkirim!</h3>
                                    <p class="small mb-0 text-secondary">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger border-0 rounded-3 p-3 mb-4" role="alert">
                            <div class="fw-semibold small mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Mohon lengkapi formulir dengan benar:</div>
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
                            <label for="name" class="form-label small fw-semibold text-secondary mb-1">
                                Nama Orang Tua / Wali <span class="text-danger">*</span>
                            </label>
                            <div class="input-group-editorial">
                                <span class="field-icon"><i class="bi bi-person"></i></span>
                                <input type="text" name="name" id="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       placeholder="Contoh: Ayah Hendra / Bunda Fatimah" 
                                       value="{{ old('name', auth()->user()?->name) }}" required>
                            </div>
                            @error('name')
                                <div class="invalid-feedback d-block small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 2. Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label small fw-semibold text-secondary mb-1">
                                Alamat Email <span class="text-danger">*</span>
                            </label>
                            <div class="input-group-editorial">
                                <span class="field-icon"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" id="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       placeholder="nama@email.com" 
                                       value="{{ old('email', auth()->user()?->email) }}" required>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 3. WhatsApp -->
                        <div class="col-md-6">
                            <label for="phone" class="form-label small fw-semibold text-secondary mb-1">
                                Nomor WhatsApp <span class="text-danger">*</span>
                            </label>
                            <div class="input-group-editorial">
                                <span class="field-icon"><i class="bi bi-whatsapp text-success"></i></span>
                                <input type="tel" name="phone" id="phone" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       placeholder="081234567890" 
                                       value="{{ old('phone', auth()->user()?->phone) }}" required>
                            </div>
                            @error('phone')
                                <div class="invalid-feedback d-block small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 4. Alamat Lengkap -->
                        <div class="col-12">
                            <label for="address" class="form-label small fw-semibold text-secondary mb-1">
                                Alamat Lengkap / Kota Domisili <span class="text-danger">*</span>
                            </label>
                            <div class="input-group-editorial align-items-start">
                                <span class="field-icon pt-2"><i class="bi bi-geo-alt"></i></span>
                                <textarea name="address" id="address" rows="2" 
                                          class="form-control @error('address') is-invalid @enderror" 
                                          placeholder="Contoh: Jl. Sukajadi No. 45, Sukasari, Kota Bandung" required>{{ old('address') }}</textarea>
                            </div>
                            @error('address')
                                <div class="invalid-feedback d-block small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 5. Pesan -->
                        <div class="col-12">
                            <label for="message" class="form-label small fw-semibold text-secondary mb-1">
                                Pesan / Kebutuhan Bimbingan Ananda <span class="text-danger">*</span>
                            </label>
                            <div class="input-group-editorial align-items-start">
                                <span class="field-icon pt-2"><i class="bi bi-chat-left-dots"></i></span>
                                <textarea name="message" id="message" rows="4" 
                                          class="form-control @error('message') is-invalid @enderror" 
                                          placeholder="Tuliskan pertanyaan atau kebutuhan bimbingan ananda (misal: ingin jadwal privat santri putri hari Rabu jam 16:00, usia 10 tahun pemula tajwid)..." required>{{ old('message') }}</textarea>
                            </div>
                            @error('message')
                                <div class="invalid-feedback d-block small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn-editorial-primary w-100 py-3 justify-content-center border-0 fw-semibold fs-6">
                                <i class="bi bi-send-fill me-1"></i> Kirim Pesan Konsultasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Info Lembaga -->
            <div class="col-lg-5" data-reveal data-reveal-delay="150">
                <div class="contact-card p-4 p-md-5 mb-4">
                    <h3 class="fs-5 fw-bold text-heading mb-4 d-flex align-items-center gap-2">
                        <i class="bi bi-building-check text-emerald-deep"></i> Informasi Lembaga
                    </h3>

                    <!-- WhatsApp -->
                    <div class="contact-info-item">
                        <div class="contact-icon-box">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-0.5">WhatsApp Konselor Layanan</span>
                            <a href="{{ wa_url() }}" target="_blank" class="fw-bold text-emerald-deep text-decoration-none fs-6">
                                +{{ site_setting('whatsapp_number', '6285786689008') }}
                            </a>
                            <div class="small text-muted mt-1">
                                <span class="badge bg-light text-secondary border">Senin - Ahad: 08:00 - 21:00 WIB</span>
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="contact-info-item">
                        <div class="contact-icon-box">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-0.5">Email Resmi</span>
                            <span class="fw-semibold text-heading small">{{ site_setting('email_contact', 'belajarquranalhikmah@gmail.com') }}</span>
                        </div>
                    </div>

                    <!-- Instagram -->
                    <div class="contact-info-item">
                        <div class="contact-icon-box">
                            <i class="bi bi-instagram"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-0.5">Instagram Resmi</span>
                            <a href="https://www.instagram.com/{{ site_setting('instagram_handle', 'houseofalhikmah') }}/" target="_blank" class="fw-semibold text-heading text-decoration-none small">
                                @<span>{{ site_setting('instagram_handle', 'houseofalhikmah') }}</span>
                            </a>
                        </div>
                    </div>

                    <!-- Kantor Layanan -->
                    <div class="contact-info-item">
                        <div class="contact-icon-box">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-0.5">Kantor Layanan</span>
                            <span class="text-secondary small lh-base d-block">{{ site_setting('address', 'Indonesia (Melayani Area Jabodetabek & Online Seluruh Indonesia)') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Fast FAQ Card -->
                <div class="contact-faq-callout p-4">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="contact-icon-box">
                            <i class="bi bi-question-circle-fill"></i>
                        </div>
                        <div>
                            <h3 class="fs-6 fw-bold text-heading mb-0">Butuh Jawaban Cepat?</h3>
                            <span class="small text-muted">Pertanyaan umum telah dirangkum</span>
                        </div>
                    </div>
                    <p class="small text-secondary mb-3 mt-2 lh-base">
                        Sebagian besar pertanyaan seputar jadwal bimbingan, kualifikasi asatidz datang ke rumah, dan rincian biaya telah dijawab lengkap pada halaman FAQ.
                    </p>
                    <div>
                        <a href="{{ route('faq') }}" class="btn-editorial-secondary px-3 py-2 text-decoration-none">
                            <i class="bi bi-question-circle me-1"></i> Buka Halaman FAQ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
