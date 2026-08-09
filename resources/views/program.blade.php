@extends('layouts.landing')

@section('title', 'Program Belajar | AL-HIKMAH')
@section('description', 'Program belajar AL-HIKMAH — Iqra, Tahsin, Tahfidz, Adab & Doa, Bahasa Arab, dan Kelas Muslimah
    untuk anak dan dewasa.')

@section('content')
    <!-- Page Header -->
    <section class="page-header section-padding"
        style="padding-top:120px;background:linear-gradient(170deg,var(--bg-primary)0%,var(--primary-lighter)100%)">
        <div class="container text-center">
            <div class="section-badge mx-auto" data-reveal><i class="bi bi-journal-bookmark"></i> Program</div>
            <h1 class="section-title" data-reveal>Program <span class="text-gradient">Belajar</span></h1>
            <p class="section-description mx-auto" data-reveal>Setiap orang memiliki langkah yang berbeda. Temukan program
                yang sesuai dengan perjalanan belajar Anda.</p>
        </div>
    </section>

    <!-- Program Anak -->
    <section class="section-padding" aria-label="Program Anak">
        <div class="container">
            <div class="program-section-title"><i class="bi bi-emoji-smile"></i> Program Anak (10–15 tahun) — Utama</div>
            <div class="row g-4 mt-1">
                <div class="col-md-6" data-reveal>
                    <div class="program-card">
                        <div class="program-icon"><i class="bi bi-book-half"></i></div>
                        <h4>Iqra & Dasar Al-Qur'an</h4>
                        <p>Memulai perjalanan mengenal huruf hijaiyah dan membaca Al-Qur'an secara bertahap.</p>
                    </div>
                </div>
                <div class="col-md-6" data-reveal data-reveal-delay="100">
                    <div class="program-card">
                        <div class="program-icon"><i class="bi bi-mic"></i></div>
                        <h4>Tahsin Dasar</h4>
                        <p>Membantu memperbaiki bacaan agar lebih baik dan sesuai dengan kaidah tajwid.</p>
                    </div>
                </div>
                <div class="col-md-6" data-reveal data-reveal-delay="200">
                    <div class="program-card">
                        <div class="program-icon"><i class="bi bi-emoji-laughing"></i></div>
                        <h4>Adab & Doa Harian</h4>
                        <p>Mengenalkan nilai-nilai adab Islami dan doa yang dapat diamalkan dalam kehidupan sehari-hari.</p>
                    </div>
                </div>
                <div class="col-md-6" data-reveal data-reveal-delay="300">
                    <div class="program-card">
                        <div class="program-icon"><i class="bi bi-clipboard2-pulse"></i></div>
                        <h4>Tahfidz Al-Qur'an</h4>
                        <p>Mendampingi anak dalam menghafal Al-Qur'an secara bertahap dengan murajaah dan pembiasaan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Program Tambahan -->
    <section class="section-padding section-alt" aria-label="Program Tambahan">
        <div class="container">
            <div class="program-section-title"><i class="bi bi-person-badge"></i> Program Tambahan (Dewasa & Muslimah)</div>
            <div class="row g-4 mt-1">
                <div class="col-md-6" data-reveal>
                    <div class="program-card">
                        <div class="program-icon"><i class="bi bi-book"></i></div>
                        <h4>Belajar dari Nol (Dewasa)</h4>
                        <p>Tidak pernah terlambat untuk memulai. Program untuk siapa saja yang ingin belajar dari dasar.</p>
                    </div>
                </div>
                <div class="col-md-6" data-reveal data-reveal-delay="100">
                    <div class="program-card">
                        <div class="program-icon"><i class="bi bi-mic"></i></div>
                        <h4>Tahsin Dewasa</h4>
                        <p>Pendampingan untuk memperbaiki makhraj, tajwid, dan kualitas bacaan.</p>
                    </div>
                </div>
                <div class="col-md-6" data-reveal data-reveal-delay="200">
                    <div class="program-card">
                        <div class="program-icon"><i class="bi bi-people"></i></div>
                        <h4>Kelas Muslimah</h4>
                        <p>Ruang belajar yang nyaman bagi muslimah bersama pendamping wanita.</p>
                    </div>
                </div>
                <div class="col-md-6" data-reveal data-reveal-delay="300">
                    <div class="program-card">
                        <div class="program-icon"><i class="bi bi-clipboard2-pulse"></i></div>
                        <h4>Tahfidz Dewasa</h4>
                        <p>Mendampingi perjalanan menghafal dengan target yang disesuaikan kemampuan.</p>
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
                    <div class="program-card arabic-featured">
                        <div class="program-icon"><i class="bi bi-chat-dots"></i></div>
                        <h4>Bahasa Arab Dasar</h4>
                        <p>Mengenal kosakata dan percakapan dasar untuk membangun fondasi bahasa Arab.</p>
                    </div>
                </div>
                <div class="col-md-6" data-reveal data-reveal-delay="100">
                    <div class="program-card arabic-featured">
                        <div class="program-icon"><i class="bi bi-book"></i></div>
                        <h4>Nahwu & Sharaf</h4>
                        <p>Mempelajari dasar-dasar tata bahasa Arab sebagai bekal memahami teks keislaman.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
