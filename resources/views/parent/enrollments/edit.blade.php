@extends('layouts.parent')

@section('title', 'Ubah Jadwal Belajar | AL-HIKMAH')
@section('header', 'Ubah Preferensi Hari & Jam')
@section('subheader', 'Sesuaikan preferensi hari dan jam bimbingan sebelum pembayaran lunas')

@section('content')
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Warning Notice Card -->
            <div class="alert alert-warning border-0 shadow-sm rounded-4 p-4 mb-4 d-flex align-items-start gap-3">
                <i class="bi bi-shield-exclamation text-warning fs-2 flex-shrink-0"></i>
                <div>
                    <h6 class="fw-bold text-dark mb-1">Ketentuan Perubahan Jadwal Bimbingan:</h6>
                    <p class="small text-muted mb-0">
                        Perubahan preferensi hari dan jam bimbingan <strong>hanya dapat dilakukan sebelum pembayaran diselesaikan</strong>. Setelah pembayaran lunas dan kelas telah aktif, jadwal akan <strong>dikunci permanen (locked)</strong> pada sistem demi menjaga kepastian alokasi waktu guru pembimbing.
                    </p>
                </div>
            </div>

            <!-- Program & Santri Info Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="p-4 text-white" style="background: var(--primary-gradient);">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <span class="badge bg-white text-success px-3 py-1 rounded-pill fw-bold mb-2">
                                <i class="bi bi-bookmark-star-fill me-1"></i> {{ ucfirst($enrollment->program->category ?? 'Program') }}
                            </span>
                            <h4 class="fw-bold mb-1">{{ $enrollment->program->name }}</h4>
                            <p class="mb-0 text-white-50 small">
                                <i class="bi bi-person-fill me-1"></i> Santri: <strong>{{ $enrollment->student->getDisplayName() }}</strong> ({{ $enrollment->student->age }} Tahun) &nbsp;•&nbsp;
                                <i class="bi bi-tag-fill me-1"></i> Investasi: <strong>{{ $enrollment->formatted_price }}</strong>
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-10 p-3 rounded-4 border border-white border-opacity-25 text-end">
                            <span class="text-white-50 small d-block mb-1">Status Pendaftaran</span>
                            <span class="badge bg-white text-dark px-3 py-1 rounded-pill fw-bold">
                                <i class="bi {{ $enrollment->status->icon() }} text-success me-1"></i> {{ $enrollment->status->label() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Edit -->
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                <form action="{{ route('parent.enrollments.update', $enrollment->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Metode Belajar -->
                    <div class="mb-4 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">1</span>
                            <h5 class="fw-bold text-heading mb-0">Metode Pembelajaran <span class="text-danger">*</span></h5>
                        </div>

                        @php
                            $selectedMethod = old('learning_method', $enrollment->learning_method ?? 'offline');
                        @endphp
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="radio" class="btn-check" name="learning_method" value="offline" id="method_offline" {{ $selectedMethod === 'offline' ? 'checked' : '' }}>
                                <label class="method-tile w-100" for="method_offline">
                                    <div class="method-icon">🏠</div>
                                    <div class="method-title">Offline (Guru Datang)</div>
                                    <div class="method-desc">Guru berkunjung langsung ke rumah santri untuk bimbingan tatap muka intensif.</div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <input type="radio" class="btn-check" name="learning_method" value="online" id="method_online" {{ $selectedMethod === 'online' ? 'checked' : '' }}>
                                <label class="method-tile w-100" for="method_online">
                                    <div class="method-icon">💻</div>
                                    <div class="method-title">Online (Privat Zoom)</div>
                                    <div class="method-desc">Sesi privat interaktif via video call resolusi tinggi dan whiteboard digital.</div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <input type="radio" class="btn-check" name="learning_method" value="hybrid" id="method_hybrid" {{ $selectedMethod === 'hybrid' ? 'checked' : '' }}>
                                <label class="method-tile w-100" for="method_hybrid">
                                    <div class="method-icon">🔄</div>
                                    <div class="method-title">Hybrid (Kombinasi)</div>
                                    <div class="method-desc">Kombinasi fleksibel antara tatap muka dan sesi daring sesuai kesepakatan.</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Hari Belajar -->
                    <div class="mb-4 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">2</span>
                            <h5 class="fw-bold text-heading mb-0">Pilih Hari Belajar <span class="text-danger">*</span></h5>
                        </div>
                        <p class="text-muted small mb-3">Pilih minimal 1 hari yang diinginkan untuk jadwal bimbingan rutin per pekan.</p>

                        @php
                            $selectedDays = old('requested_days', $enrollment->requested_days ?? []);
                        @endphp
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($availableDays as $dayKey => $dayLabel)
                                <div class="form-check-day">
                                    <input class="btn-check" type="checkbox" name="requested_days[]" value="{{ $dayKey }}" id="day_{{ $dayKey }}" {{ in_array($dayKey, $selectedDays) ? 'checked' : '' }}>
                                    <label class="btn btn-outline-success rounded-pill px-3 py-2 fw-semibold" for="day_{{ $dayKey }}">
                                        <i class="bi bi-calendar-event me-1"></i> {{ $dayLabel }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('requested_days')
                            <div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Jam & Catatan -->
                    <div class="mb-4 pb-3">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">3</span>
                            <h5 class="fw-bold text-heading mb-0">Estimasi Jam & Catatan Tambahan</h5>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Estimasi Jam Mulai Belajar (WIB):</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-subtle text-muted rounded-start-pill ps-3">
                                        <i class="bi bi-clock-fill"></i>
                                    </span>
                                    <input type="time" name="requested_time" class="form-control border-subtle rounded-end-pill py-2" value="{{ old('requested_time', $enrollment->requested_time ? date('H:i', strtotime($enrollment->requested_time)) : '16:00') }}">
                                </div>
                                <span class="text-muted small mt-1 d-block">Contoh slot rekomendasi: 08:00 (Pagi), 10:00 (Siang), 16:00 (Sore), 18:30 (Malam).</span>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Catatan Khusus untuk Lembaga & Mentor:</label>
                                <textarea name="parent_notes" class="form-control border-subtle rounded-4 p-3" rows="3" placeholder="Tuliskan catatan preferensi, kondisi ananda, atau permintaan khusus...">{{ old('parent_notes', $enrollment->parent_notes) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('parent.enrollments.show', $enrollment->id) }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill fw-semibold">
                            <i class="bi bi-arrow-left me-1"></i> Batal & Kembali
                        </a>
                        <button type="submit" class="btn btn-success px-5 py-2 rounded-pill fw-bold shadow-sm" style="background: var(--primary-gradient); border: none;">
                            <i class="bi bi-check-circle-fill me-1"></i> Simpan Pembaruan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.method-tile {
    display: block;
    padding: 1.25rem;
    border: 2px solid var(--border-subtle, #e2e8f0);
    border-radius: 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #fff;
}
.method-tile:hover {
    border-color: #10b981;
    transform: translateY(-2px);
}
.btn-check:checked + .method-tile {
    border-color: #10b981;
    background-color: rgba(16, 185, 129, 0.05);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
}
.method-icon {
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
}
.method-title {
    font-weight: 700;
    color: var(--heading-color, #1e293b);
    margin-bottom: 0.25rem;
}
.method-desc {
    font-size: 0.8rem;
    color: #64748b;
    line-height: 1.4;
}
</style>
@endpush
