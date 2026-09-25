@extends('layouts.landing')

@section('title', 'Paket Bimbingan & Biaya | AL-HIKMAH')
@section('description', 'Rincian paket bimbingan privat Al-Qur\'an AL-HIKMAH: biaya transparan, fasilitas lengkap, dan jaminan kualitas untuk setiap santri.')

@section('content')

    {{-- 1. PAGE HEADER --}}
    <section class="editorial-page-header" aria-label="Header Biaya Belajar">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 text-center" data-reveal>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Paket Bimbingan &amp; Biaya</li>
                        </ol>
                    </nav>

                    <div class="editorial-badge mx-auto">
                        <i class="bi bi-tag-fill"></i>
                        <span>Biaya Transparan &amp; Akad Jelas</span>
                    </div>

                    <h1 class="editorial-title">Paket Bimbingan <span class="text-emerald-deep">Privat Al-Qur'an</span></h1>
                    <p class="editorial-subtitle mx-auto">
                        Setiap pertemuan berlangsung selama 90 menit penuh, mempertemukan satu guru dengan satu santri secara tenang. Ananda mendapatkan bimbingan tartil yang runtut, disimak dengan sabar, dan dipantau melalui catatan perkembangan harian.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. AKUN & BIAYA REGISTRASI AWAL --}}
    <section class="py-5" aria-label="Informasi Pendaftaran">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10" data-reveal>
                    <div class="editorial-card p-4 p-md-5 rounded-4 shadow-sm border position-relative overflow-hidden">
                        {{-- Subtle background decoration --}}
                        <div class="position-absolute top-0 end-0 p-4 opacity-10 pointer-events-none d-none d-md-block" style="transform: translate(20%, -20%);" aria-hidden="true">
                            <i class="bi bi-award-fill" style="font-size: 14rem; color: var(--primary);"></i>
                        </div>

                        <div class="row g-4 align-items-center position-relative">
                            {{-- Sisi Kiri: Profil & Komitmen Bimbingan --}}
                            <div class="col-lg-7">
                                <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-success-subtle text-success small fw-semibold mb-3">
                                    <i class="bi bi-patch-check-fill"></i> Akun Wali Santri Terdaftar
                                </div>
                                <h2 class="editorial-title fs-3 mb-2">
                                    Ahlan wa Sahlan, <span class="text-emerald-deep">{{ auth()->user()->name }}</span>
                                </h2>
                                <p class="text-secondary small mb-4" style="line-height: 1.6; max-width: 540px;">
                                    Selamat datang di portal bimbingan Al-Qur'an Al-Hikmah. Seluruh sesi belajar diselenggarakan secara privat 1 guru untuk 1 santri, berdurasi 90 menit penuh, dengan kurikulum personal yang dirancang khusus sesuai karakter dan hasil evaluasi ananda.
                                </p>

                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-start gap-2.5">
                                            <div class="rounded-circle p-1.5 bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                                                <i class="bi bi-bullseye fs-6"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-heading small">Kurikulum Personal</div>
                                                <small class="text-secondary" style="font-size: 0.74rem;">Disesuaikan ritme &amp; karakter anak</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-start gap-2.5">
                                            <div class="rounded-circle p-1.5 bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                                                <i class="bi bi-clock-history fs-6"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-heading small">90 Menit Penuh</div>
                                                <small class="text-secondary" style="font-size: 0.74rem;">Talaqqi sabar tanpa antre giliran</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-start gap-2.5">
                                            <div class="rounded-circle p-1.5 bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                                                <i class="bi bi-phone fs-6"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-heading small">Rapor Mutaba'ah Real-Time</div>
                                                <small class="text-secondary" style="font-size: 0.74rem;">Pantau capaian hafalan dari HP</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-start gap-2.5">
                                            <div class="rounded-circle p-1.5 bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                                                <i class="bi bi-shield-check fs-6"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-heading small">Garansi Guru Cocok</div>
                                                <small class="text-secondary" style="font-size: 0.74rem;">Kenyamanan belajar santri utama</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sisi Kanan: Kartu Investasi Registrasi Awal (Membership Pass Style) --}}
                            <div class="col-lg-5">
                                <div class="membership-pass-card position-relative">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="badge badge-warning-contrast fw-bold px-3 py-1.5 rounded-pill small">
                                            <i class="bi bi-shield-check me-1"></i> CUKUP 1X DIAWAL
                                        </span>
                                        <span class="badge bg-white text-emerald-deep fw-bold rounded-pill px-3 py-1.5 shadow-sm border border-light-subtle" style="color: #064e3b !important; background-color: #ffffff !important; font-size: 0.8rem; letter-spacing: 0.3px;">
                                            <i class="bi bi-person-check-fill me-1 text-success"></i> Santri Baru
                                        </span>
                                    </div>

                                    <div class="text-white text-opacity-85 small mt-3 fw-medium">Biaya Registrasi &amp; Asesmen Diagnostik</div>
                                    <div class="fw-bold text-white fs-1 mb-2 font-monospace">
                                        Rp {{ number_format($registrationFee, 0, ',', '.') }}
                                    </div>

                                    <div class="membership-pass-notice mb-3">
                                        <div class="membership-pass-notice-title">
                                            <i class="bi bi-check-circle-fill"></i> 1x Pendaftaran untuk Selamanya
                                        </div>
                                        <p class="membership-pass-notice-text">
                                            Bebas biaya daftar ulang berkala dan <strong>tanpa cicilan bulanan untuk registrasi</strong>. Orang tua hanya membayar biaya paket bimbingan aktif.
                                        </p>
                                    </div>

                                    <div class="border-top pt-3 border-white border-opacity-15 d-flex flex-column gap-2">
                                        <div class="d-flex align-items-center gap-2 small text-white text-opacity-95" style="font-size: 0.82rem;">
                                            <i class="bi bi-check2-circle text-warning fs-6 flex-shrink-0"></i>
                                            <span>Asesmen diagnostik makhraj dan tajwid awal</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 small text-white text-opacity-95" style="font-size: 0.82rem;">
                                            <i class="bi bi-check2-circle text-warning fs-6 flex-shrink-0"></i>
                                            <span>Penyusunan kurikulum dan target juz personal</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 small text-white text-opacity-95" style="font-size: 0.82rem;">
                                            <i class="bi bi-check2-circle text-warning fs-6 flex-shrink-0"></i>
                                            <span>Aktivasi akun sistem mutaba'ah santri dan wali</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 small text-white text-opacity-95" style="font-size: 0.82rem;">
                                            <i class="bi bi-check2-circle text-warning fs-6 flex-shrink-0"></i>
                                            <span>Garansi pergantian guru pembimbing jika tidak cocok</span>
                                        </div>
                                    </div>

                                    <div class="pt-3 mt-3 border-top border-white border-opacity-15 text-white text-opacity-85 small d-flex align-items-center gap-2" style="font-size: 0.78rem;">
                                        <i class="bi bi-patch-check-fill text-warning"></i>
                                        <span>Berlaku aktif permanen selama santri belajar di Al-Hikmah.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 2.5 KOMPARASI BIAYA & NILAI --}}
    <section class="py-5" aria-label="Perbandingan Nilai Bimbingan">
        <div class="container">
            <div class="row justify-content-center mb-4" data-reveal>
                <div class="col-lg-8 text-center">
                    <h2 class="editorial-title fs-3 mb-2">Mengapa Bimbingan Privat di <span class="text-emerald-deep">Al-Hikmah Berbeda?</span></h2>
                    <p class="editorial-subtitle mx-auto">Pemahaman rasional mengapa durasi dan fokus guru sangat menentukan kualitas bacaan ananda.</p>
                </div>
            </div>
            <div class="row justify-content-center" data-reveal data-reveal-delay="100">
                <div class="col-lg-10">
                    <div class="table-responsive rounded-4 shadow-sm border mb-4">
                        <table class="table table-hover align-middle mb-0 comparison-table">
                            <thead>
                                <tr>
                                    <th scope="col" class="py-3 px-4 fw-semibold text-heading w-25 border-end">Aspek Belajar</th>
                                    <th scope="col" class="py-3 px-4 fw-semibold text-secondary w-25 border-end text-center">Ngaji Biasa / TPA</th>
                                    <th scope="col" class="py-3 px-4 fw-bold comparison-col-highlight w-50 text-center"><i class="bi bi-star-fill text-warning me-1"></i> Privat 1-on-1 Al-Hikmah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="py-3 px-4 fw-medium text-heading border-end">Rasio Guru : Murid</td>
                                    <td class="py-3 px-4 text-center text-secondary border-end small">1 Guru : 15 - 20 Santri</td>
                                    <td class="py-3 px-4 text-center text-heading fw-semibold small comparison-col-highlight">1 Guru : 1 Santri (Fokus 100%)</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 fw-medium text-heading border-end">Waktu Disimak Guru</td>
                                    <td class="py-3 px-4 text-center text-secondary border-end small">Cuma 5 - 7 menit (sisanya mengantre)</td>
                                    <td class="py-3 px-4 text-center text-heading fw-semibold small comparison-col-highlight">90 Menit Penuh (Fokus ke Ananda)</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 fw-medium text-heading border-end">Laporan Progres</td>
                                    <td class="py-3 px-4 text-center text-secondary border-end small">Tidak ada / buku paraf manual</td>
                                    <td class="py-3 px-4 text-center text-heading fw-semibold small comparison-col-highlight">Rapor Digital Real-Time di HP Orang Tua</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 fw-medium text-heading border-end">Kurikulum Belajar</td>
                                    <td class="py-3 px-4 text-center text-secondary border-end small">Disamaratakan seluruh kelas</td>
                                    <td class="py-3 px-4 text-center text-heading fw-semibold small comparison-col-highlight">Personal sesuai ritme kecepatan ananda</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 fw-medium text-heading border-end">Hasil Belajar</td>
                                    <td class="py-3 px-4 text-center text-secondary border-end small">Sering berbulan-bulan jalan di tempat</td>
                                    <td class="py-3 px-4 text-center text-success fw-bold small comparison-col-highlight">3 - 4x Lebih Cepat Lancar &amp; Mutqin</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Reframing & Pesan Kunci -->
                    <div class="row g-3">
                        <div class="col-md-7">
                            <div class="editorial-card p-3 p-md-4 h-100 d-flex gap-3 align-items-start border shadow-sm rounded-4">
                                <div class="rounded-circle p-2 bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                                    <i class="bi bi-clock-history fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-heading small mb-1">Nilai Waktu Emas Ananda</div>
                                    <p class="text-secondary small mb-0" style="line-height: 1.5;">
                                        Satu sesi 90 menit di Al-Hikmah setara dengan 3 minggu anak mengantre di pengajian umum. Ayah dan Bunda menghemat waktu emas di usia tumbuh kembang ananda.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="editorial-card p-3 p-md-4 h-100 d-flex gap-3 align-items-start border shadow-sm rounded-4">
                                <div class="rounded-circle p-2 bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                                    <i class="bi bi-cup-hot-fill fs-5 text-warning"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-heading small mb-1">Investasi Seharga Jajan Harian</div>
                                    <p class="text-secondary small mb-0" style="line-height: 1.5;">
                                        Hanya mulai <strong>Rp 20.000 per hari</strong> (seharga segelas es kopi susu atau jajan ananda) untuk bekal bacaan Al-Qur'an yang ia bawa seumur hidup.
                                    </p>
                                </div>
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
        <div class="container-xl">

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
                        $cleanLevel = str_replace(['Paling Diminati (', ')'], '', $program->level);
                        if (trim($cleanLevel) === '8 Sesi/Bulan') {
                            $cleanLevel = '8 Sesi / Bulan';
                        }
                    @endphp

                    <div class="col-12 col-md-6 col-xl-3"
                         data-reveal data-reveal-delay="{{ ($index % 4) * 80 }}">

                        <div class="{{ $isFeatured ? 'editorial-card-featured' : 'editorial-card' }} p-4 h-100 d-flex flex-column justify-content-between {{ $parentEnrollment ? 'border-success' : '' }}">

                            <div>
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-3" style="min-height: 32px;">
                                    <span class="badge badge-program-level rounded-pill px-2.5 py-1 text-truncate" style="max-width: 130px;" title="{{ $cleanLevel }}">
                                        {{ $cleanLevel }}
                                    </span>
                                    @if($parentEnrollment)
                                        <span class="badge bg-success text-white rounded-pill px-2.5 py-1 flex-shrink-0">
                                            <i class="bi bi-check-circle-fill me-1" aria-hidden="true"></i> Terdaftar
                                        </span>
                                    @elseif($isFeatured)
                                        <span class="badge bg-emerald-deep text-white rounded-pill px-2.5 py-1 flex-shrink-0 shadow-xs" style="white-space: nowrap; font-size: 0.76rem; letter-spacing: 0.2px;">
                                            <i class="bi bi-star-fill text-warning me-1" aria-hidden="true"></i> Paling Diminati
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-2" style="min-height: 54px;">
                                    <h3 class="fw-bold text-heading fs-5 mb-0">
                                        {{ $program->name }}
                                    </h3>
                                </div>

                                <span class="badge text-secondary border mb-3 d-inline-block rounded-pill px-2.5 py-1" style="background: var(--bg-tertiary);">
                                    <i class="bi bi-clock text-emerald-deep me-1" aria-hidden="true"></i> 90 Menit / Sesi Privat
                                </span>

                                @if($parentEnrollment)
                                    @if($parentEnrollment->isWaitingAdmin())
                                        <div class="alert alert-warning border-0 py-2 px-3 mb-3 rounded-3 text-start small">
                                            <div class="d-flex align-items-center gap-2 fw-bold text-heading mb-1">
                                                <i class="bi bi-hourglass-split text-warning"></i> Status: Sedang Direview
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
                                                <i class="bi bi-award-fill text-success"></i> Status: Bimbingan Aktif Berjalan
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
                                        <span>Sesi Privat 90 Menit (1 Guru 1 Murid)</span>
                                    </div>
                                    <div class="d-flex align-items-start gap-2 mb-2 small text-secondary">
                                        <i class="bi bi-check2-circle text-emerald-deep fs-6 mt-0.5 flex-shrink-0"></i>
                                        <span>Rapor Progres &amp; Evaluasi Tajwid Real-Time</span>
                                    </div>
                                    <div class="d-flex align-items-start gap-2 mb-2 small text-secondary">
                                        <i class="bi bi-check2-circle text-emerald-deep fs-6 mt-0.5 flex-shrink-0"></i>
                                        <span>Jadwal Fleksibel Bisa Disesuaikan</span>
                                    </div>
                                    <div class="d-flex align-items-start gap-2 mb-2 small text-success fw-medium">
                                        <i class="bi bi-check2-circle text-success fs-6 mt-0.5 flex-shrink-0"></i>
                                        <span>Sudah termasuk infaq jariyah dakwah Al-Qur'an</span>
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
                                        @if($parentEnrollment->isWaitingAdmin())
                                            <a href="{{ route('parent.enrollments.show', $parentEnrollment->id) }}"
                                               class="btn-editorial-primary w-100 text-center justify-content-center">
                                                <i class="bi bi-eye me-1"></i> Pantau Status Jadwal
                                            </a>
                                        @elseif($parentEnrollment->isWaitingParent() || $parentEnrollment->isConfirmed() || $parentEnrollment->isActive())
                                            <a href="{{ route('parent.enrollments.show', $parentEnrollment->id) }}"
                                               class="btn-editorial-primary w-100 text-center justify-content-center">
                                                <i class="bi bi-eye me-1"></i> Lihat Sesi Bimbingan
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
