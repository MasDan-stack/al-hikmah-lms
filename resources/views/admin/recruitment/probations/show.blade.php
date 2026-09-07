@extends('layouts.admin')

@section('title', 'Detail & Monitoring Masa Percobaan Mentor')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">Monitoring Masa Percobaan: {{ $probation->mentor->getDisplayName() }}</h1>
            <small class="text-muted">
                <i class="bi bi-clock-history me-1"></i> Terakhir Disinkronkan: 
                <strong>{{ $probation->last_synced_at ? \Carbon\Carbon::parse($probation->last_synced_at)->locale('id')->isoFormat('D MMM Y, HH:mm') : 'Saat Halaman Dibuka' }}</strong>
            </small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.staff.show', $probation->mentor_id) }}" class="btn btn-outline-info btn-sm rounded-pill px-3 shadow-xs">
                <i class="bi bi-person-badge me-1"></i> Detail Akun Guru
            </a>
            <form action="{{ route('admin.mentors.probation.sync', $probation->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-xs">
                    <i class="bi bi-arrow-repeat me-1"></i> 🔄 Sinkronkan Data Aktual LMS
                </button>
            </form>
            <a href="{{ route('admin.mentors.probation.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3 shadow-xs">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Kiri: Checklist & Metrik Kinerja -->
        <div class="col-xl-8 col-lg-7">
            <!-- Checklist Onboarding -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold"><i class="bi bi-check2-square me-2"></i>Checklist Onboarding & Kinerja 90 Hari</h6>
                    <span class="badge bg-light text-primary">Durasi: {{ $probation->duration_months ?? 3 }} Bulan</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.mentors.probation.updateScores', $probation->id) }}" method="POST">
                        @csrf
                        <h6 class="font-weight-bold text-gray-800 mb-3"><i class="bi bi-list-check me-1 text-primary"></i>1. Checklist Adaptasi & Pelatihan</h6>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="orientation_completed" value="1" id="orientCheck" {{ $probation->orientation_completed ? 'checked' : '' }}>
                                    <label class="form-check-label" for="orientCheck">Orientasi Lembaga & Kurikulum Selesai</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="system_training_completed" value="1" id="sysCheck" {{ $probation->system_training_completed ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sysCheck">Pelatihan Sistem LMS & Mutaba'ah Selesai</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="first_session_conducted" value="1" id="firstSessCheck" {{ $probation->first_session_conducted ? 'checked' : '' }}>
                                    <label class="form-check-label" for="firstSessCheck">Sesi Mengajar Perdana Terlaksana</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small text-muted">Modul Pelatihan Diselesaikan</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="training_modules_completed" class="form-control" value="{{ $probation->training_modules_completed ?? 0 }}" min="0" max="10">
                                    <span class="input-group-text">/ {{ $probation->training_modules_required ?? 4 }} Modul</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="font-weight-bold text-gray-800 mb-3"><i class="bi bi-graph-up-arrow me-1 text-primary"></i>2. Metrik Kinerja Aktual</h6>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tingkat Presensi & Kehadiran Mengajar (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" min="0" max="100" name="attendance_rate" class="form-control" value="{{ $probation->attendance_rate ?? 100.0 }}" required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rating Rata-rata Wali Santri (Skala 1.00 - 5.00)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="1" max="5" name="average_rating" class="form-control" value="{{ $probation->average_rating ?? 5.00 }}" required>
                                    <span class="input-group-text"><i class="bi bi-star-fill text-warning"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan Evaluasi / Mid-Review Admin</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Tuliskan catatan progres atau evaluasi berkala...">{{ $probation->mid_review_notes }}</textarea>
                        </div>

                        @if($probation->status === 'active')
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Perubahan Checklist & Nilai</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Kanan: Profil Mentor & Keputusan Akhir -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-person-badge me-2"></i>Status Masa Percobaan</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr><th width="45%">Mulai</th><td>{{ Carbon\Carbon::parse($probation->start_date)->format('d M Y') }}</td></tr>
                        <tr><th>Berakhir</th><td>{{ Carbon\Carbon::parse($probation->end_date)->format('d M Y') }}</td></tr>
                        <tr>
                            <th>Sisa Waktu</th>
                            <td>
                                @php
                                    $daysLeft = (int) now()->diffInDays(Carbon\Carbon::parse($probation->end_date), false);
                                @endphp
                                @if($daysLeft > 0)
                                    <span class="badge bg-info text-dark">{{ $daysLeft }} Hari Lagi</span>
                                @else
                                    <span class="badge bg-secondary">Telah Berakhir</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($probation->status === 'active')
                                    <span class="badge bg-primary">Aktif Berjalan</span>
                                @elseif($probation->status === 'passed')
                                    <span class="badge bg-success">Lulus (Guru Tetap)</span>
                                @elseif($probation->status === 'extended')
                                    <span class="badge bg-warning text-dark">Diperpanjang</span>
                                @elseif($probation->status === 'terminated')
                                    <span class="badge bg-danger">Diberhentikan</span>
                                @endif
                            </td>
                        </tr>
                        @if($probation->final_decision)
                            <tr><th>Keputusan</th><td><strong class="text-uppercase text-primary">{{ $probation->final_decision }}</strong></td></tr>
                        @endif
                        @if($probation->final_evaluation_date)
                            <tr><th>Tgl Keputusan</th><td>{{ Carbon\Carbon::parse($probation->final_evaluation_date)->format('d M Y') }}</td></tr>
                        @endif
                        <tr><th colspan="2" class="bg-light text-center py-2">Statistik Kinerja</th></tr>
                        <tr><th>Total Sesi Mengajar</th><td>{{ $probation->total_sessions_conducted ?? 0 }} Sesi</td></tr>
                        <tr><th>Total Santri Aktif</th><td>{{ $probation->active_students_assigned ?? 0 }} Santri</td></tr>
                    </table>

                    @if($probation->final_notes)
                        <div class="alert alert-light border mt-2">
                            <small class="text-muted d-block">Catatan Keputusan Akhir:</small>
                            <p class="mb-0 small">{{ $probation->final_notes }}</p>
                        </div>
                    @endif

                    <hr>

                    @if($probation->status === 'active')
                        <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#decisionModal">
                            <i class="bi bi-award me-1"></i>Evaluasi & Putuskan Kelulusan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Keputusan Akhir -->
<div class="modal fade" id="decisionModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.mentors.probation.complete', $probation->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Putuskan Hasil Masa Percobaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Pilih Keputusan Akhir</label>
                        <select name="decision" id="decisionSelect" class="form-select" required>
                            <option value="passed">🟢 Lulus Menjadi Guru Tetap (Diberi Badge M01 - Mentor Certified)</option>
                            <option value="extended">🟡 Perpanjang Masa Percobaan (1 Bulan Tambahan)</option>
                            <option value="terminated">🔴 Diberhentikan / Tidak Diangkat</option>
                        </select>
                    </div>

                    <div id="handoverWizardSection" class="border rounded-3 p-3 bg-light border-warning mb-3" style="display: none;">
                        <div class="d-flex align-items-center mb-2 text-warning">
                            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                            <strong class="text-dark">Hand-over Wizard: Alihkan Santri Bimbingan</strong>
                        </div>
                        <p class="small text-muted mb-2">
                            Mentor ini memiliki santri bimbingan aktif (<strong>{{ $probation->active_students_assigned ?? 0 }} Santri</strong>). Pilih guru pengganti agar bimbingan santri otomatis dialihkan dan KBM tidak terputus.
                        </p>
                        <label class="form-label font-weight-bold small">Pilih Guru Pengganti (Substitute Mentor)</label>
                        <select name="substitute_mentor_id" id="substitute_mentor_id" class="form-select form-select-sm">
                            <option value="">-- Pilih Guru Pengganti (Opsional/Sesuai Alokasi) --</option>
                            @foreach($activeMentors ?? [] as $actMentor)
                                @if($actMentor->id !== $probation->mentor_id)
                                    <option value="{{ $actMentor->id }}">
                                        {{ $actMentor->getDisplayName() ?? $actMentor->user?->name }} ({{ $actMentor->specialization ?? 'Guru Pembimbing' }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan & Rekomendasi HR</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Masukkan pertimbangan evaluasi akhir..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Keputusan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const decisionSelect = document.getElementById('decisionSelect');
    const handoverSection = document.getElementById('handoverWizardSection');
    const activeStudentsCount = {{ (int) ($probation->active_students_assigned ?? 0) }};

    if (decisionSelect && handoverSection) {
        decisionSelect.addEventListener('change', function () {
            if (this.value === 'terminated' && activeStudentsCount > 0) {
                handoverSection.style.display = 'block';
            } else {
                handoverSection.style.display = 'none';
            }
        });
    }
});
</script>
@endsection
