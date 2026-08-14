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

    <!-- 1. Program Anak (10–15 tahun) — Utama -->
    <section class="section-padding" aria-label="Program Anak">
        <div class="container">
            <div class="program-section-title"><i class="bi bi-emoji-smile"></i> Program Anak (10–15 tahun) — Utama</div>
            <div class="row g-4 mt-1">
                @foreach($anakPrograms as $index => $program)
                    <div class="col-md-6" data-reveal data-reveal-delay="{{ ($index % 2) * 100 }}">
                        <div class="program-card h-100">
                            <div class="program-icon"><i class="bi {{ $program->icon }}"></i></div>
                            <h4>{{ $program->name }}</h4>
                            <p>{{ $program->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 2. Program Tambahan (Dewasa & Muslimah) -->
    <section class="section-padding section-alt" aria-label="Program Tambahan">
        <div class="container">
            <div class="program-section-title"><i class="bi bi-person-badge"></i> Program Tambahan (Dewasa & Muslimah)</div>
            <div class="row g-4 mt-1">
                @foreach($dewasaPrograms as $index => $program)
                    <div class="col-md-6" data-reveal data-reveal-delay="{{ ($index % 2) * 100 }}">
                        <div class="program-card h-100">
                            <div class="program-icon"><i class="bi {{ $program->icon }}"></i></div>
                            <h4>{{ $program->name }}</h4>
                            <p>{{ $program->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 3. Program Bahasa Arab -->
    <section class="section-padding" aria-label="Bahasa Arab">
        <div class="container">
            <div class="program-section-title"><i class="bi bi-translate"></i> Program Bahasa Arab</div>
            <div class="row g-4 mt-1">
                @foreach($arabPrograms as $index => $program)
                    <div class="col-md-6" data-reveal data-reveal-delay="{{ ($index % 2) * 100 }}">
                        <div class="program-card arabic-featured h-100">
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
                    <h4 class="fw-bold mb-2">Ingin Mengetahui Rincian Investasi & Jadwal Belajar?</h4>
                    <p class="text-muted mb-4">Lihat informasi biaya transparan untuk setiap program yang Anda minati.</p>
                    @if (auth()->user()->isParent())
                        <a href="{{ route('biaya') }}" class="btn btn-primary-custom px-4 py-2 rounded-pill">
                            <i class="bi bi-tag-fill me-2"></i> Lihat Informasi Biaya & Paket
                        </a>
                    @elseif (auth()->user()->isAdmin())
                        <a href="{{ route('biaya') }}" class="btn btn-primary-custom px-4 py-2 rounded-pill">
                            <i class="bi bi-tag-fill me-2"></i> Lihat Informasi Biaya & Paket (Kamu Administrator)
                        </a>
                    @endif
                </div>
            </section>
        @endif
    @endauth
@endsection
