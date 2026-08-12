@extends('layouts.admin')

@section('title', 'Manajemen Pembayaran SPP')
@section('header', 'Kelola Tagihan & SPP')
@section('subheader', 'Terbitkan tagihan baru, sesuaikan nominal SPP, dan pantau status pembayaran orang tua')

@section('content')
<div class="container-fluid p-0">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Cards Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-danger-subtle text-danger fs-3">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Nominal Tagihan Pending</div>
                        <h4 class="fw-bold mb-0 text-danger">Rp {{ number_format($totalPendingAmount, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success-subtle text-success fs-3">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Nominal SPP Terbayar Lunas</div>
                        <h4 class="fw-bold mb-0 text-success">Rp {{ number_format($totalPaidAmount, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table & Actions -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-1"><i class="bi bi-receipt-cutoff text-primary me-2"></i>Daftar Seluruh Tagihan SPP & Pembayaran</h5>
                <small class="text-muted">Kelola penerbitan tagihan SPP dan status pembayaran santri</small>
            </div>

            <div class="d-flex gap-2">
                <!-- Tombol Buka Modal Buat Tagihan Baru -->
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createPaymentModal">
                    <i class="bi bi-plus-circle me-1"></i> Terbitkan Tagihan SPP Baru
                </button>

                <!-- Form Kirim Pengingat Massal -->
                <form action="{{ route('admin.payments.send-reminder') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-4 fw-bold">
                        <i class="bi bi-send me-1"></i> Kirim Pengingat ke Semua
                    </button>
                </form>
            </div>
        </div>

        @if($allPayments->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-receipt fs-1 text-secondary d-block mb-2"></i>
                <h6 class="fw-bold text-dark mb-1">Belum Ada Tagihan Terbit</h6>
                <p class="small text-muted mb-3">Klik tombol di atas untuk menerbitkan tagihan SPP pertama santri.</p>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#createPaymentModal">
                    Terbitkan Tagihan Sekarang
                </button>
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No. Invoice</th>
                            <th>Santri Binaan</th>
                            <th>Wali Santri</th>
                            <th>Program</th>
                            <th>Nominal Tagihan</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allPayments as $p)
                            <tr>
                                <td class="fw-bold text-primary">#{{ $p->invoice_number ?? ('INV-' . $p->id) }}</td>
                                <td class="fw-semibold">{{ $p->student?->user?->name ?? $p->student?->full_name }}</td>
                                <td class="small text-secondary">{{ $p->student?->parent?->user?->name ?? 'Wali Santri' }}</td>
                                <td><span class="badge bg-secondary-subtle text-dark">{{ $p->program?->name ?? 'SPP Bimbingan' }}</span></td>
                                <td class="fw-bold text-dark">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $p->due_date ? $p->due_date->format('d/m/Y') : '-' }}</span></td>
                                <td>
                                    @if($p->status === 'paid')
                                        <span class="badge bg-success rounded-pill px-3">LUNAS</span>
                                    @else
                                        <span class="badge bg-warning text-dark rounded-pill px-3">PENDING</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <!-- Edit Modal Trigger -->
                                        <button type="button" class="btn btn-sm btn-outline-warning rounded-circle" data-bs-toggle="modal" data-bs-target="#editPaymentModal{{ $p->id }}" title="Edit Nominal / Status">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <!-- Form Delete -->
                                        <form action="{{ route('admin.payments.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tagihan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Hapus Tagihan">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- 📝 MODAL EDIT TAGIHAN -->
                                    <div class="modal fade" id="editPaymentModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content rounded-4 border-0 shadow">
                                                <div class="modal-header border-bottom">
                                                    <h5 class="modal-title fw-bold">Edit Tagihan #{{ $p->invoice_number }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.payments.update', $p->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Santri</label>
                                                            <input type="text" class="form-control bg-light" value="{{ $p->student?->user?->name ?? $p->student?->full_name }}" readonly>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Nominal SPP (Rp) *</label>
                                                            <input type="number" name="amount" class="form-control" value="{{ old('amount', $p->amount) }}" min="1000" step="1000" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Tanggal Jatuh Tempo *</label>
                                                            <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $p->due_date ? $p->due_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Status Pembayaran *</label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="pending" {{ $p->status === 'pending' ? 'selected' : '' }}>Pending (Belum Dibayar)</option>
                                                                <option value="paid" {{ $p->status === 'paid' ? 'selected' : '' }}>Paid (Lunas)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top">
                                                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-warning rounded-pill fw-bold px-4">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $allPayments->links() }}
            </div>
        @endif
    </div>
</div>

<!-- ➕ MODAL INPUT / BUAT TAGIHAN SPP BARU -->
<div class="modal fade" id="createPaymentModal" tabindex="-1" aria-labelledby="createPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-bold" id="createPaymentModalLabel"><i class="bi bi-wallet2 me-2"></i>Terbitkan Tagihan SPP Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.payments.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pilih Santri Binaan *</label>
                            <select name="student_id" class="form-select" required>
                                <option value="">-- Pilih Santri --</option>
                                @foreach($students as $s)
                                    <option value="{{ $s->id }}">{{ $s->user?->name ?? $s->full_name }} (Wali: {{ $s->parent?->user?->name ?? 'Wali' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pilih Program / Kelas (Opsional)</label>
                            <select name="program_id" class="form-select">
                                <option value="">-- SPP Umum Bimbingan --</option>
                                @foreach($programs as $prog)
                                    <option value="{{ $prog->id }}">{{ $prog->name }} (Rp {{ number_format($prog->price, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nominal Tagihan SPP (Rp) *</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">Rp</span>
                                <input type="number" name="amount" class="form-control" placeholder="250000" min="1000" step="1000" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Jatuh Tempo *</label>
                            <input type="date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor Invoice (Opsional)</label>
                            <input type="text" name="invoice_number" class="form-control" placeholder="Kosongkan untuk auto-generate (e.g. INV-202608-XXXX)">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Awal *</label>
                            <select name="status" class="form-select" required>
                                <option value="pending">Pending (Belum Dibayar)</option>
                                <option value="paid">Paid (Langsung Lunas)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-save me-1"></i> Terbitkan Tagihan SPP
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
