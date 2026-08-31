@extends('layouts.mentor')

@section('title', 'Dashboard Mentor')
@section('header', $isRecruitmentMode ? 'Portal Seleksi Calon Guru' : 'Dashboard Utama')
@section('subheader', $isRecruitmentMode ? 'Pantau tahapan seleksi, kerjakan ujian kompetensi, dan status penerimaan Anda' : 'Ringkasan jadwal mengajar, santri binaan, dan grafik perkembangan')

@section('content')
<div class="container-fluid p-0">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm border-0 bg-success-subtle text-success" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show rounded-4 mb-4 shadow-sm border-0 bg-info-subtle text-info" role="alert">
            <i class="bi bi-info-circle-fill me-2 fs-5"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($isRecruitmentMode && $mentorApplication)
        <!-- ======================================================== -->
        <!-- 🌟 VIEW KHUSUS CALON GURU DALAM PROSES REKRUTMEN (SELEKSI) -->
        <!-- ======================================================== -->
        
        <!-- 1. Hero Banner Profil Calon Guru -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden text-white" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 py-2 mb-3 border border-white border-opacity-25">
                            <i class="bi bi-person-badge-fill me-1"></i> No. Registrasi: {{ $mentorApplication->application_code }}
                        </span>
                        <h2 class="fw-bold mb-2">Ahlan wa Sahlan, {{ $mentorApplication->full_name }}</h2>
                        <p class="text-white-50 mb-4 fs-6">
                            Terima kasih telah mendaftar sebagai Guru Pembimbing Al-Qur'an di AL-HIKMAH LMS. Pantau status dan kerjakan ujian seleksi langsung melalui portal ini.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                                <i class="bi bi-bookmark-star-fill text-warning me-1"></i> Spesialisasi: {{ $mentorApplication->specialization }}
                            </span>
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                                <i class="bi bi-book-fill text-primary me-1"></i> Hafalan: {{ $mentorApplication->hifz_total_juz }} Juz
                            </span>
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                                <i class="bi bi-clock-history text-info me-1"></i> Pengalaman: {{ $mentorApplication->experience_years }} Tahun
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 d-inline-block text-center w-100" style="max-width: 280px;">
                            <small class="text-white-50 text-uppercase d-block fw-bold" style="font-size: 0.75rem;">Status Saat Ini</small>
                            <div class="my-2">{!! $mentorApplication->status_badge !!}</div>
                            <small class="text-white-50 d-block">Tahap {{ $mentorApplication->current_stage }} dari 5</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Visual Stepper 5 Tahap Rekrutmen -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-signpost-split me-2 text-primary"></i>Linimasa Tahapan Seleksi Guru</h5>
            </div>
            <div class="card-body p-4">
                @php
                    $stages = [
                        1 => ['title' => 'Pendaftaran & Berkas', 'desc' => 'Formulir & CV dikirim', 'icon' => 'bi-file-earmark-check'],
                        2 => ['title' => 'Verifikasi Berkas', 'desc' => 'Peninjauan berkas & sanad', 'icon' => 'bi-folder-check'],
                        3 => ['title' => 'Tes Kompetensi', 'desc' => 'Ujian tajwid & pedagogi', 'icon' => 'bi-pencil-square'],
                        4 => ['title' => 'Wawancara & Microteaching', 'desc' => 'Simulasi mengajar', 'icon' => 'bi-camera-video'],
                        5 => ['title' => 'Penerimaan Guru', 'desc' => 'Masa percobaan 3 bulan', 'icon' => 'bi-award'],
                    ];
                    $curStage = $mentorApplication->current_stage ?? 1;
                @endphp

                <div class="row g-3 text-center">
                    @foreach($stages as $stepNum => $step)
                        @php
                            $isDone = $curStage > $stepNum || $mentorApplication->status === 'approved';
                            $isCurrent = $curStage === $stepNum && $mentorApplication->status !== 'approved';
                        @endphp
                        <div class="col-md col-sm-6">
                            <div class="p-3 rounded-4 border h-100 {{ $isCurrent ? 'border-primary bg-primary-subtle shadow-sm' : ($isDone ? 'border-success bg-success-subtle' : 'bg-light text-muted') }}">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2 {{ $isCurrent ? 'bg-primary text-white' : ($isDone ? 'bg-success text-white' : 'bg-secondary text-white') }}" style="width: 40px; height: 40px;">
                                    @if($isDone)
                                        <i class="bi bi-check-lg fs-5"></i>
                                    @else
                                        <i class="bi {{ $step['icon'] }}"></i>
                                    @endif
                                </div>
                                <div class="fw-bold text-dark small mb-1">{{ $step['title'] }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $step['desc'] }}</div>
                                <div class="mt-2">
                                    @if($isDone)
                                        <span class="badge bg-success rounded-pill px-2" style="font-size: 0.7rem;">Selesai</span>
                                    @elseif($isCurrent)
                                        <span class="badge bg-primary rounded-pill px-2" style="font-size: 0.7rem;">Tahap Aktif</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-2" style="font-size: 0.7rem;">Menunggu</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 3. KARTU UTAMA: SESI TES KOMPETENSI / INSTRUKSI TAHAP AKTIF -->
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                @if($activeTestSession)
                    <!-- Sesi Ujian Siap Dikerjakan -->
                    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden border-start border-5 border-primary">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <span class="badge bg-danger text-white rounded-pill px-3 py-2 animate__animated animate__pulse animate__infinite">
                                    <i class="bi bi-exclamation-circle me-1"></i> Ujian Siap Dikerjakan
                                </span>
                                <span class="text-muted small">
                                    <i class="bi bi-clock me-1"></i> Estimasi Durasi: {{ $activeTestSession->duration_minutes }} Menit
                                </span>
                            </div>

                            <h3 class="fw-bold text-dark mb-2">Ujian Tes Kompetensi Pedagogi & Tajwid</h3>
                            <p class="text-muted mb-4">
                                Tim Administrator telah menjadwalkan butir soal kompetensi untuk Anda. Silakan mulai mengerjakan tes di tempat yang tenang dengan koneksi internet yang stabil.
                            </p>

                            <div class="p-3 bg-light rounded-4 border mb-4">
                                <div class="row g-3 text-center">
                                    <div class="col-sm-4">
                                        <small class="text-muted d-block">Jumlah Soal</small>
                                        <span class="fw-bold text-dark fs-5">{{ count($activeTestSession->ai_question_payload['questions'] ?? []) }} Butir Soal</span>
                                    </div>
                                    <div class="col-sm-4 border-start">
                                        <small class="text-muted d-block">Bentuk Ujian</small>
                                        <span class="fw-bold text-dark fs-5">Pilihan Ganda</span>
                                    </div>
                                    <div class="col-sm-4 border-start">
                                        <small class="text-muted d-block">Sistem Penilaian</small>
                                        <span class="fw-bold text-success fs-5">Otomatis Terdata</span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('mentor.recruitment.take-test', $activeTestSession->id) }}" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                                    <i class="bi bi-pencil-square me-2"></i>Mulai Kerjakan Tes Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                @elseif($mentorApplication->status === 'test_completed')
                    <!-- Tes Selesai & Menunggu Evaluasi -->
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-md-5 border-start border-5 border-success">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-success text-white p-3 fs-3">
                                <i class="bi bi-check2-all"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-dark mb-1">Tes Kompetensi Selesai Dikerjakan</h4>
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Hasil Telah Terkirim</span>
                            </div>
                        </div>
                        <p class="text-muted mb-3">
                            Alhamdulillah, jawaban tes kompetensi Anda telah tersimpan di sistem dengan skor perolehan:
                            <strong class="text-success fs-5">{{ $mentorApplication->final_score ?? '-' }}/100</strong>.
                            Tim penguji AL-HIKMAH akan meninjau hasil Anda untuk penjadwalan tahap wawancara.
                        </p>
                        @if($mentorApplication->admin_notes)
                            <div class="alert alert-secondary border-0 rounded-3 mb-0 small">
                                <strong>Catatan Penguji:</strong> {{ $mentorApplication->admin_notes }}
                            </div>
                        @endif
                    </div>
                @elseif($mentorApplication->status === 'interview_scheduled')
                    <!-- Undangan Wawancara -->
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-md-5 border-start border-5 border-warning">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-warning text-dark p-3 fs-3">
                                <i class="bi bi-camera-video"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-dark mb-1">Undangan Wawancara & Microteaching</h4>
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-1">Tahap 4 Terjadwal</span>
                            </div>
                        </div>
                        <p class="text-muted mb-3">
                            Selamat! Anda diundang untuk mengikuti sesi Wawancara & Simulasi Mengajar (Microteaching).
                        </p>
                        <div class="alert alert-light border rounded-3 mb-0">
                            <div class="fw-bold text-dark mb-1"><i class="bi bi-info-circle text-primary me-2"></i>Instruksi & Jadwal:</div>
                            <div class="small text-secondary">{{ $mentorApplication->admin_notes ?? 'Tim panitia akan menghubungi Anda melalui WhatsApp untuk tautan Zoom / Google Meet.' }}</div>
                        </div>
                    </div>
                @else
                    <!-- Berkas Sedang Ditinjau -->
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-md-5 border-start border-5 border-info">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-info-subtle text-info p-3 fs-3">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-dark mb-1">Berkas Dalam Tahap Peninjauan</h4>
                                <span class="badge bg-info text-dark rounded-pill px-3 py-1">Tahap 2: Administrasi</span>
                            </div>
                        </div>
                        <p class="text-muted mb-0">
                            Berkas lamaran, portofolio, dan sertifikat/syahadah Anda sedang diverifikasi oleh tim panitia rekrutmen AL-HIKMAH. Sesi tes kompetensi akan otomatis muncul pada halaman ini setelah berkas Anda disetujui.
                        </p>
                    </div>
                @endif
            </div>

            <!-- 4. Sidebar Portofolio & Berkas Calon Guru -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-folder2-open me-2 text-primary"></i>Berkas Lampiran Anda</h6>
                    </div>
                    <div class="card-body p-4">
                        @forelse($mentorApplication->documents as $doc)
                            <div class="p-3 bg-light rounded-3 border mb-3 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi {{ $doc->document_type == 'cv' ? 'bi-file-earmark-pdf text-danger' : 'bi-file-earmark-image text-info' }} fs-3"></i>
                                    <div>
                                        <span class="badge bg-secondary-subtle text-dark text-uppercase fw-bold" style="font-size: 0.7rem;">{{ $doc->document_type }}</span>
                                        <div class="small fw-semibold text-dark text-truncate" style="max-width: 150px;">{{ $doc->file_name }}</div>
                                    </div>
                                </div>
                                <span class="badge bg-white text-muted border">{{ round($doc->file_size) }} KB</span>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">Tidak ada berkas terlampir.</p>
                        @endforelse

                        <div class="mt-4 pt-3 border-top">
                            <h6 class="fw-bold text-dark small mb-2"><i class="bi bi-question-circle text-info me-1"></i>Bantuan & Informasi</h6>
                            <p class="text-muted small mb-0">
                                Apabila Anda memiliki pertanyaan seputar seleksi, silakan hubungi tim administrasi AL-HIKMAH.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Informasi Fitur Pengajaran Yang Akan Terbuka -->
        <div class="card border-0 shadow-sm rounded-4 bg-light p-4 text-center mb-4">
            <div class="py-2">
                <i class="bi bi-lock-fill fs-2 text-muted mb-2 d-block"></i>
                <h6 class="fw-bold text-dark mb-1">Fitur Manajemen Santri & Bimbingan Belum Aktif</h6>
                <p class="text-muted small mb-0 mx-auto" style="max-width: 600px;">
                    Menu operasional seperti <strong>Jadwal Mengajar, Daftar Santri Binaan, Catat Progres Hafalan, dan Ketersediaan Jam</strong> akan otomatis aktif di dashboard ini setelah Anda dinyatakan resmi diterima sebagai Guru Pembimbing AL-HIKMAH.
                </p>
            </div>
        </div>

    @else
        <!-- ======================================================== -->
        <!-- 🌟 VIEW UTAMA GURU AKTIF & MASA PERCOBAAN (PROBATION) -->
        <!-- ======================================================== -->

        <!-- Cards Summary -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-3 bg-primary-subtle text-primary fs-3">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Sesi Hari Ini</div>
                            <h3 class="fw-bold mb-0 text-dark">{{ $todaySessionsCount }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-3 bg-success-subtle text-success fs-3">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Santri Binaan</div>
                            <h3 class="fw-bold mb-0 text-dark">{{ $activeStudentsCount }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-3 bg-warning-subtle text-warning fs-3">
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Rata-rata Tajwid</div>
                            <h3 class="fw-bold mb-0 text-dark">{{ $avgTajwid }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-3 bg-info-subtle text-info fs-3">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Sesi Mendatang</div>
                            <h3 class="fw-bold mb-0 text-dark">{{ $upcomingSessionsCount }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Action Buttons -->
        <div class="d-flex gap-2 mb-4 flex-wrap">
            <a href="{{ route('mentor.availability.index') }}" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm text-dark">
                <i class="bi bi-clock-history me-2"></i>Atur Ketersediaan Jam
            </a>
            <a href="{{ route('mentor.students.parents') }}" class="btn btn-info text-white rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-person-lines-fill me-2"></i>Data Orang Tua Wali
            </a>
            <a href="{{ route('mentor.progress.create') }}" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-pencil-square me-2"></i>Catat Progres Hafalan
            </a>
            <a href="{{ route('mentor.progress.bulk-create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-layers-fill me-2"></i>Catat Progres Massal
            </a>
            <a href="{{ route('mentor.reports.export') }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold">
                <i class="bi bi-file-earmark-pdf me-2"></i>Export Laporan
            </a>
            <a href="{{ route('mentor.students.index') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                <i class="bi bi-people me-2"></i>Lihat Semua Santri
            </a>
            <a href="{{ route('mentor.sessions.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                <i class="bi bi-calendar-check me-2"></i>Kalender Sesi
            </a>
        </div>

        <!-- ⚠️ Alert Santri Progres Terendah (< 75) -->
        @if($lowProgressStudents->isNotEmpty())
            <div class="card border-0 shadow-sm rounded-4 bg-warning-subtle mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-4"></i>
                        <h6 class="fw-bold text-dark mb-0">Santri Perlu Perhatian Khusus (Progres Terendah)</h6>
                    </div>
                    <p class="text-secondary small mb-3">Santri berikut membutuhkan bimbingan intensif karena rata-rata nilai tajwid berada di bawah 75:</p>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($lowProgressStudents as $lowSt)
                            <div class="bg-white rounded-pill px-3 py-1 shadow-sm d-flex align-items-center gap-2 border">
                                <span class="fw-bold text-dark small">{{ $lowSt->user?->name ?? $lowSt->full_name }}</span>
                                <span class="badge bg-danger rounded-pill">Tajwid: {{ $lowSt->avg_tajwid_score }}</span>
                                <a href="{{ route('mentor.progress.create', ['student_id' => $lowSt->id]) }}" class="btn btn-sm btn-link text-primary p-0 text-decoration-none">
                                    <i class="bi bi-plus-circle"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="row g-4 mb-4">
            <!-- 📊 Chart Grafik Perkembangan -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-graph-up-arrow me-2 text-primary"></i>Grafik Perkembangan Bimbingan
                        </h5>
                        <span class="badge bg-light text-dark rounded-pill px-3">6 Bulan Terakhir</span>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="mentorProgressChart" height="150"></canvas>
                    </div>
                </div>
            </div>

            <!-- 🕒 Log Aktivitas Mentor -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-activity me-2 text-info"></i>Aktivitas Terbaru Anda
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if($recentActivities->isEmpty())
                            <div class="text-center py-4 text-muted small">
                                Belum ada riwayat aktivitas yang tercatat.
                            </div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach($recentActivities as $act)
                                    <div class="list-group-item px-0 py-2 border-bottom">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-info-subtle text-info rounded-pill small">{{ ucfirst(str_replace('_', ' ', $act->action)) }}</span>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $act->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="small text-dark mt-1">{{ $act->description }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Jadwal Mengajar Hari Ini - With View Toggle (Tabel / Timeline Visual) -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock me-2 text-primary"></i>Jadwal Mengajar Hari Ini</h5>
                        
                        <div class="d-flex align-items-center gap-2">
                            <div class="btn-group btn-group-sm" role="group" aria-label="Toggle View">
                                <button type="button" class="btn btn-outline-primary active" id="btnViewTable">
                                    <i class="bi bi-table me-1"></i>Tabel
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="btnViewTimeline">
                                    <i class="bi bi-distribute-vertical me-1"></i>Timeline
                                </button>
                            </div>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3">{{ today()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        @if($todaySessions->isEmpty())
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                                Tidak ada jadwal sesi mengajar untuk hari ini.
                            </div>
                        @else
                            <!-- 1️⃣ TABEL VIEW -->
                            <div id="tableViewContainer" class="table-responsive">
                                <table class="table align-middle table-hover datatable" id="tableMentorTodaySessions" data-page-length="5">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Santri</th>
                                            <th>Mode</th>
                                            <th>Konfirmasi Wali</th>
                                            <th>Status Sesi</th>
                                            <th class="no-sort">Aksi Cepat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($todaySessions as $session)
                                            <tr>
                                                <td class="fw-bold text-primary">{{ $session->time }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $session->student?->user?->name ?? $session->student?->full_name }}</div>
                                                    <small class="text-muted">{{ $session->notes }}</small>
                                                </td>
                                                <td>
                                                    @if($session->method === 'offline')
                                                        <span class="badge bg-success-subtle text-success rounded-pill px-2 border border-success-subtle">
                                                            <i class="bi bi-house-door me-1"></i> Offline
                                                        </span>
                                                    @elseif($session->method === 'online')
                                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2 border border-primary-subtle">
                                                            <i class="bi bi-camera-video me-1"></i> Online
                                                        </span>
                                                    @else
                                                        <span class="badge bg-info-subtle text-info rounded-pill px-2 border border-info-subtle">
                                                            <i class="bi bi-arrow-repeat me-1"></i> Hybrid
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($session->confirmation)
                                                        @if($session->confirmation->status === 'hadir')
                                                            <span class="badge bg-success text-white rounded-pill px-2">
                                                                <i class="bi bi-check-circle me-1"></i> Hadir
                                                            </span>
                                                        @elseif($session->confirmation->status === 'izin')
                                                            <span class="badge bg-warning text-dark rounded-pill px-2" title="{{ $session->confirmation->notes }}">
                                                                <i class="bi bi-info-circle me-1"></i> Izin
                                                            </span>
                                                        @elseif($session->confirmation->status === 'sakit')
                                                            <span class="badge bg-danger text-white rounded-pill px-2" title="{{ $session->confirmation->notes }}">
                                                                <i class="bi bi-heart-pulse me-1"></i> Sakit
                                                            </span>
                                                        @endif
                                                        @if($session->confirmation->notes)
                                                            <small class="d-block text-muted fst-italic mt-1" style="max-width: 150px;">"{{ \Illuminate\Support\Str::limit($session->confirmation->notes, 25) }}"</small>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-light text-secondary rounded-pill border px-2">
                                                            <i class="bi bi-hourglass-split me-1"></i> Belum Konfirmasi
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($session->status === 'completed')
                                                        <span class="badge bg-success-subtle text-success rounded-pill">Selesai</span>
                                                    @elseif($session->status === 'in_progress')
                                                        <span class="badge bg-primary-subtle text-primary rounded-pill">Sedang Berlangsung</span>
                                                    @elseif($session->status === 'cancelled')
                                                        <span class="badge bg-danger-subtle text-danger rounded-pill">Batal</span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning rounded-pill">Terjadwal</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1 flex-wrap">
                                                        @if($session->status !== 'completed' && $session->status !== 'in_progress')
                                                            <form action="{{ route('mentor.sessions.update-status', $session->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="status" value="in_progress">
                                                                <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill" title="Mulai Sesi">
                                                                    <i class="bi bi-play-fill"></i> Mulai
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <a href="{{ route('mentor.progress.create', ['student_id' => $session->student_id, 'session_id' => $session->id]) }}" class="btn btn-sm btn-outline-success rounded-pill">
                                                            <i class="bi bi-check2-circle"></i> Catat Progres
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- 2️⃣ TIMELINE VIEW -->
                            <div id="timelineViewContainer" class="d-none">
                                <div class="position-relative ps-4 border-start border-2 border-primary my-2">
                                    @foreach($todaySessions as $session)
                                        <div class="mb-4 position-relative">
                                            <div class="position-absolute top-0 start-0 translate-middle rounded-circle bg-primary text-white p-1" style="left: -17px !important;">
                                                <i class="bi bi-clock-fill small"></i>
                                            </div>
                                            <div class="card border shadow-sm rounded-3 p-3 ms-2">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="fw-bold text-primary fs-6">{{ $session->time }}</span>
                                                    <div>
                                                        <span class="badge bg-info-subtle text-info rounded-pill me-1">{{ ucfirst($session->method) }}</span>
                                                        @if($session->status === 'completed')
                                                            <span class="badge bg-success-subtle text-success rounded-pill">Selesai</span>
                                                        @elseif($session->status === 'in_progress')
                                                            <span class="badge bg-primary-subtle text-primary rounded-pill">Sedang Berlangsung</span>
                                                        @else
                                                            <span class="badge bg-warning-subtle text-warning rounded-pill">Terjadwal</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-1">{{ $session->student?->user?->name ?? $session->student?->full_name }}</h6>
                                                <p class="text-muted small mb-3">{{ $session->notes ?? 'Tidak ada catatan sesi.' }}</p>
                                                
                                                <div class="d-flex gap-2">
                                                    @if($session->status !== 'completed' && $session->status !== 'in_progress')
                                                        <form action="{{ route('mentor.sessions.update-status', $session->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="status" value="in_progress">
                                                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">
                                                                <i class="bi bi-play-fill me-1"></i> Mulai Sesi
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('mentor.progress.create', ['student_id' => $session->student_id, 'session_id' => $session->id]) }}" class="btn btn-sm btn-success rounded-pill px-3">
                                                        <i class="bi bi-pencil-square me-1"></i> Selesai & Catat
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Santri Binaan & Progres Terakhir -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-journal-bookmark-fill me-2 text-success"></i>Progres Terakhir Santri</h5>
                    </div>
                    <div class="card-body p-4">
                        @if($recentProgress->isEmpty())
                            <div class="text-center py-4 text-muted">
                                Belum ada catatan progres terbaru.
                            </div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach($recentProgress as $prog)
                                    <div class="list-group-item px-0 py-3 border-bottom">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div class="fw-bold text-dark">{{ $prog->student?->user?->name ?? 'Santri' }}</div>
                                            <small class="text-muted">{{ $prog->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="small text-secondary mb-1">
                                            <i class="bi bi-book me-1"></i>{{ $prog->surah_start ?? 'Surah' }} (Juz {{ $prog->juz ?? 1 }})
                                        </div>
                                        <div class="d-flex gap-2">
                                            <span class="badge bg-success-subtle text-success small">Tajwid: {{ $prog->nilai_tajwid ?? '-' }}</span>
                                            <span class="badge bg-primary-subtle text-primary small">Adab: {{ $prog->nilai_adab ?? '-' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 👨👩👧 Widget Santri Binaan & Wali -->
                <div class="card border-0 shadow-sm rounded-4 mt-4 bg-white">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-people-fill me-2 text-info"></i>Santri & Kontak Wali</h5>
                        <a href="{{ route('mentor.students.parents') }}" class="btn btn-sm btn-link text-info text-decoration-none">Lihat Semua</a>
                    </div>
                    <div class="card-body p-4">
                        @if($students->isEmpty())
                            <div class="text-center py-3 text-muted small">Belum ada santri binaan aktif.</div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach($students->take(4) as $st)
                                    <div class="list-group-item px-0 py-2 border-bottom d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold text-dark small">{{ $st->getDisplayName() }}</div>
                                            <div class="text-muted text-truncate style-sub" style="font-size: 0.78rem;">
                                                <i class="bi bi-person me-1"></i>Wali: {{ $st->getParentNameAttribute() }}
                                            </div>
                                        </div>
                                        @if($st->getParentPhoneAttribute() !== '-')
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $st->getParentPhoneAttribute()) }}" target="_blank" class="btn btn-sm btn-success rounded-circle" title="WhatsApp Orang Tua">
                                                <i class="bi bi-whatsapp"></i>
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
@if(!$isRecruitmentMode)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle view Table / Timeline
        const btnTable = document.getElementById('btnViewTable');
        const btnTimeline = document.getElementById('btnViewTimeline');
        const tableContainer = document.getElementById('tableViewContainer');
        const timelineContainer = document.getElementById('timelineViewContainer');

        if (btnTable && btnTimeline && tableContainer && timelineContainer) {
            btnTable.addEventListener('click', function () {
                btnTable.classList.add('active');
                btnTimeline.classList.remove('active');
                tableContainer.classList.remove('d-none');
                timelineContainer.classList.add('d-none');
            });

            btnTimeline.addEventListener('click', function () {
                btnTimeline.classList.add('active');
                btnTable.classList.remove('active');
                timelineContainer.classList.remove('d-none');
                tableContainer.classList.add('d-none');
            });
        }

        // Chart.js Chart Implementation
        const ctx = document.getElementById('mentorProgressChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [
                        {
                            label: 'Jumlah Catatan Progres',
                            data: {!! json_encode($chartProgressCounts) !!},
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13, 110, 253, 0.1)',
                            fill: true,
                            tension: 0.3,
                            borderWidth: 2
                        },
                        {
                            label: 'Rata-rata Nilai Tajwid',
                            data: {!! json_encode($chartAvgTajwid) !!},
                            borderColor: '#198754',
                            backgroundColor: 'rgba(25, 135, 84, 0.1)',
                            fill: true,
                            tension: 0.3,
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    scales: {
                        y: { beginAtZero: true, max: 100 }
                    }
                }
            });
        }
    });
</script>
@endif
@endpush
