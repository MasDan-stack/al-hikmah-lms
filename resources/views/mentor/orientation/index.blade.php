@extends('layouts.mentor')

@section('title', 'Pusat Orientasi & Pembekalan Guru Baru (Probation Hub) - AL-HIKMAH LMS')
@section('header', 'Pusat Orientasi & SOP Pembekalan')
@section('subheader', 'Panduan kurikulum, SOP pengajaran Al-Hikmah, dan pemantauan 4 modul orientasi wajib')

@section('content')
<div class="container-fluid py-2">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-4" role="alert">
            <div class="rounded-circle bg-success text-white p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="bi bi-check-lg fs-5"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-success">Berhasil!</h6>
                <div class="small">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-4" role="alert">
            <div class="rounded-circle bg-danger text-white p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="bi bi-exclamation-triangle fs-5"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-danger">Perhatian!</h6>
                <div class="small">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 🌟 HERO PROGRESS & STATUS CARD -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0d7a3e 0%, #064e26 100%); color: #ffffff;">
        <div class="card-body p-4 p-lg-5 position-relative">
            <div class="position-absolute end-0 bottom-0 opacity-10 d-none d-lg-block pe-4 pb-3" style="pointer-events: none;">
                <i class="bi bi-mortarboard" style="font-size: 13rem; line-height: 0.8;"></i>
            </div>

            <div class="row align-items-center g-4 position-relative z-1">
                <div class="col-lg-7">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-white bg-opacity-20 backdrop-blur mb-3">
                        <i class="bi bi-shield-check text-warning"></i>
                        <span class="small fw-semibold text-white">Probation Onboarding Hub &bull; 90 Hari</span>
                    </div>
                    <h2 class="fw-bold text-white mb-2">Pusat Pembekalan & Standar Mutu Guru</h2>
                    <p class="text-white-50 mb-4 pe-lg-4" style="font-size: 0.95rem; line-height: 1.6;">
                        Selamat datang di modul orientasi terpadu AL-HIKMAH. Pelajari dan selesaikan seluruh 4 modul wajib berikut sebagai prasyarat resmi evaluasi pengangkatan menjadi Guru Tetap bersertifikat <strong>M01 - Mentor Certified</strong>.
                    </p>

                    <div class="d-flex flex-wrap gap-4 text-white pt-2 border-top border-white border-opacity-20">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Progres Orientasi</div>
                            <div class="fs-4 fw-bold text-warning">{{ $completedCount }}/4 Modul <span class="fs-6 fw-normal text-white-50">({{ $progressPercent }}%)</span></div>
                        </div>
                        @if($probationTracking)
                            <div>
                                <div class="small text-white-50 text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Masa Evaluasi</div>
                                <div class="fs-4 fw-bold text-white">{{ $daysLeft }} Hari <span class="fs-6 fw-normal text-white-50">Tersisa</span></div>
                            </div>
                            <div>
                                <div class="small text-white-50 text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Presensi Aktual</div>
                                <div class="fs-4 fw-bold {{ ($probationTracking->attendance_rate ?? 100) >= 90 ? 'text-white' : 'text-danger' }}">
                                    {{ number_format($probationTracking->attendance_rate ?? 100, 1) }}%
                                </div>
                            </div>
                            <div>
                                <div class="small text-white-50 text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Rating Wali</div>
                                <div class="fs-4 fw-bold text-warning">
                                    ⭐ {{ number_format($probationTracking->average_rating ?? 5.0, 2) }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="bg-white rounded-4 p-4 text-dark shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-award-fill text-warning me-2"></i>Target Pengangkatan M01</h6>
                            <span class="badge {{ $completedCount === 4 ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill px-3 py-1">
                                {{ $completedCount === 4 ? 'Siap Evaluasi' : 'Dalam Proses' }}
                            </span>
                        </div>
                        <p class="small text-muted mb-3">
                            Guru yang berhasil menuntaskan 4 modul orientasi, presensi mengajar &ge; 90%, dan rating kepuasan wali &ge; 4.50 akan dianugerahi gelar pendidik tetap.
                        </p>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between small fw-bold mb-1">
                                <span>Kelengkapan 4 Modul:</span>
                                <span>{{ $progressPercent }}%</span>
                            </div>
                            <div class="progress rounded-pill" style="height: 10px;">
                                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width: {{ $progressPercent }}%;"></div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('mentor.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill w-50 fw-semibold">
                                <i class="bi bi-arrow-left me-1"></i> Dashboard
                            </a>
                            <button onclick="window.print()" class="btn btn-outline-success btn-sm rounded-pill w-50 fw-semibold">
                                <i class="bi bi-printer me-1"></i> Cetak SOP
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 📚 4 INTERACTIVE MODULES -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark mb-0">
            <i class="bi bi-journal-bookmark-fill text-success me-2"></i>Silabus 4 Modul Orientasi Wajib
        </h5>
        <span class="small text-muted">Pelajari seksama setiap bab dan tekan tombol konfirmasi selesai</span>
    </div>

    <div class="accordion d-flex flex-column gap-3 mb-5" id="orientationAccordion">
        @foreach($modules as $key => $mod)
            <div class="accordion-item border-0 rounded-4 shadow-sm overflow-hidden bg-white">
                <h2 class="accordion-header" id="heading-{{ $key }}">
                    <button class="accordion-button p-3.5 {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $key }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse-{{ $key }}">
                        <div class="d-flex align-items-center justify-content-between w-100 me-3 flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle p-2.5 d-flex align-items-center justify-content-center shadow-xs {{ $mod['is_completed'] ? 'bg-success text-white' : 'bg-light text-' . $mod['badge_color'] }}" style="width: 44px; height: 44px;">
                                    <i class="bi {{ $mod['is_completed'] ? 'bi-check2-circle fs-5' : $mod['icon'] . ' fs-5' }}"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-{{ $mod['badge_color'] }}-subtle text-{{ $mod['badge_color'] }} rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem; font-weight: 700;">
                                            {{ $mod['code'] }}
                                        </span>
                                        <h6 class="fw-bold text-dark mb-0">{{ $mod['title'] }}</h6>
                                    </div>
                                    <small class="text-secondary d-none d-sm-block mt-0.5">{{ $mod['summary'] }}</small>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1 small d-none d-md-inline-flex align-items-center gap-1">
                                    <i class="bi bi-clock"></i> {{ $mod['estimated_time'] }}
                                </span>
                                @if($mod['is_completed'])
                                    <span class="badge bg-success rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-patch-check-fill"></i> Tuntas
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-hourglass-split"></i> Wajib
                                    </span>
                                @endif
                            </div>
                        </div>
                    </button>
                </h2>

                <div id="collapse-{{ $key }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading-{{ $key }}" data-bs-parent="#orientationAccordion">
                    <div class="accordion-body p-4 pt-2 border-top">
                        <!-- Ringkasan Singkat -->
                        <div class="p-3 bg-light rounded-3 mb-4 border-start border-4 border-{{ $mod['badge_color'] }}">
                            <div class="small fw-bold text-muted text-uppercase mb-1">Tujuan Pembelajaran:</div>
                            <p class="mb-0 small text-dark">{{ $mod['summary'] }}</p>
                        </div>

                        <!-- Bab Materi Terstruktur -->
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-journal-text me-2 text-primary"></i>Uraian Panduan & Materi SOP</h6>
                        <div class="row g-3 mb-4">
                            @foreach($mod['topics'] as $topic)
                                <div class="col-md-6">
                                    <div class="card h-100 border rounded-3 p-3 bg-white shadow-none">
                                        <h6 class="fw-bold text-dark mb-2" style="font-size: 0.92rem;">{{ $topic['heading'] }}</h6>
                                        <p class="small text-muted mb-0" style="line-height: 1.6;">{{ $topic['content'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Rubrik Tambahan Khusus Modul 2 (Tajwid) -->
                        @if($key === 'mod2')
                            <div class="card border-0 rounded-3 bg-success-subtle p-3 mb-4">
                                <h6 class="fw-bold text-success mb-2"><i class="bi bi-table me-2"></i>Tabel Standar Bobot Penilaian Tajwid Terpadu</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered bg-white rounded-3 mb-0">
                                        <thead class="table-light">
                                            <tr class="small fw-bold">
                                                <th>Pilar Penilaian</th>
                                                <th>Bobot</th>
                                                <th>Aspek Pengujian Utama</th>
                                                <th>Standar Kelulusan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="small">
                                            <tr>
                                                <td class="fw-semibold">1. Makharijul Huruf</td>
                                                <td><span class="badge bg-primary">30%</span></td>
                                                <td>Letak artikulasi Halq, Lisan, Syafatain, Khaisyum</td>
                                                <td>Tidak tertukar (contoh: 'ain vs hamzah, tsa vs sin)</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">2. Sifatul Huruf</td>
                                                <td><span class="badge bg-success">25%</span></td>
                                                <td>Hams, Jahr, Isti'la, Qalqalah, Tafkhim/Tarqiq</td>
                                                <td>Pemberian hak dan mustahaq huruf saat sukun & harakat</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">3. Ahkamul Madd</td>
                                                <td><span class="badge bg-info text-dark">25%</span></td>
                                                <td>Mad Ashli (2 harakat), Mad Far'i (4, 5, 6 harakat)</td>
                                                <td>Ketukan durasi harakat stabil dan tidak berlebih</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">4. Nun & Mim Sukun</td>
                                                <td><span class="badge bg-warning text-dark">20%</span></td>
                                                <td>Izhar, Idgham, Iqlab, Ikhfa, Gunnah Musyaddadah</td>
                                                <td>Kadar dengung (ghunnah) 2 harakat sempurna</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- Checklist Poin Kunci -->
                        <div class="card border-0 rounded-3 bg-light p-3.5 mb-4">
                            <h6 class="fw-bold text-dark mb-2.5">
                                <i class="bi bi-check2-all text-success me-2"></i>Poin Kunci Pemahaman Mandiri (Self-Checklist)
                            </h6>
                            <div class="row g-2">
                                @foreach($mod['key_takeaways'] as $idx => $takeaway)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start gap-2 small text-secondary">
                                            <i class="bi bi-check-circle text-success mt-0.5"></i>
                                            <span>{{ $takeaway }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Footer Aksi Konfirmasi Modul -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 border-top">
                            <span class="small text-muted">
                                <i class="bi bi-info-circle me-1"></i>Dengan menekan tombol konfirmasi, Anda menyatakan telah membaca, memahami, dan siap mengamalkan SOP di atas.
                            </span>

                            @if($mod['is_completed'])
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-semibold">
                                        <i class="bi bi-check-all me-1"></i> Modul Telah Tuntas Terverifikasi
                                    </span>
                                </div>
                            @else
                                <form action="{{ route('mentor.orientation.complete', $mod['key']) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-xs">
                                        <i class="bi bi-check2-circle me-1"></i> Tandai Selesai Mempelajari {{ $mod['code'] }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- 💡 QUICK RESOURCE & FAQ CARDS -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-primary-subtle text-primary p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-calendar-check fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Jadwal Sesi Mengajar</h6>
                        <small class="text-muted">Siapkan KBM Anda</small>
                    </div>
                </div>
                <p class="small text-secondary mb-3">Periksa agenda bimbingan santri Anda, pantau riwayat konfirmasi kehadiran, dan pastikan hadir 5 menit sebelum waktu KBM.</p>
                <a href="{{ route('mentor.sessions.index') }}" class="btn btn-outline-primary btn-sm rounded-pill fw-semibold mt-auto">
                    Buka Jadwal Mengajar &rarr;
                </a>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-success-subtle text-success p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-pencil-square fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Jurnal Mutaba'ah Harian</h6>
                        <small class="text-muted">Pencatatan Progres Tilawah</small>
                    </div>
                </div>
                <p class="small text-secondary mb-3">Catat perkembangan tilawah dan hafalan santri secara tertib dan transparan segera setelah setiap sesi bimbingan selesai.</p>
                <a href="{{ route('mentor.progress.create') }}" class="btn btn-outline-success btn-sm rounded-pill fw-semibold mt-auto">
                    Catat Progres Harian &rarr;
                </a>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-warning-subtle text-warning p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-chat-dots fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Konsultasi Pembimbing</h6>
                        <small class="text-muted">Bantuan Koordinator</small>
                    </div>
                </div>
                <p class="small text-secondary mb-3">Jika Anda mengalami kendala teknis LMS atau membutuhkan arahan pedagogis khusus, hubungi Koordinator Pengajar Al-Hikmah.</p>
                <a href="{{ route('mentor.messages.index') }}" class="btn btn-outline-warning text-dark btn-sm rounded-pill fw-semibold mt-auto">
                    Buka Ruang Pesan &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
