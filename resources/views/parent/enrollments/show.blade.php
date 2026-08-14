@extends('layouts.parent')

@section('title', 'Detail Permohonan Pendaftaran | AL-HIKMAH')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pb-3 border-bottom mb-4">
                <div>
                    <span class="badge bg-{{ $enrollment->status->badgeClass() }} px-3 py-2 rounded-pill fw-bold">
                        <i class="bi {{ $enrollment->status->icon() }} me-1"></i> {{ $enrollment->status->label() }}
                    </span>
                    <h5 class="fw-bold mt-2 mb-0">{{ $enrollment->program->name }} — {{ $enrollment->student->getDisplayName() }}</h5>
                </div>
                <div class="text-end">
                    <span class="text-muted small d-block">Nomor Permohonan</span>
                    <span class="fw-bold">#ENR-{{ str_pad($enrollment->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success border-0 rounded-3 mb-4">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning border-0 rounded-3 mb-4">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
                </div>
            @endif

            <div class="row g-4">
                <!-- Kolom Request Parent -->
                <div class="col-md-6">
                    <div class="p-3 rounded-3 bg-light border h-100">
                        <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-person-check me-2"></i>Jadwal Yang Anda Ajukan</h6>
                        <p class="mb-2 small"><strong>Hari Pilihan:</strong> {{ $enrollment->requested_days_label }}</p>
                        <p class="mb-2 small"><strong>Estimasi Jam:</strong> {{ $enrollment->requested_time_label }}</p>
                        <p class="mb-2 small"><strong>Biaya Program Terkunci:</strong> {{ $enrollment->formatted_price }}</p>
                        <p class="mb-0 small"><strong>Catatan Anda:</strong> {{ $enrollment->parent_notes ?? '-' }}</p>
                    </div>
                </div>

                <!-- Kolom Alternatif Lembaga -->
                <div class="col-md-6">
                    <div class="p-3 rounded-3 {{ $enrollment->isWaitingParent() ? 'bg-warning-subtle border border-warning' : 'bg-light border' }} h-100">
                        <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-building me-2"></i>Respon / Alternatif Lembaga</h6>
                        @if($enrollment->offered_days || $enrollment->admin_notes)
                            <p class="mb-2 small"><strong>Hari Ditawarkan:</strong> {{ $enrollment->offered_days_label }}</p>
                            <p class="mb-2 small"><strong>Jam Ditawarkan:</strong> {{ $enrollment->offered_time_label }}</p>
                            @if($enrollment->mentor)
                                <p class="mb-2 small"><strong>Mentor Disarankan:</strong> {{ $enrollment->mentor->getDisplayName() }}</p>
                            @endif
                            <p class="mb-0 small"><strong>Catatan Lembaga:</strong> {{ $enrollment->admin_notes ?? '-' }}</p>
                        @else
                            <p class="text-muted small mb-0">Lembaga sedang mereview jadwal dan ketersediaan kuota guru pembimbing.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action: Respon Alternatif -->
            @if($enrollment->isWaitingParent())
                <div class="alert alert-warning border-0 rounded-3 mt-4 p-4">
                    <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-circle me-1"></i> Tindakan Diperlukan: Konfirmasi Jadwal Alternatif</h6>
                    <p class="small mb-3">Lembaga menawarkan alternatif jadwal di atas. Silakan pilih apakah Anda menyetujui jadwal ini atau ingin dicarikan jadwal lain.</p>
                    <div class="d-flex gap-2">
                        <form action="{{ route('parent.enrollments.accept-offer', $enrollment->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success px-4 rounded-pill">
                                <i class="bi bi-check-circle me-1"></i> Setujui Jadwal Ini
                            </button>
                        </form>
                        <button type="button" class="btn btn-outline-danger px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle me-1"></i> Minta Alternatif Lain
                        </button>
                    </div>
                </div>

                <!-- Modal Tolak -->
                <div class="modal fade" id="rejectModal" tabindex="-1">
                    <div class="modal-dialog">
                        <form action="{{ route('parent.enrollments.reject-offer', $enrollment->id) }}" method="POST">
                            @csrf
                            <div class="modal-content rounded-4 border-0">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Minta Jadwal Lain</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <label class="form-label small fw-bold">Alasan / Preferensi Tambahan</label>
                                    <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Contoh: Jika hari Selasa tidak bisa, bagaimana jika Jumat jam 16:00?"></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Kembali</button>
                                    <button type="submit" class="btn btn-danger rounded-pill">Kirim Permintaan Ulang</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Action: Bayar Tagihan (Jika Sudah Confirmed) -->
            @if($enrollment->isConfirmed() && $enrollment->payment)
                <div class="alert alert-success-subtle border-0 rounded-3 mt-4 p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h6 class="fw-bold text-success mb-1"><i class="bi bi-check2-all me-1"></i> Jadwal Telah Disepakati!</h6>
                        <p class="small text-muted mb-0">Invoice tagihan pendaftaran sebesar <strong>Rp {{ number_format($enrollment->payment->amount, 0, ',', '.') }}</strong> telah siap dibayar.</p>
                    </div>
                    <div>
                        <a href="{{ route('parent.payments.show', $enrollment->payment->id) }}" class="btn btn-primary-custom px-4 py-2 rounded-pill shadow-sm">
                            <i class="bi bi-wallet2 me-1"></i> Bayar Sekarang
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
