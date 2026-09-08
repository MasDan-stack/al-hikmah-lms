@extends('layouts.student')

@section('title', 'Target Milestone Santri')
@section('header', 'Target Jangka Panjang (Milestone)')
@section('subheader', 'Pasang target besar untuk menyemangati perjalanan hafalan Al-Qur\'an Anda.')

@section('content')
<div class="row g-4 mb-4">
    <!-- Form Tambah Milestone -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-flag-fill text-warning me-2"></i>Buat Target Milestone Baru</h6>
            <form action="{{ route('student.milestones.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Target</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Khatam Juz 29 Sebelum Ramadhan" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Jenis Target</label>
                    <select name="target_type" class="form-select" required>
                        <option value="juz_completion">Khatam 1 Juz</option>
                        <option value="ayat_milestone">Pencapaian Jumlah Ayat</option>
                        <option value="exam">Ujian Mutqin</option>
                        <option value="custom">Target Khusus Lainnya</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Target Tanggal Selesai</label>
                    <input type="datetime-local" name="target_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Target Jumlah (Ayat / Target Angka)</label>
                    <input type="number" name="progress_goal" class="form-control" value="100" min="1" required>
                </div>
                <button type="submit" class="btn btn-warning text-dark w-100 rounded-pill fw-bold">
                    <i class="bi bi-rocket-takeoff me-1"></i> Mulai Target Milestone
                </button>
            </form>
        </div>
    </div>

    <!-- Daftar Milestone Aktif & Selesai -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-list-check text-success me-2"></i>Daftar Milestone Anda</h6>

            <div class="row g-3">
                @forelse($milestones as $ms)
                    <div class="col-12">
                        <div class="p-3 rounded-4 border bg-light-subtle position-relative overflow-hidden">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-1 mb-1" style="font-size: 0.7rem;">
                                        {{ strtoupper(str_replace('_', ' ', $ms->target_type)) }}
                                    </span>
                                    <h6 class="fw-bold mb-0">{{ $ms->name }}</h6>
                                    <small class="text-muted">Tenggat: {{ $ms->target_date->translatedFormat('d F Y, H:i') }} WIB</small>
                                </div>
                                <div>
                                    @if($ms->status === 'achieved')
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 fw-bold">
                                            <i class="bi bi-check-circle-fill me-1"></i> Tercapai 🎉
                                        </span>
                                    @elseif($ms->status === 'expired')
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-2 fw-bold">
                                            <i class="bi bi-clock-history me-1"></i> Kadaluarsa
                                        </span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-bold">
                                            <i class="bi bi-hourglass-split me-1"></i> Aktif Berjalan
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @php
                                $percent = $ms->progress_goal > 0 ? min(100, round(($ms->progress_current / $ms->progress_goal) * 100)) : 0;
                            @endphp
                            <div class="mt-3">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Progress: {{ $ms->progress_current }} / {{ $ms->progress_goal }}</span>
                                    <span class="fw-bold text-success">{{ $percent }}%</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 8px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percent }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="bi bi-flag" style="font-size: 2.5rem;"></i>
                        <p class="mt-2 mb-0">Belum ada target milestone yang dibuat. Pasang target pertama Anda sekarang!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
