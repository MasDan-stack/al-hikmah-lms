@extends('layouts.landing')

@section('title', 'Program Belajar | AL-HIKMAH')
@section('description', 'Program belajar AL-HIKMAH — Iqra, Tahsin, Tahfidz, Adab & Doa, Bahasa Arab, dan Kelas Muslimah untuk anak dan dewasa.')

@section('content')
    <!-- ============================================ -->
    <!-- 1. ETRAIN BREADCRUMB HEADER -->
    <!-- ============================================ -->
    <section class="breadcrumb_bg" aria-label="Header Program Belajar">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb_iner_item" data-reveal>
                        <div class="section-badge mx-auto mb-2"><i class="bi bi-journal-bookmark"></i> Kurikulum Pilihan</div>
                        <h2>Program Belajar <span class="text-gradient">AL-HIKMAH</span></h2>
                        <p>Setiap orang memiliki langkah yang berbeda. Temukan program yang paling sesuai dengan kebutuhan dan target perjalanan belajar Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 2. PROGRAM UTAMA ANAK (10-15 TAHUN) -->
    <!-- ============================================ -->
    <section class="py-5" aria-label="Program Anak">
        <div class="container">
            <div class="program-section-title" data-reveal>
                <i class="bi bi-emoji-smile"></i> Program Anak (10–15 tahun) — Utama
            </div>

            <div class="row g-4 mt-1">
                @foreach($anakPrograms as $index => $program)
                    <div class="col-md-6" data-reveal data-reveal-delay="{{ ($index % 2) * 100 }}">
                        <div class="program-card">
                            <div class="program-icon"><i class="bi {{ $program->icon }}"></i></div>
                            <h4>{{ $program->name }}</h4>
                            <p>{{ $program->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 3. PROGRAM TAMBAHAN (DEWASA & MUSLIMAH) -->
    <!-- ============================================ -->
    <section class="py-5 bg-white" aria-label="Program Tambahan">
        <div class="container">
            <div class="program-section-title" data-reveal>
                <i class="bi bi-person-badge"></i> Program Tambahan (Dewasa &amp; Muslimah)
            </div>

            <div class="row g-4 mt-1">
                @foreach($dewasaPrograms as $index => $program)
                    <div class="col-md-6" data-reveal data-reveal-delay="{{ ($index % 2) * 100 }}">
                        <div class="program-card">
                            <div class="program-icon"><i class="bi {{ $program->icon }}"></i></div>
                            <h4>{{ $program->name }}</h4>
                            <p>{{ $program->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 4. PROGRAM BAHASA ARAB -->
    <!-- ============================================ -->
    <section class="py-5" aria-label="Bahasa Arab">
        <div class="container">
            <div class="program-section-title" data-reveal>
                <i class="bi bi-translate"></i> Program Bahasa Arab
            </div>

            <div class="row g-4 mt-1">
                @foreach($arabPrograms as $index => $program)
                    <div class="col-md-6" data-reveal data-reveal-delay="{{ ($index % 2) * 100 }}">
                        <div class="program-card arabic-featured">
                            <div class="program-icon"><i class="bi {{ $program->icon }}"></i></div>
                            <h4>{{ $program->name }}</h4>
                            <p>{{ $program->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Hanya Tampil untuk Orang Tua (Parent) dan Admin yang Sudah Login --}}
    @auth
        @if (auth()->user()->isParent() || auth()->user()->isAdmin())
            <!-- Banner Arahkan ke Biaya -->
            <section class="py-5 bg-white border-top">
                <div class="container text-center">
                    <h4 class="fw-bold mb-2">Ingin Mengetahui Rincian Investasi &amp; Jadwal Belajar?</h4>
                    <p class="text-muted mb-4">Lihat informasi biaya transparan untuk setiap program yang Anda minati.</p>
                    @if (auth()->user()->isParent())
                        <a href="{{ route('biaya') }}" class="btn_1">
                            <i class="bi bi-tag-fill me-1"></i> Lihat Informasi Biaya &amp; Paket
                        </a>
                    @elseif (auth()->user()->isAdmin())
                        <a href="{{ route('biaya') }}" class="btn_1">
                            <i class="bi bi-tag-fill me-1"></i> Lihat Informasi Biaya &amp; Paket (Kamu Administrator)
                        </a>
                    @endif

                </div>
            </section>
        @endif
    @endauth
@endsection

