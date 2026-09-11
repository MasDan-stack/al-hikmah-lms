@extends('layouts.landing')

@section('title', 'Paket Bimbingan & Biaya | AL-HIKMAH')
@section('description', 'Rincian paket bimbingan privat Al-Qur\'an AL-HIKMAH: biaya transparan, fasilitas lengkap, dan jaminan kualitas untuk setiap santri.')

@section('content')

    {{-- 1. PAGE HEADER --}}
    <section class="py-5 bg-body-tertiary border-bottom" aria-label="Header Biaya Belajar">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-9 text-center">
                    <div data-reveal>
                        <h1 class="editorial-title mb-3">Paket Bimbingan <span class="text-emerald-deep">Privat Al-Qur'an</span></h1>
                        <p class="editorial-subtitle mx-auto">
                            Biaya transparan tanpa biaya tersembunyi. Setiap sesi 90 menit privat bersama satu guru untuk satu santri.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. AKUN & BIAYA REGISTRASI AWAL --}}
    <section class="py-5" aria-label="Informasi Pendaftaran">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10" data-reveal>
                    <div class="editorial-card p-4 p-md-5 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-4">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="editorial-icon-badge flex-shrink-0" style="width: 44px; height: 44px;">
                                    <i class="bi bi-person-check-fill"></i>
                                </div>
                                <div>
                                    <span class="badge bg-light text-emerald-deep border mb-1">Akun Terdaftar</span>
                                    <h2 class="fw-bold text-heading fs-5 mb-0">Ahlan wa Sahlan, {{ auth()->user()->name }}</h2>
                                </div>
                            </div>
                            <p class="small text-secondary mb-0" style="max-width: 560px;">
                                Seluruh bimbingan diselenggarakan secara privat 1 Guru 1 Santri, 90 menit per sesi, dengan kurikulum personal yang disesuaikan hasil evaluasi awal ananda.
                            </p>
                        </div>

                        <div class="flex-shrink-0 w-100" style="max-width: 280px;">
                            <div class="p-3.5 rounded-3 bg-body border">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="small fw-semibold text-secondary">Registrasi Santri Baru:</span>
                                    <span class="badge bg-light text-secondary border">1x di Awal</span>
                                </div>
                                <div class="tnum-price fw-bold text-emerald-deep fs-3 mb-1">
                                    Rp {{ number_format($registrationFee, 0, ',', '.') }}
                                </div>
                                <p class="small text-secondary mb-0" style="font-size: 0.76rem; line-height: 1.4;">
                                    Mencakup assessment makhraj, tajwid, penyusunan kurikulum personal, dan akun portal santri.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. PAKET UTAMA --}}
    {{-- Asymmetric layout: Featured card spans 2 columns (col-lg-6), standard cards span col-lg-3 --}}
    <section id="paket-utama" class="py-5 bg-body-tertiary border-top border-bottom" aria-label="Paket Bimbingan">
        <div class="container">

            <div class="row align-items-end mb-5" data-reveal>
                <div class="col-lg-7">
                    <h2 class="editorial-title text-start mb-2">Pilih Frekuensi &amp; Komitmen Bimbingan</h2>
                    <p class="editorial-subtitle text-start mb-0">
                        Jadwal bimbingan disepakati bersama guru privat yang ditugaskan, fleksibel di hari dan jam pilihan orang tua.
                    </p>
                </div>
                <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
                    <a href="{{ wa_url('Assalamualaikum Admin Al-Hikmah, saya ingin konsultasi mengenai paket bimbingan Al-Qur\'an.') }}"
                       target="_blank" rel="noopener"
                       class="btn-editorial-whatsapp">
                        <i class="bi bi-whatsapp me-1"></i> Tanya via WhatsApp
                    </a>
                </div>
            </div>

            @php
                $mainPackages = $programs->filter(function($p) {
                    return in_array($p->name, [
                        'Paket Tunas Istiqomah',
                        'Paket Bimbingan Mumtaz',
                        'Paket Akselerasi Itqan',
                        'Program Unggulan: Mahir Tahfidz Al-Qur\'an',
                    ]);
                });
                if ($mainPackages->isEmpty()) {
                    $mainPackages = $programs->take(4);
                }
            @endphp

            <div class="row g-4 align-items-stretch">
                @foreach($mainPackages as $index => $program)
                    @php
                        $parentEnrollment = isset($parentEnrollments) ? $parentEnrollments->firstWhere('program_id', $program->id) : null;
                        $isFeatured = $program->is_popular;
                    @endphp

                    <div class="col-md-6 col-lg-3"
                         data-reveal data-reveal-delay="{{ ($index % 4) * 80 }}">

                        <div class="{{ $isFeatured ? 'editorial-card-featured' : 'editorial-card' }} p-4 h-100 d-flex flex-column justify-content-between {{ $parentEnrollment ? 'border-success' : '' }}">

                            <div>
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-3" style="min-height: 28px;">
                                    <span class="badge bg-light text-emerald-deep border">
                                        {{ $program->level }}
                                    </span>
                                    @if($parentEnrollment)
                                        <span class="badge bg-success text-white">
                                            <i class="bi bi-check-circle-fill me-1" aria-hidden="true"></i> Terdaftar
                                        </span>
                                    @elseif($isFeatured)
                                        <span class="badge text-white" style="background: var(--emerald-deep);">
                                            <i class="bi bi-star-fill text-warning me-1" aria-hidden="true"></i> Paling Diminati
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-2" style="min-height: 54px;">
                                    <h3 class="fw-bold text-heading fs-5 mb-0">
                                        {{ $program->name }}
                                    </h3>
                                </div>

                                <span class="badge bg-light text-secondary border mb-3 d-inline-block">
                                    <i class="bi bi-clock text-emerald-deep me-1" aria-hidden="true"></i> 90 Menit / Sesi Privat
                                </span>

                                @if($parentEnrollment)
                                    @if($parentEnrollment->isWaitingAdmin())
                                        <div class="alert alert-warning border-0 py-2 px-3 mb-3 rounded-3 text-start small">
                                            <div class="d-flex align-items-center gap-2 fw-bold text-dark mb-1">
                                                <i class="bi bi-hourglass-split text-warning"></i> Sedang Direview
                                            </div>
                                            <div class="text-secondary" style="font-size:0.8rem;">
                                                Santri: <strong>{{ $parentEnrollment->student?->getDisplayName() }}</strong><br>
                                                Lembaga sedang mereview jadwal dan ketersediaan guru.
                                            </div>
                                        </div>
                                    @elseif($parentEnrollment->isWaitingParent())
                                        <div class="alert alert-info border-0 py-2 px-3 mb-3 rounded-3 text-start small">
                                            <div class="d-flex align-items-center gap-2 fw-bold text-info-emphasis mb-1">
                                                <i class="bi bi-chat-dots-fill text-info"></i> Menunggu Respon Anda
                                            </div>
                                            <div class="text-secondary" style="font-size:0.8rem;">
                                                Santri: <strong>{{ $parentEnrollment->student?->getDisplayName() }}</strong><br>
                                                Ada tawaran alternatif jadwal dari lembaga.
                                            </div>
                                        </div>
                                    @elseif($parentEnrollment->isConfirmed())
                                        <div class="alert alert-primary border-0 py-2 px-3 mb-3 rounded-3 text-start small">
                                            <div class="d-flex align-items-center gap-2 fw-bold text-primary-emphasis mb-1">
                                                <i class="bi bi-check-circle-fill text-primary"></i> Jadwal Deal (Siap Bayar)
                                            </div>
                                            <div class="text-secondary" style="font-size:0.8rem;">
                                                Santri: <strong>{{ $parentEnrollment->student?->getDisplayName() }}</strong><br>
                                                Guru: <strong>{{ $parentEnrollment->mentor?->getDisplayName() ?? 'Ditentukan Lembaga' }}</strong>
                                            </div>
                                        </div>
                                    @elseif($parentEnrollment->isActive())
                                        <div class="alert alert-success border-0 py-2 px-3 mb-3 rounded-3 text-start small">
                                            <div class="d-flex align-items-center gap-2 fw-bold text-success mb-1">
                                                <i class="bi bi-award-fill text-success"></i> Bimbingan Aktif
                                            </div>
                                            <div class="text-secondary" style="font-size:0.8rem;">
                                                Santri: <strong>{{ $parentEnrollment->student?->getDisplayName() }}</strong><br>
                                                Guru: <strong>{{ $parentEnrollment->mentor?->getDisplayName() ?? 'Guru Aktif' }}</strong>
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex align-items-baseline gap-1 mb-1">
                                        <span class="tnum-price fw-bold text-emerald-deep fs-2">{{ $program->formatted_price }}</span>
                                        <span class="text-secondary small">/ bulan</span>
                                    </div>
                                    <div class="small text-secondary">
                                        <i class="bi bi-calendar-check text-emerald-deep me-1"></i>
                                        {{ $program->duration_weeks }} Pertemuan per Bulan
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex align-items-start gap-2 mb-2 small text-secondary">
                                        <i class="bi bi-check2-circle text-emerald-deep fs-6 mt-0.5 flex-shrink-0"></i>
                                        <span>Model Privat Intensif (1 Guru 1 Santri)</span>
                                    </div>
                                    <div class="d-flex align-items-start gap-2 mb-2 small text-secondary">
                                        <i class="bi bi-check2-circle text-emerald-deep fs-6 mt-0.5 flex-shrink-0"></i>
                                        <span>Durasi 90 menit per pertemuan</span>
                                    </div>
                                    <div class="d-flex align-items-start gap-2 mb-2 small text-secondary">
                                        <i class="bi bi-check2-circle text-emerald-deep fs-6 mt-0.5 flex-shrink-0"></i>
                                        <span>Modul materi &amp; lembar mutabaah digital</span>
                                    </div>
                                    <div class="d-flex align-items-start gap-2 mb-2 small text-secondary">
                                        <i class="bi bi-check2-circle text-emerald-deep fs-6 mt-0.5 flex-shrink-0"></i>
                                        <span>Laporan evaluasi tajwid berkala ke orang tua</span>
                                    </div>
                                    @if(str_contains($program->name, 'Tahfidz'))
                                        <div class="d-flex align-items-start gap-2 mb-2 small text-secondary">
                                            <i class="bi bi-check2-circle text-emerald-deep fs-6 mt-0.5 flex-shrink-0"></i>
                                            <span>Target hafalan mandiri &amp; murajaah mutqin</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="pt-3 border-top mt-auto">
                                @if(auth()->user()->isParent())
                                    @if($parentEnrollment)
                                        @if($parentEnrollment->isWaitingAdmin() || $parentEnrollment->isWaitingParent() || $parentEnrollment->isConfirmed() || $parentEnrollment->isActive())
                                            <a href="{{ route('parent.enrollments.show', $parentEnrollment->id) }}"
                                               class="btn-editorial-primary w-100 text-center justify-content-center">
                                                <i class="bi bi-eye me-1"></i> Lihat Status Bimbingan
                                            </a>
                                        @else
                                            <a href="{{ route('parent.enrollments.create', ['program_id' => $program->id]) }}"
                                               class="{{ $program->is_popular ? 'btn-editorial-primary' : 'btn-editorial-secondary' }} w-100 text-center justify-content-center">
                                                <i class="bi bi-calendar-plus me-1"></i> Pilih Paket &amp; Tentukan Jadwal
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('parent.enrollments.create', ['program_id' => $program->id]) }}"
                                           class="{{ $program->is_popular ? 'btn-editorial-primary' : 'btn-editorial-secondary' }} w-100 text-center justify-content-center">
                                            <i class="bi bi-calendar-plus me-1"></i> Pilih Paket &amp; Tentukan Jadwal
                                        </a>
                                    @endif
                                @elseif(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.enrollments.index') }}"
                                       class="btn-editorial-secondary w-100 text-center justify-content-center">
                                        <i class="bi bi-gear me-1"></i> Kelola Pendaftaran (Admin)
                                    </a>
                                @endif
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-5 pt-2" data-reveal>
                <p class="text-muted small fst-italic mb-0">
                    "Setiap santri memiliki kecepatan belajar yang berbeda. Tim akademik Al-Hikmah siap mencocokkan guru pembimbing berdasarkan karakter dan hasil evaluasi awal ananda."
                </p>
            </div>

        </div>
    </section>

    {{-- 4. JAMINAN LAYANAN --}}
    <section class="py-5" aria-label="Jaminan Layanan">
        <div class="container">
            <div class="row justify-content-center mb-5" data-reveal>
                <div class="col-lg-7 text-center">
                    <h2 class="editorial-title mb-2">Komitmen &amp; <span class="text-emerald-deep">Jaminan Layanan</span></h2>
                    <p class="editorial-subtitle mx-auto">
                        Bukan sekadar les, melainkan pendampingan yang bertanggung jawab penuh terhadap kenyamanan dan capaian mengaji ananda.
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-sm-6 col-lg-3" data-reveal>
                    <div class="editorial-card p-4 h-100">
                        <div class="editorial-icon-badge mb-3" aria-hidden="true">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <h3 class="fw-bold fs-6 text-heading mb-2">Ganti Guru Tanpa Biaya</h3>
                        <p class="text-secondary small mb-0">
                            Bila ananda kurang nyaman dalam 2 pertemuan pertama, orang tua dapat mengajukan penggantian guru tanpa biaya tambahan.
                        </p>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3" data-reveal data-reveal-delay="100">
                    <div class="editorial-card p-4 h-100">
                        <div class="editorial-icon-badge mb-3" aria-hidden="true">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <h3 class="fw-bold fs-6 text-heading mb-2">Sesi Pengganti Fleksibel</h3>
                        <p class="text-secondary small mb-0">
                            Konfirmasi min. 6 jam sebelum sesi jika berhalangan hadir. Sesi tidak hangus, dijadwalkan di hari lain.
                        </p>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3" data-reveal data-reveal-delay="200">
                    <div class="editorial-card p-4 h-100">
                        <div class="editorial-icon-badge mb-3" aria-hidden="true">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h3 class="fw-bold fs-6 text-heading mb-2">Rapor Mutabaah Berkala</h3>
                        <p class="text-secondary small mb-0">
                            Setiap sesi tercatat di portal: capaian halaman, catatan makhraj, dan rekomendasi latihan murajaah di rumah.
                        </p>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3" data-reveal data-reveal-delay="300">
                    <div class="editorial-card p-4 h-100">
                        <div class="editorial-icon-badge mb-3" aria-hidden="true">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <h3 class="fw-bold fs-6 text-heading mb-2">Harga Tetap, Transparan</h3>
                        <p class="text-secondary small mb-0">
                            Nominal paket bimbingan bersifat tetap, sudah mencakup modul materi digital dan evaluasi tajwid berkala.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. FAQ --}}
    <section class="py-5 bg-body-tertiary border-top" aria-label="Pertanyaan Seputar Paket">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="text-center mb-5" data-reveal>
                        <h2 class="editorial-title mb-2">Pertanyaan Seputar <span class="text-emerald-deep">Paket &amp; Pembayaran</span></h2>
                        <p class="editorial-subtitle mx-auto mb-0">Informasi lengkap mengenai fleksibilitas bimbingan dan prosedur administrasi.</p>
                    </div>

                    <div class="accordion accordion-flush" id="accordionFaqBiaya" data-reveal>

                        <div class="accordion-item mb-3 rounded-3 border overflow-hidden">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faqBiaya1"
                                        aria-expanded="false" aria-controls="faqBiaya1">
                                    Apakah ada penyesuaian untuk pendaftaran lebih dari satu anak dalam satu keluarga?
                                </button>
                            </h3>
                            <div id="faqBiaya1" class="accordion-collapse collapse" data-bs-parent="#accordionFaqBiaya">
                                <div class="accordion-body text-secondary small">
                                    Ya, tersedia kemudahan bagi keluarga yang mendaftarkan dua santri atau lebih dalam satu sesi kunjungan (Home Visit) atau jadwal online berturutan. Hubungi admin kami untuk rekomendasi pengaturan jadwal keluarga.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3 rounded-3 border overflow-hidden">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faqBiaya2"
                                        aria-expanded="false" aria-controls="faqBiaya2">
                                    Bagaimana alur pembayaran setelah paket dipilih?
                                </button>
                            </h3>
                            <div id="faqBiaya2" class="accordion-collapse collapse" data-bs-parent="#accordionFaqBiaya">
                                <div class="accordion-body text-secondary small">
                                    Setelah jadwal diverifikasi admin dan disetujui, orang tua melakukan pembayaran via transfer bank atau QRIS yang tersedia di dasbor LMS Al-Hikmah.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3 rounded-3 border overflow-hidden">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faqBiaya3"
                                        aria-expanded="false" aria-controls="faqBiaya3">
                                    Apa yang terjadi jika santri tidak dapat hadir di salah satu sesi?
                                </button>
                            </h3>
                            <div id="faqBiaya3" class="accordion-collapse collapse" data-bs-parent="#accordionFaqBiaya">
                                <div class="accordion-body text-secondary small">
                                    Sesi tidak hangus apabila orang tua memberitahu guru atau admin minimal 6 jam sebelum jadwal dimulai. Sesi pengganti dijadwalkan di hari lain yang disepakati bersama.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item rounded-3 border overflow-hidden">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faqBiaya4"
                                        aria-expanded="false" aria-controls="faqBiaya4">
                                    Untuk sesi Home Visit, apakah guru membawa perlengkapan belajar sendiri?
                                </button>
                            </h3>
                            <div id="faqBiaya4" class="accordion-collapse collapse" data-bs-parent="#accordionFaqBiaya">
                                <div class="accordion-body text-secondary small">
                                    Guru membawa panduan kurikulum, lembar mutabaah fisik, dan alat bantu makhraj. Orang tua cukup menyediakan mushaf atau Iqra milik ananda, serta ruangan yang tenang untuk proses bimbingan.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
