@extends('layouts.landing')

@section('title', 'Program Belajar | AL-HIKMAH')
@section('description', 'Program belajar AL-HIKMAH — Iqra, Tahsin, Tahfidz, Adab & Doa, Bahasa Arab, dan Kelas Muslimah untuk anak dan dewasa.')

@section('content')
    <!-- Page Header -->
    <section class="page-header section-padding"
        style="padding-top:120px;background:linear-gradient(170deg,var(--bg-primary)0%,var(--primary-lighter)100%)">
        <div class="container text-center">
            <div class="section-badge mx-auto" data-reveal><i class="bi bi-journal-bookmark"></i> Program</div>
            <h1 class="section-title" data-reveal>Program <span class="text-gradient">Belajar</span></h1>
            <p class="section-description mx-auto" data-reveal>Setiap orang memiliki langkah yang berbeda. Temukan
                program yang sesuai dengan perjalanan belajar Anda.</p>
        </div>
    </section>

    @if(isset($programs) && $programs->count() > 0)
        <!-- Dynamic Programs from Database (Master Data LMS) -->
        <section class="section-padding bg-light border-bottom" aria-label="Program Terdaftar">
            <div class="container">
                <div class="program-section-title"><i class="bi bi-stars text-success me-2"></i>Modul & Program Bimbingan Aktif (LMS)</div>
                <div class="row g-4 mt-1">
                    @foreach($programs as $index => $program)
                        <div class="col-md-6 col-lg-4" data-reveal data-reveal-delay="{{ ($index % 3) * 100 }}">
                            <div class="program-card h-100 d-flex flex-column justify-content-between p-4 shadow-sm rounded-4 border bg-white">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="program-icon mb-0"><i class="bi bi-journal-check"></i></div>
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 fw-bold">{{ $program->level }}</span>
                                    </div>
                                    <h4 class="fw-bold text-dark mb-2">{{ $program->name }}</h4>
                                    <p class="text-muted small mb-3">{{ $program->description ?? 'Bimbingan Al-Qur\'an terstruktur dengan pendekatan yang personal dan sesuai adab.' }}</p>
                                </div>
                                <div class="pt-3 border-top mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="small text-secondary"><i class="bi bi-clock me-1"></i>{{ $program->duration_weeks }} Minggu</span>
                                        <span class="fw-bold text-success">Rp {{ number_format($program->price, 0, ',', '.') }}</span>
                                    </div>
                                    <a href="{{ wa_url('Assalamualaikum, saya ingin berkonsultasi mengenai program ' . $program->name) }}"
                                       class="btn btn-outline-custom w-100 rounded-pill" target="_blank">
                                       <i class="bi bi-whatsapp me-1"></i> Konsultasi Program
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Program Anak -->
    <section class="section-padding" aria-label="Program Anak">
        <div class="container">
            <div class="program-section-title"><i class="bi bi-emoji-smile"></i> Program Anak (10–15 tahun) — Utama
            </div>
            <div class="row g-4 mt-1">
                <div class="col-md-6" data-reveal>
                    <div class="program-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="program-icon"><i class="bi bi-book-half"></i></div>
                            <h4>Iqra & Dasar Al-Qur'an</h4>
                            <p>Memulai perjalanan mengenal huruf hijaiyah dan membaca Al-Qur'an secara bertahap.</p>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <a href="{{ wa_url('Assalamualaikum, saya ingin berkonsultasi mengenai program Iqra & Dasar Al-Qur\'an Anak') }}" class="btn btn-outline-custom w-100 rounded-pill" target="_blank">
                                <i class="bi bi-whatsapp me-1"></i> Konsultasi Program
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" data-reveal data-reveal-delay="100">
                    <div class="program-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="program-icon"><i class="bi bi-mic"></i></div>
                            <h4>Tahsin Dasar</h4>
                            <p>Membantu memperbaiki bacaan agar lebih baik dan sesuai dengan kaidah tajwid.</p>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <a href="{{ wa_url('Assalamualaikum, saya ingin berkonsultasi mengenai program Tahsin Dasar Anak') }}" class="btn btn-outline-custom w-100 rounded-pill" target="_blank">
                                <i class="bi bi-whatsapp me-1"></i> Konsultasi Program
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" data-reveal data-reveal-delay="200">
                    <div class="program-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="program-icon"><i class="bi bi-emoji-laughing"></i></div>
                            <h4>Adab & Doa Harian</h4>
                            <p>Mengenalkan nilai-nilai adab Islami dan doa yang dapat diamalkan dalam kehidupan sehari-hari.</p>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <a href="{{ wa_url('Assalamualaikum, saya ingin berkonsultasi mengenai program Adab & Doa Harian') }}" class="btn btn-outline-custom w-100 rounded-pill" target="_blank">
                                <i class="bi bi-whatsapp me-1"></i> Konsultasi Program
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" data-reveal data-reveal-delay="300">
                    <div class="program-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="program-icon"><i class="bi bi-clipboard2-pulse"></i></div>
                            <h4>Tahfidz Al-Qur'an</h4>
                            <p>Mendampingi anak dalam menghafal Al-Qur'an secara bertahap dengan murajaah dan pembiasaan.</p>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <a href="{{ wa_url('Assalamualaikum, saya ingin berkonsultasi mengenai program Tahfidz Al-Qur\'an Anak') }}" class="btn btn-outline-custom w-100 rounded-pill" target="_blank">
                                <i class="bi bi-whatsapp me-1"></i> Konsultasi Program
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Program Tambahan -->
    <section class="section-padding section-alt" aria-label="Program Tambahan">
        <div class="container">
            <div class="program-section-title"><i class="bi bi-person-badge"></i> Program Tambahan (Dewasa & Muslimah)
            </div>
            <div class="row g-4 mt-1">
                <div class="col-md-6" data-reveal>
                    <div class="program-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="program-icon"><i class="bi bi-book"></i></div>
                            <h4>Belajar dari Nol (Dewasa)</h4>
                            <p>Tidak pernah terlambat untuk memulai. Program untuk siapa saja yang ingin belajar dari dasar.</p>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <a href="{{ wa_url('Assalamualaikum, saya ingin berkonsultasi mengenai program Belajar dari Nol (Dewasa)') }}" class="btn btn-outline-custom w-100 rounded-pill" target="_blank">
                                <i class="bi bi-whatsapp me-1"></i> Konsultasi Program
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" data-reveal data-reveal-delay="100">
                    <div class="program-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="program-icon"><i class="bi bi-mic"></i></div>
                            <h4>Tahsin Dewasa</h4>
                            <p>Pendampingan untuk memperbaiki makhraj, tajwid, dan kualitas bacaan.</p>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <a href="{{ wa_url('Assalamualaikum, saya ingin berkonsultasi mengenai program Tahsin Dewasa') }}" class="btn btn-outline-custom w-100 rounded-pill" target="_blank">
                                <i class="bi bi-whatsapp me-1"></i> Konsultasi Program
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" data-reveal data-reveal-delay="200">
                    <div class="program-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="program-icon"><i class="bi bi-people"></i></div>
                            <h4>Kelas Muslimah</h4>
                            <p>Ruang belajar yang nyaman bagi muslimah bersama pendamping wanita.</p>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <a href="{{ wa_url('Assalamualaikum, saya ingin berkonsultasi mengenai Kelas Muslimah') }}" class="btn btn-outline-custom w-100 rounded-pill" target="_blank">
                                <i class="bi bi-whatsapp me-1"></i> Konsultasi Program
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" data-reveal data-reveal-delay="300">
                    <div class="program-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="program-icon"><i class="bi bi-clipboard2-pulse"></i></div>
                            <h4>Tahfidz Dewasa</h4>
                            <p>Mendampingi perjalanan menghafal dengan target yang disesuaikan kemampuan.</p>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <a href="{{ wa_url('Assalamualaikum, saya ingin berkonsultasi mengenai program Tahfidz Dewasa') }}" class="btn btn-outline-custom w-100 rounded-pill" target="_blank">
                                <i class="bi bi-whatsapp me-1"></i> Konsultasi Program
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bahasa Arab -->
    <section class="section-padding" aria-label="Bahasa Arab">
        <div class="container">
            <div class="program-section-title"><i class="bi bi-translate"></i> Program Bahasa Arab</div>
            <div class="row g-4 mt-1">
                <div class="col-md-6" data-reveal>
                    <div class="program-card arabic-featured h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="program-icon"><i class="bi bi-chat-dots"></i></div>
                            <h4>Bahasa Arab Dasar</h4>
                            <p>Mengenal kosakata dan percakapan dasar untuk membangun fondasi bahasa Arab.</p>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <a href="{{ wa_url('Assalamualaikum, saya ingin berkonsultasi mengenai program Bahasa Arab Dasar') }}" class="btn btn-outline-custom w-100 rounded-pill" target="_blank">
                                <i class="bi bi-whatsapp me-1"></i> Konsultasi Program
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" data-reveal data-reveal-delay="100">
                    <div class="program-card arabic-featured h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="program-icon"><i class="bi bi-book"></i></div>
                            <h4>Nahwu & Sharaf</h4>
                            <p>Mempelajari dasar-dasar tata bahasa Arab sebagai bekal memahami teks keislaman.</p>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <a href="{{ wa_url('Assalamualaikum, saya ingin berkonsultasi mengenai program Nahwu & Sharaf') }}" class="btn btn-outline-custom w-100 rounded-pill" target="_blank">
                                <i class="bi bi-whatsapp me-1"></i> Konsultasi Program
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
