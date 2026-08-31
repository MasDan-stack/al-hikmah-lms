@extends('layouts.parent')

@section('title', 'Detail Sesi Belajar')
@section('header', 'Detail Sesi Belajar')
@section('subheader', 'Informasi waktu, lokasi, mentor, dan konfirmasi kehadiran ananda')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Detail Sesi #{{ $session->id }}</h4>
        <a href="{{ route('parent.schedules.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Detail Info Sesi -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <h5 class="fw-bold text-dark border-bottom pb-3 mb-3">Informasi Sesi Mengajar</h5>

                <div class="mb-3">
                    <div class="text-muted small fw-bold">SANTRI BINAAN:</div>
                    <div class="fs-5 fw-bold text-dark">{{ $session->student?->user?->name ?? $session->student?->full_name }}</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small fw-bold">WAKTU & TANGGAL:</div>
                    <div class="fw-bold text-primary">{{ $session->date ? \Carbon\Carbon::parse($session->date)->locale('id')->isoFormat('dddd, D MMMM Y') : '-' }} (Jam {{ date('H:i', strtotime($session->time)) }} WIB)</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small fw-bold">METODE BELAJAR:</div>
                    @if($session->method === 'offline')
                        <span class="badge bg-success-subtle text-success fs-6 rounded-pill px-3 py-1 border border-success-subtle"><i class="bi bi-house-door me-1"></i> Offline (Home Visit)</span>
                    @elseif($session->method === 'online')
                        <span class="badge bg-primary-subtle text-primary fs-6 rounded-pill px-3 py-1 border border-primary-subtle"><i class="bi bi-camera-video me-1"></i> Online</span>
                    @else
                        <span class="badge bg-info-subtle text-info fs-6 rounded-pill px-3 py-1 border border-info-subtle"><i class="bi bi-arrow-repeat me-1"></i> Hybrid</span>
                    @endif
                </div>

                <div class="mb-3">
                    <div class="text-muted small fw-bold">PEMBIMBING / MENTOR:</div>
                    <div class="fw-semibold text-dark">{{ $session->mentor?->user?->name ?? 'Ustaz/Ustazah' }}</div>
                    <small class="text-secondary">{{ $session->mentor?->specialization ?? 'Guru Al-Qur\'an' }}</small>
                </div>

                <div class="mb-3">
                    <div class="text-muted small fw-bold">CATATAN SESI:</div>
                    <div class="p-3 bg-light rounded-3 text-secondary">{{ $session->notes ?? 'Tidak ada catatan tambahan.' }}</div>
                </div>
            </div>
        </div>

        <!-- Form Konfirmasi Kehadiran Orang Tua -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <h5 class="fw-bold text-dark border-bottom pb-3 mb-3">
                    <i class="bi bi-check2-square text-success me-2"></i>Konfirmasi Kehadiran Anak
                </h5>
                <p class="text-muted small">Bantu mentor mempersiapkan sesi bimbingan dengan mengonfirmasi kehadiran ananda.</p>

                @if($confirmation)
                    <div class="alert alert-info rounded-3 mb-3">
                        <div class="fw-bold mb-1"><i class="bi bi-info-circle me-1"></i>Status Konfirmasi Saat Ini:</div>
                        <span class="badge bg-success rounded-pill px-3 fs-6">{{ strtoupper($confirmation->status) }}</span>
                        @if($confirmation->notes)
                            <div class="small mt-2 text-dark">Catatan: "{{ $confirmation->notes }}"</div>
                        @endif
                    </div>
                @endif

                <form action="{{ route('parent.schedules.confirm', $session->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Status Kehadiran *</label>
                        <select name="status" class="form-select" required>
                            <option value="hadir" {{ ($confirmation?->status === 'hadir') ? 'selected' : '' }}>Hadir (Siap Mengikuti Bimbingan)</option>
                            <option value="izin" {{ ($confirmation?->status === 'izin') ? 'selected' : '' }}>Izin (Berhalangan Hadir)</option>
                            <option value="sakit" {{ ($confirmation?->status === 'sakit') ? 'selected' : '' }}>Sakit (Kurang Sehat)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan untuk Mentor (Opsional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Ananda sedikit batuk, mohon bimbingan tidak terlalu berat...">{{ $confirmation?->notes }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-success rounded-pill w-100 fw-bold shadow-sm">
                        <i class="bi bi-send me-1"></i> Kirim Konfirmasi Kehadiran
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
