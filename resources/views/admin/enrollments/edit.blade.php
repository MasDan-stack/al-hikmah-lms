@extends('layouts.admin')

@section('title', 'Proses Pendaftaran & Negosiasi Jadwal | Admin AL-HIKMAH')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Summary Header -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom pb-3 mb-3">
                        <div>
                            <span class="badge bg-{{ $enrollment->status->badgeClass() }} px-3 py-2 rounded-pill">
                                {{ $enrollment->status->label() }}
                            </span>
                            <h5 class="fw-bold mt-2 mb-0">Permohonan Pendaftaran #ENR-{{ str_pad($enrollment->id, 5, '0', STR_PAD_LEFT) }}</h5>
                        </div>
                        <div class="text-end">
                            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-arrow-left me-1"></i> Kembali ke Antrean
                            </a>
                        </div>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger border-0 rounded-3 mb-4">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block">Santri / Anak</small>
                                <span class="fw-bold">{{ $enrollment->student->getDisplayName() }}</span>
                                <small class="d-block text-muted">{{ $enrollment->student->age }} Tahun</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block">Orang Tua / Wali</small>
                                <span class="fw-bold">{{ $enrollment->student->getParentNameAttribute() }}</span>
                                <small class="d-block text-muted"><i class="bi bi-telephone me-1"></i>{{ $enrollment->student->getParentPhoneAttribute() }}</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block">Program & Harga Terkunci</small>
                                <span class="fw-bold text-success">{{ $enrollment->program->name }}</span>
                                <small class="d-block text-muted">{{ $enrollment->formatted_price }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two Action Panels: Accept vs Counter-Offer -->
            <div class="row g-4">
                <!-- Panel Opsi A: Setujui Jadwal Request -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-success-subtle border-0 pt-3 px-4">
                            <h6 class="fw-bold text-success-emphasis mb-0"><i class="bi bi-check-circle me-2"></i>OPSI A: Setujui Jadwal Orang Tua</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="p-3 bg-light rounded-3 mb-3 small">
                                <p class="mb-1"><strong>Hari Diminta:</strong> {{ $enrollment->requested_days_label }}</p>
                                <p class="mb-1"><strong>Jam Diminta:</strong> {{ $enrollment->requested_time_label }}</p>
                                <p class="mb-0"><strong>Catatan Wali:</strong> {{ $enrollment->parent_notes ?? '-' }}</p>
                            </div>

                            <form action="{{ route('admin.enrollments.accept', $enrollment->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Pilih Mentor Pembimbing <span class="text-danger">*</span></label>
                                    <select name="mentor_id" class="form-select" required>
                                        <option value="">-- Pilih Mentor --</option>
                                        @foreach($mentors as $mentor)
                                            <option value="{{ $mentor->id }}" {{ old('mentor_id', $enrollment->mentor_id) == $mentor->id ? 'selected' : '' }}>
                                                {{ $mentor->getDisplayName() }} (Keahlian: {{ $mentor->specialization ?? '-' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Tanggal Mulai Belajar <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $enrollment->start_date?->format('Y-m-d') ?? date('Y-m-d', strtotime('+3 days'))) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Catatan Admin (Opsional)</label>
                                    <textarea name="admin_notes" class="form-control" rows="2" placeholder="Catatan untuk orang tua...">{{ old('admin_notes') }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-success w-100 rounded-pill py-2">
                                    <i class="bi bi-check-lg me-1"></i> Setujui Jadwal & Terbitkan Invoice
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Panel Opsi B: Tawarkan Alternatif Jadwal (Counter-Offer) -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-warning-subtle border-0 pt-3 px-4">
                            <h6 class="fw-bold text-warning-emphasis mb-0"><i class="bi bi-chat-dots me-2"></i>OPSI B: Tawarkan Alternatif Jadwal</h6>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('admin.enrollments.offer', $enrollment->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Saran Mentor (Opsional)</label>
                                    <select name="mentor_id" class="form-select">
                                        <option value="">-- Pilih Mentor Jika Ada --</option>
                                        @foreach($mentors as $mentor)
                                            <option value="{{ $mentor->id }}" {{ old('mentor_id', $enrollment->mentor_id) == $mentor->id ? 'selected' : '' }}>
                                                {{ $mentor->getDisplayName() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small d-block">Tawarkan Hari Alternatif <span class="text-danger">*</span></label>
                                    <div class="row g-2">
                                        @foreach($days as $val => $dayLabel)
                                            <div class="col-6">
                                                <div class="form-check p-1 border rounded text-center">
                                                    <input class="form-check-input ms-0 me-1" type="checkbox" name="offered_days[]" value="{{ $val }}" id="offered_{{ $val }}" {{ is_array(old('offered_days', $enrollment->offered_days)) && in_array($val, old('offered_days', $enrollment->offered_days ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="offered_{{ $val }}">{{ $dayLabel }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Jam Alternatif (Opsional)</label>
                                    <input type="time" name="offered_time" class="form-control" value="{{ old('offered_time', $enrollment->offered_time) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Penjelasan Alasan / Catatan Admin <span class="text-danger">*</span></label>
                                    <textarea name="admin_notes" class="form-control" rows="2" placeholder="Contoh: Jadwal Senin jam 15:00 penuh, mentor bersedia di hari Selasa jam 16:00." required>{{ old('admin_notes', $enrollment->admin_notes) }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-warning w-100 rounded-pill py-2">
                                    <i class="bi bi-send me-1"></i> Kirim Tawaran Alternatif Ke Orang Tua
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
