@extends('layouts.admin')

@section('title', 'SPK Guru Teladan (AHP) & Alokasi Bonus')

@section('content')
<div class="container-fluid py-2">
    <!-- Header Page & Controls -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-primary-emphasis d-flex align-items-center gap-2">
                <i class="bi bi-trophy-fill text-warning"></i> SPK Guru Teladan (AHP) & Alokasi Bonus
            </h4>
            <p class="text-muted small mb-0">
                Sistem Pendukung Keputusan multi-kriteria berbasis metode ilmiah <em>Analytical Hierarchy Process (AHP)</em> terintegrasi data aktual LMS.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <form method="GET" action="{{ route('admin.mentors.ahp-ranking.index') }}" class="d-flex align-items-center gap-2">
                <input type="month" name="month" value="{{ $selectedMonth }}" class="form-control form-control-sm rounded-pill px-3 shadow-sm" onchange="this.form.submit()">
            </form>
            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#ahpMatrixModal">
                <i class="bi bi-sliders"></i> Kalibrasi & Simulasi Bobot
            </button>
            <form method="POST" action="{{ route('admin.mentors.ahp-ranking.announce', ['month' => $selectedMonth]) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin mengumumkan ranking dan mengirim notifikasi WhatsApp resmi ke seluruh mentor?');">
                @csrf
                <button type="submit" class="btn btn-outline-success btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                    <i class="bi bi-whatsapp"></i> Umumkan via WhatsApp
                </button>
            </form>
            <a href="{{ route('admin.mentors.ahp-ranking.print-sk', ['month' => $selectedMonth]) }}" target="_blank" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                <i class="bi bi-printer-fill"></i> Cetak SK & Bonus (A4)
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 4 KPI Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-card-custom position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Ustadz/Ustazah Teladan #1</span>
                    <span class="p-2 rounded-3 bg-warning-subtle text-warning">
                        <i class="bi bi-award-fill fs-5"></i>
                    </span>
                </div>
                <h5 class="fw-bold mb-1 text-truncate text-warning-emphasis">
                    {{ $evaluation['top_winner']['mentor_name'] ?? 'Belum Ada' }}
                </h5>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="badge bg-warning text-dark rounded-pill font-monospace fw-bold">
                        Skor: {{ $evaluation['top_winner']['final_ahp_score'] ?? 0 }}/100
                    </span>
                    <span class="text-muted small">Periode {{ $evaluation['period_label'] }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-card-custom">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Konsistensi Matriks (CR)</span>
                    <span class="p-2 rounded-3 {{ $evaluation['ahp_weights']['is_consistent'] ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                        <i class="bi {{ $evaluation['ahp_weights']['is_consistent'] ? 'bi-check-circle-fill' : 'bi-exclamation-octagon-fill' }} fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold mb-1 {{ $evaluation['ahp_weights']['is_consistent'] ? 'text-success' : 'text-danger' }}">
                    {{ $evaluation['ahp_weights']['cr_percent'] }}%
                </h3>
                <span class="badge {{ $evaluation['ahp_weights']['is_consistent'] ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} rounded-pill align-self-start font-monospace">
                    {{ $evaluation['ahp_weights']['is_consistent'] ? 'Konsisten (CR <= 10%)' : 'Inkonsisten (CR > 10%)' }}
                </span>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-card-custom">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Total Guru Dievaluasi</span>
                    <span class="p-2 rounded-3 bg-primary-subtle text-primary">
                        <i class="bi bi-people-fill fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold mb-1">{{ $evaluation['total_mentors'] }} <small class="text-muted fs-6 fw-normal">Pengajar</small></h3>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill align-self-start">
                    100% Data Riil LMS
                </span>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-card-custom">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Total Alokasi Reward</span>
                    <span class="p-2 rounded-3 bg-success-subtle text-success">
                        <i class="bi bi-cash-coin fs-5"></i>
                    </span>
                </div>
                <h3 class="fw-bold mb-1 text-success">Rp {{ number_format($evaluation['total_reward_allocated'], 0, ',', '.') }}</h3>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill align-self-start">
                    Top 5 Guru Berprestasi
                </span>
            </div>
        </div>
    </div>

    <!-- Podium Top 3 Visual Cards -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-4 bg-card-custom">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-trophy-fill text-warning"></i> Podium Ustadz/Ustazah Teladan Periode {{ $evaluation['period_label'] }}
                </h5>
                <p class="text-muted small mb-0">Tiga pengajar terbaik dengan perolehan skor komposit AHP tertinggi.</p>
            </div>
        </div>

        <div class="row g-4 justify-content-center align-items-end">
            <!-- Juara 2 (Perak) -->
            @php $p2 = $evaluation['podium']->get(1); @endphp
            <div class="col-12 col-md-4 order-2 order-md-1">
                @if($p2)
                <div class="card border border-secondary-subtle rounded-4 text-center p-3 h-100 shadow-sm" style="background: linear-gradient(180deg, rgba(226, 232, 240, 0.25) 0%, rgba(255, 255, 255, 0) 100%);">
                    <div class="position-relative d-inline-block mx-auto mb-2">
                        <img src="{{ $p2['mentor']?->user?->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($p2['mentor_name']).'&background=94a3b8&color=fff' }}" 
                             alt="{{ $p2['mentor_name'] }}" class="rounded-circle shadow-sm border border-3 border-secondary" style="width: 76px; height: 76px; object-fit: cover;">
                        <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-secondary text-white border border-2 border-white">
                            #2
                        </span>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1 mb-2 fw-semibold">
                        🥈 Juara 2 - Teladan Madya
                    </span>
                    <h6 class="fw-bold mb-1 text-truncate">{{ $p2['mentor_name'] }}</h6>
                    <div class="fs-4 fw-bold text-secondary font-monospace">{{ $p2['final_ahp_score'] }} <small class="fs-6 text-muted">/100</small></div>
                    <div class="text-success fw-bold small mt-1">🎁 Bonus: Rp {{ number_format($p2['reward_amount'], 0, ',', '.') }}</div>
                </div>
                @else
                <div class="text-center text-muted p-4 border rounded-4">Belum ada data</div>
                @endif
            </div>

            <!-- Juara 1 (Emas) -->
            @php $p1 = $evaluation['podium']->get(0); @endphp
            <div class="col-12 col-md-4 order-1 order-md-2">
                @if($p1)
                <div class="card border border-warning rounded-4 text-center p-4 h-100 shadow position-relative" style="background: linear-gradient(180deg, rgba(254, 243, 199, 0.45) 0%, rgba(255, 255, 255, 0) 100%); transform: translateY(-10px);">
                    <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-warning text-dark px-3 py-1 shadow-sm fw-bold">
                        👑 JUARA 1 UTAMA
                    </span>
                    <div class="position-relative d-inline-block mx-auto mb-2 mt-2">
                        <img src="{{ $p1['mentor']?->user?->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($p1['mentor_name']).'&background=f59e0b&color=fff' }}" 
                             alt="{{ $p1['mentor_name'] }}" class="rounded-circle shadow border border-4 border-warning" style="width: 96px; height: 96px; object-fit: cover;">
                        <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-warning text-dark border border-2 border-white fs-6">
                            🥇
                        </span>
                    </div>
                    <h5 class="fw-bold mb-1 text-truncate text-warning-emphasis">{{ $p1['mentor_name'] }}</h5>
                    <p class="text-muted small mb-2">{{ $p1['mentor']?->specialization ?? 'Guru Al-Qur\'an' }}</p>
                    <div class="fs-2 fw-bold text-warning-emphasis font-monospace">{{ $p1['final_ahp_score'] }} <small class="fs-6 text-muted">/100</small></div>
                    <div class="badge bg-success-subtle text-success fs-6 fw-bold px-3 py-1 rounded-pill mt-2">
                        🎁 Bonus Reward: Rp {{ number_format($p1['reward_amount'], 0, ',', '.') }}
                    </div>
                </div>
                @else
                <div class="text-center text-muted p-4 border rounded-4">Belum ada data</div>
                @endif
            </div>

            <!-- Juara 3 (Perunggu) -->
            @php $p3 = $evaluation['podium']->get(2); @endphp
            <div class="col-12 col-md-4 order-3 order-md-3">
                @if($p3)
                <div class="card border border-warning-subtle rounded-4 text-center p-3 h-100 shadow-sm" style="background: linear-gradient(180deg, rgba(254, 215, 170, 0.25) 0%, rgba(255, 255, 255, 0) 100%);">
                    <div class="position-relative d-inline-block mx-auto mb-2">
                        <img src="{{ $p3['mentor']?->user?->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($p3['mentor_name']).'&background=d97706&color=fff' }}" 
                             alt="{{ $p3['mentor_name'] }}" class="rounded-circle shadow-sm border border-3 border-danger-subtle" style="width: 76px; height: 76px; object-fit: cover;">
                        <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-danger-subtle text-danger-emphasis border border-2 border-white">
                            #3
                        </span>
                    </div>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1 mb-2 fw-semibold">
                        🥉 Juara 3 - Teladan Muda
                    </span>
                    <h6 class="fw-bold mb-1 text-truncate">{{ $p3['mentor_name'] }}</h6>
                    <div class="fs-4 fw-bold text-warning-emphasis font-monospace">{{ $p3['final_ahp_score'] }} <small class="fs-6 text-muted">/100</small></div>
                    <div class="text-success fw-bold small mt-1">🎁 Bonus: Rp {{ number_format($p3['reward_amount'], 0, ',', '.') }}</div>
                </div>
                @else
                <div class="text-center text-muted p-4 border rounded-4">Belum ada data</div>
                @endif
            </div>
        </div>
    </div>

    <!-- 2 Column Section: Radar Chart & Rincian Bobot Kriteria -->
    <div class="row g-4 mb-4">
        <!-- Radar (Spider) Chart -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-card-custom">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-bold mb-0 text-primary-emphasis d-flex align-items-center gap-2">
                            <i class="bi bi-diagram-3-fill text-primary"></i> Grafik Radar 5 Dimensi Kompetensi Guru
                        </h6>
                        <span class="text-muted small">Perbandingan multi-kriteria Top 3 Ustadz/Ustazah Teladan.</span>
                    </div>
                </div>
                <div id="radarChartAhp" style="min-height: 320px;"></div>
            </div>
        </div>

        <!-- Rincian Bobot Kriteria AHP -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-card-custom">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-bold mb-0 text-primary-emphasis d-flex align-items-center gap-2">
                            <i class="bi bi-pie-chart-fill text-info"></i> Bobot Prioritas Kriteria (Eigenvector)
                        </h6>
                        <span class="text-muted small">Hasil kalkulasi normalisasi matriks Saaty.</span>
                    </div>
                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#ahpMatrixModal">
                        Ubah <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                <div class="vstack gap-3">
                    @foreach($criteriaDefs as $key => $meta)
                    @php $w = $evaluation['ahp_weights']['weights'][$key] ?? $meta['default_weight']; @endphp
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-semibold text-truncate">
                                <span class="badge bg-light text-dark border me-1">{{ $meta['code'] }}</span> {{ $meta['name'] }}
                            </span>
                            <span class="small font-monospace fw-bold text-primary">{{ round($w * 100, 1) }}%</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 6px;">
                            <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: {{ round($w * 100, 1) }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 p-3 bg-light rounded-3 border">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Eigenvalue Maksimum (&lambda;<sub>maks</sub>):</span>
                        <span class="fw-bold font-monospace">{{ $evaluation['ahp_weights']['lambda_max'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Indeks Konsistensi (CI):</span>
                        <span class="fw-bold font-monospace">{{ $evaluation['ahp_weights']['ci'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>Rasio Konsistensi (CR):</span>
                        <span class="fw-bold font-monospace text-success">{{ $evaluation['ahp_weights']['cr_percent'] }}% (&le; 10%)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaderboard Table Lengkap -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-card-custom mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-list-ol text-primary"></i> Tabel Peringkat Lengkap Guru Teladan AHP
                </h5>
                <p class="text-muted small mb-0">Daftar seluruh ustadz/ustazah aktif beserta rincian 5 nilai kriteria dan alokasi bonus.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="ahpLeaderboardTable">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">Rank</th>
                        <th>Ustadz / Ustazah</th>
                        <th class="text-center">C1: Disiplin</th>
                        <th class="text-center">C2: Pedagogi</th>
                        <th class="text-center">C3: Akhlak</th>
                        <th class="text-center">C4: Kepuasan</th>
                        <th class="text-center">C5: Keaktifan</th>
                        <th class="text-center">Skor AHP</th>
                        <th class="text-end">Bonus Reward</th>
                        <th>Catatan Khusus Admin</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($evaluation['leaderboard'] as $item)
                    <tr>
                        <td class="text-center fw-bold">
                            @if($item['rank_position'] === 1)
                                <span class="badge rounded-pill bg-warning text-dark px-2 py-1">🥇 1</span>
                            @elseif($item['rank_position'] === 2)
                                <span class="badge rounded-pill bg-secondary text-white px-2 py-1">🥈 2</span>
                            @elseif($item['rank_position'] === 3)
                                <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning px-2 py-1">🥉 3</span>
                            @else
                                <span class="text-muted font-monospace">{{ $item['rank_position'] }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $item['mentor']?->user?->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($item['mentor_name']).'&background=0d7a3e&color=fff' }}" 
                                     alt="{{ $item['mentor_name'] }}" class="rounded-circle" style="width: 38px; height: 38px; object-fit: cover;">
                                <div>
                                    <div class="fw-bold text-dark">{{ $item['mentor_name'] }}</div>
                                    <small class="text-muted font-monospace">{{ $item['mentor']?->specialization ?? 'Guru Al-Qur\'an' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center font-monospace">{{ $item['c1_discipline'] }}</td>
                        <td class="text-center font-monospace">{{ $item['c2_pedagogy'] }}</td>
                        <td class="text-center font-monospace">{{ $item['c3_morals'] }}</td>
                        <td class="text-center font-monospace">{{ $item['c4_satisfaction'] }}</td>
                        <td class="text-center font-monospace">{{ $item['c5_involvement'] }}</td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-6 font-monospace fw-bold">
                                {{ $item['final_ahp_score'] }}
                            </span>
                        </td>
                        <td class="text-end fw-bold font-monospace {{ $item['reward_amount'] > 0 ? 'text-success' : 'text-muted' }}">
                            {{ $item['reward_amount'] > 0 ? 'Rp ' . number_format($item['reward_amount'], 0, ',', '.') : '-' }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="small text-muted text-truncate" style="max-width: 180px;" id="notesText-{{ $item['snapshot_id'] }}">
                                    {{ $item['admin_notes'] ?? '-' }}
                                </span>
                                @if($item['snapshot_id'])
                                <button type="button" class="btn btn-outline-secondary btn-sm p-0 px-2 rounded-pill" onclick="openNotesModal('{{ $item['snapshot_id'] }}', '{{ addslashes($item['mentor_name']) }}', '{{ addslashes($item['admin_notes'] ?? '') }}')">
                                    <i class="bi bi-pencil-fill" style="font-size: 0.75rem;"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            @if(!empty($item['is_announced']))
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                    <i class="bi bi-check2-all"></i> Terkirim
                                </span>
                            @else
                                <span class="badge bg-light text-muted border rounded-pill px-2 py-1">
                                    Pending
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Poin A: Edit Catatan Khusus Mentor -->
<div class="modal fade" id="mentorNotesModal" tabindex="-1" aria-labelledby="mentorNotesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="mentorNotesModalLabel">
                    <i class="bi bi-chat-square-quote-fill text-primary me-2"></i> Catatan Khusus Pimpinan Lembaga
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="notesForm" onsubmit="submitMentorNotes(event)">
                <div class="modal-body py-3">
                    <p class="text-muted small mb-2">
                        Tambahkan catatan prestasi istimewa, kasus khusus, atau pertimbangan pimpinan untuk guru: <strong id="modalMentorName"></strong>.
                    </p>
                    <input type="hidden" id="modalSnapshotId">
                    <div class="mb-3">
                        <textarea class="form-control rounded-3" id="modalAdminNotes" rows="4" placeholder="Contoh: Berhasil mengkhatamkan 5 santri dalam 1 periode dengan predikat Mumtaz..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" id="btnSaveNotes">
                        <i class="bi bi-save-fill me-1"></i> Simpan Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Poin B: Kalibrasi & Simulasi Bobot Matriks AHP -->
<div class="modal fade" id="ahpMatrixModal" tabindex="-1" aria-labelledby="ahpMatrixModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="ahpMatrixModalLabel">
                        <i class="bi bi-sliders text-primary"></i> Kalibrasi & Simulasi Matriks Perbandingan Berpasangan AHP
                    </h5>
                    <p class="text-muted small mb-0">
                        Skala Fundamental Saaty 1–9: (1 = Sama Penting, 3 = Sedikit Lebih Penting, 5 = Jelas Lebih Penting, 7 = Sangat Jelas, 9 = Mutlak).
                    </p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body py-3">
                <div class="row g-4">
                    <!-- Sisi Kiri: Form Input Matriks Saaty 1-9 -->
                    <div class="col-12 col-lg-7">
                        <div class="card border rounded-4 p-3 bg-light mb-3">
                            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-grid-3x3-gap-fill text-primary"></i> Matriks Perbandingan Kriteria (5x5)
                            </h6>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm text-center align-middle bg-white mb-0" id="matrixTable">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Kriteria</th>
                                            <th>C1</th>
                                            <th>C2</th>
                                            <th>C3</th>
                                            <th>C4</th>
                                            <th>C5</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small font-monospace">
                                        @php $kList = array_keys($criteriaDefs); @endphp
                                        @foreach($kList as $rIdx => $rKey)
                                        <tr>
                                            <td class="fw-bold text-start bg-light">{{ $criteriaDefs[$rKey]['code'] }}</td>
                                            @foreach($kList as $cIdx => $cKey)
                                                @if($rIdx === $cIdx)
                                                    <td class="bg-light fw-bold text-muted">1.0</td>
                                                @elseif($rIdx < $cIdx)
                                                    <td>
                                                        <input type="number" step="0.01" min="0.11" max="9.0" 
                                                               class="form-control form-control-sm text-center p-1 matrix-input" 
                                                               id="matrix-{{ $rKey }}-{{ $cKey }}" 
                                                               data-row="{{ $rKey }}" data-col="{{ $cKey }}"
                                                               value="{{ round($matrix[$rKey][$cKey] ?? 1.0, 2) }}"
                                                               onchange="syncReciprocal('{{ $rKey }}', '{{ $cKey }}', this.value)">
                                                    </td>
                                                @else
                                                    <td class="text-muted bg-light" id="cell-{{ $rKey }}-{{ $cKey }}">
                                                        {{ round($matrix[$rKey][$cKey] ?? 1.0, 2) }}
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                * Catatan: Nilai di bawah diagonal utama dihitung otomatis sebagai kebalikan nilai simetrisnya (1/x).
                            </small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="resetToDefaultMatrix()">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset ke Preset Baku
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm rounded-pill px-3" onclick="runLiveSimulation()">
                                <i class="bi bi-play-circle-fill"></i> Jalankan Simulasi Live
                            </button>
                        </div>
                    </div>

                    <!-- Sisi Kanan: Panel Hasil Simulasi Live (Poin B) -->
                    <div class="col-12 col-lg-5">
                        <div class="card border rounded-4 p-3 bg-white h-100 shadow-sm">
                            <h6 class="fw-bold mb-2 d-flex align-items-center justify-content-between">
                                <span><i class="bi bi-cpu-fill text-warning"></i> Status Simulasi Live</span>
                                <span id="simCrBadge" class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-monospace">
                                    CR: {{ $evaluation['ahp_weights']['cr_percent'] }}% (Konsisten)
                                </span>
                            </h6>
                            <p class="text-muted small mb-3">
                                Memantau kelayakan matematis rasio konsistensi dan preview pergeseran peringkat sebelum disimpan permanen.
                            </p>

                            <div class="mb-3">
                                <span class="small fw-semibold text-muted d-block mb-1">Preview Pergeseran Top 5 Guru:</span>
                                <div id="simRankDiffContainer" class="vstack gap-2">
                                    <div class="text-muted small p-2 bg-light rounded text-center">
                                        Tekan tombol "Jalankan Simulasi Live" untuk melihat pergeseran posisi.
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning border-0 rounded-3 small mb-0 py-2 d-none" id="simInconsistentAlert">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> <strong>Peringatan Inkonsistensi!</strong> Nilai CR melebihi 10%. Matriks perbandingan tidak dapat disimpan sebelum diperbaiki.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" id="btnSaveMatrix" onclick="submitMatrixSave()">
                    <i class="bi bi-check2-circle me-1"></i> Simpan Matriks Permanen
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Inisialisasi ApexCharts Radar Chart
    const radarCategories = @json($radarCategories);
    const radarSeries = @json($radarSeries);

    if (radarSeries.length > 0) {
        const radarOptions = {
            series: radarSeries,
            chart: {
                height: 330,
                type: 'radar',
                toolbar: { show: false },
                dropShadow: { enabled: true, blur: 1, left: 1, top: 1 }
            },
            colors: ['#f59e0b', '#64748b', '#d97706'],
            stroke: { width: 2 },
            fill: { opacity: 0.25 },
            markers: { size: 4 },
            xaxis: {
                categories: radarCategories,
                labels: {
                    style: {
                        colors: ['#0d7a3e', '#0d7a3e', '#0d7a3e', '#0d7a3e', '#0d7a3e'],
                        fontSize: '11px',
                        fontWeight: 600
                    }
                }
            },
            yaxis: {
                min: 0,
                max: 100,
                tickAmount: 5,
                labels: { formatter: val => Math.round(val) }
            },
            legend: { position: 'bottom' }
        };

        new ApexCharts(document.querySelector("#radarChartAhp"), radarOptions).render();
    } else {
        document.querySelector("#radarChartAhp").innerHTML = '<div class="text-muted text-center py-5">Data mentor belum tersedia untuk radar chart.</div>';
    }
});

// Poin A: Buka Modal & Simpan Catatan Khusus Admin
function openNotesModal(snapshotId, mentorName, notes) {
    document.getElementById('modalSnapshotId').value = snapshotId;
    document.getElementById('modalMentorName').innerText = mentorName;
    document.getElementById('modalAdminNotes').value = notes;
    new bootstrap.Modal(document.getElementById('mentorNotesModal')).show();
}

function submitMentorNotes(e) {
    e.preventDefault();
    const snapshotId = document.getElementById('modalSnapshotId').value;
    const notes = document.getElementById('modalAdminNotes').value;
    const btn = document.getElementById('btnSaveNotes');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

    fetch(`{{ url('/admin/mentors/ahp-ranking/notes') }}/${snapshotId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ admin_notes: notes })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-save-fill me-1"></i> Simpan Catatan';
        if (data.success) {
            const label = document.getElementById(`notesText-${snapshotId}`);
            if (label) label.innerText = notes || '-';
            bootstrap.Modal.getInstance(document.getElementById('mentorNotesModal')).hide();
            alert('Catatan khusus mentor berhasil disimpan.');
        } else {
            alert('Gagal menyimpan catatan: ' + (data.message || 'Error'));
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-save-fill me-1"></i> Simpan Catatan';
        alert('Terjadi kesalahan jaringan.');
    });
}

// Poin B: Sinkronisasi Kebalikan Nilai Matriks Simetris (1/x)
function syncReciprocal(rowKey, colKey, val) {
    const num = parseFloat(val);
    const reciprocalCell = document.getElementById(`cell-${colKey}-${rowKey}`);
    if (reciprocalCell && num > 0) {
        reciprocalCell.innerText = (1.0 / num).toFixed(2);
    }
}

// Kumpulkan data matriks dari input form
function gatherMatrixData() {
    const keys = ['discipline', 'pedagogy', 'morals_communication', 'parent_satisfaction', 'institutional_involvement'];
    const matrix = {};

    keys.forEach(r => {
        matrix[r] = {};
        keys.forEach(c => {
            if (r === c) {
                matrix[r][c] = 1.0;
            } else {
                const input = document.getElementById(`matrix-${r}-${c}`);
                if (input) {
                    matrix[r][c] = parseFloat(input.value) || 1.0;
                } else {
                    const reciprocalInput = document.getElementById(`matrix-${c}-${r}`);
                    const reciprocalVal = reciprocalInput ? parseFloat(reciprocalInput.value) : 1.0;
                    matrix[r][c] = reciprocalVal > 0 ? (1.0 / reciprocalVal) : 1.0;
                }
            }
        });
    });

    return matrix;
}

// Jalankan Simulasi Live (Poin B)
function runLiveSimulation() {
    const matrix = gatherMatrixData();
    const month = '{{ $selectedMonth }}';
    const container = document.getElementById('simRankDiffContainer');
    const badge = document.getElementById('simCrBadge');
    const alertBox = document.getElementById('simInconsistentAlert');
    const btnSave = document.getElementById('btnSaveMatrix');

    container.innerHTML = '<div class="text-center py-2"><span class="spinner-border spinner-border-sm text-primary"></span> Menghitung simulasi...</div>';

    fetch('{{ route("admin.mentors.ahp-ranking.simulate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ matrix: matrix, month: month })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            badge.innerText = `CR: ${data.cr_percent}% (${data.is_consistent ? 'Konsisten' : 'Inkonsisten'})`;
            badge.className = `badge ${data.is_consistent ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle'} rounded-pill font-monospace`;

            if (data.is_consistent) {
                alertBox.classList.add('d-none');
                btnSave.disabled = false;
            } else {
                alertBox.classList.remove('d-none');
                btnSave.disabled = true;
            }

            let html = '';
            data.rank_diffs.forEach(diff => {
                let badgeDiff = '<span class="badge bg-light text-muted border">Tetap</span>';
                if (diff.rank_diff > 0) {
                    badgeDiff = `<span class="badge bg-success-subtle text-success"><i class="bi bi-arrow-up"></i> Naik ${diff.rank_diff}</span>`;
                } else if (diff.rank_diff < 0) {
                    badgeDiff = `<span class="badge bg-danger-subtle text-danger"><i class="bi bi-arrow-down"></i> Turun ${Math.abs(diff.rank_diff)}</span>`;
                }

                html += `
                    <div class="d-flex justify-content-between align-items-center p-2 border rounded bg-light small">
                        <div>
                            <strong>#${diff.new_rank}</strong> ${diff.mentor_name}
                            <div class="text-muted" style="font-size: 0.75rem;">Skor Baru: ${diff.new_score} (Lama: ${diff.old_score})</div>
                        </div>
                        <div>${badgeDiff}</div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }
    })
    .catch(err => {
        container.innerHTML = '<div class="text-danger small text-center">Gagal memuat simulasi.</div>';
    });
}

// Simpan Matriks Permanen
function submitMatrixSave() {
    const matrix = gatherMatrixData();
    const btn = document.getElementById('btnSaveMatrix');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

    fetch('{{ route("admin.mentors.ahp-ranking.update-matrix") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ matrix: matrix })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Simpan Matriks Permanen';
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan saat menyimpan matriks.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Simpan Matriks Permanen';
        alert('Gagal menghubungi server.');
    });
}

function resetToDefaultMatrix() {
    const defaultVals = {
        'discipline-pedagogy': 0.5,
        'discipline-morals_communication': 1.0,
        'discipline-parent_satisfaction': 1.0,
        'discipline-institutional_involvement': 3.0,
        'pedagogy-morals_communication': 2.0,
        'pedagogy-parent_satisfaction': 2.0,
        'pedagogy-institutional_involvement': 5.0,
        'morals_communication-parent_satisfaction': 1.0,
        'morals_communication-institutional_involvement': 3.0,
        'parent_satisfaction-institutional_involvement': 3.0,
    };

    Object.keys(defaultVals).forEach(pair => {
        const input = document.getElementById(`matrix-${pair}`);
        if (input) {
            input.value = defaultVals[pair];
            const parts = pair.split('-');
            syncReciprocal(parts[0], parts[1], defaultVals[pair]);
        }
    });

    runLiveSimulation();
}
</script>
@endpush
@endsection
