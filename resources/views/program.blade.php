@extends('layouts.landing')

@section('title', 'Program Belajar | AL-HIKMAH')
@section('description', 'Program belajar AL-HIKMAH meliputi Iqra, Tahsin, Tahfidz, Adab & Doa, Bahasa Arab, dan Kelas Muslimah untuk anak dan dewasa.')

@section('content')
    <!-- ============================================ -->
    <!-- ============================================ -->
    <!-- 1. EDITORIAL SUBPAGE HEADER -->
    <!-- ============================================ -->
    <section class="editorial-page-header" aria-label="Header Program Belajar">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 text-center" data-reveal>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Program Belajar</li>
                        </ol>
                    </nav>

                    <div class="editorial-badge mx-auto">
                        <i class="bi bi-journal-bookmark-fill"></i>
                        <span>Kurikulum Pilihan &amp; Terarah</span>
                    </div>

                    <h1 class="editorial-title">Program Belajar <span class="text-emerald-deep">AL-HIKMAH</span></h1>
                    <p class="editorial-subtitle mx-auto">
                        Setiap santri memiliki ritme dan langkah yang berbeda. Temukan program yang paling sesuai dengan kebutuhan ananda dan keluarga untuk perjalanan belajar yang tenang dan teratur.
                    </p>
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
                <i class="bi bi-emoji-smile"></i> Program Anak (10–15 tahun) (Program Utama)
            </div>

            <div class="row g-4 mt-1">
                @foreach($anakPrograms as $index => $program)
                    @php
                        $iconClass = str_starts_with($program->icon ?? '', 'bi-') ? $program->icon : 'bi-' . ($program->icon ?? 'book');
                        if ($iconClass === 'bi-seedling') { $iconClass = 'bi-flower1'; }
                    @endphp
                    <div class="col-md-6" data-reveal data-reveal-delay="{{ ($index % 2) * 100 }}">
                        <div class="program-card">
                            <div class="program-icon"><i class="bi {{ $iconClass }}"></i></div>
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
                    @php
                        $iconClass = str_starts_with($program->icon ?? '', 'bi-') ? $program->icon : 'bi-' . ($program->icon ?? 'people');
                        if ($iconClass === 'bi-seedling') { $iconClass = 'bi-flower1'; }
                    @endphp
                    <div class="col-md-6" data-reveal data-reveal-delay="{{ ($index % 2) * 100 }}">
                        <div class="program-card">
                            <div class="program-icon"><i class="bi {{ $iconClass }}"></i></div>
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
                    @php
                        $iconClass = str_starts_with($program->icon ?? '', 'bi-') ? $program->icon : 'bi-' . ($program->icon ?? 'chat-dots');
                        if ($iconClass === 'bi-seedling') { $iconClass = 'bi-flower1'; }
                    @endphp
                    <div class="col-md-6" data-reveal data-reveal-delay="{{ ($index % 2) * 100 }}">
                        <div class="program-card arabic-featured">
                            <div class="program-icon"><i class="bi {{ $iconClass }}"></i></div>
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

