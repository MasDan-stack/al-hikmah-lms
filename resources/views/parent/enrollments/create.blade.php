@extends('layouts.parent')

@section('title', 'Pilih Jadwal Belajar | AL-HIKMAH')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="p-2 rounded-circle bg-success-subtle me-3">
                            <i class="bi bi-calendar-plus text-success fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Pengajuan Pendaftaran & Jadwal Belajar</h5>
                            <p class="text-muted small mb-0">Tentukan santri binaan dan preferensi jadwal belajar Anda.</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Ringkasan Program & Penguncian Harga -->
                    <div class="alert alert-success-subtle border-0 rounded-3 mb-4 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-success mb-1">{{ ucfirst($program->category) }}</span>
                            <h6 class="fw-bold mb-0 text-success-emphasis">{{ $program->name }}</h6>
                            <small class="text-muted">{{ $program->level }} • {{ $program->duration_weeks }} Minggu</small>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small d-block">Investasi Belajar Terkunci</span>
                            <span class="fw-bold text-success fs-5">{{ $program->formatted_price }}</span>
                        </div>
                    </div>

                    <form action="{{ route('parent.enrollments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="program_id" value="{{ $program->id }}">

                        <!-- Pilih Santri -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-secondary">Pilih Santri / Anak Binaan <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Anak --</option>
                                @foreach($children as $child)
                                    <option value="{{ $child->id }}" {{ old('student_id') == $child->id ? 'selected' : '' }}>
                                        {{ $child->getDisplayName() }} ({{ $child->age }} Tahun)
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        <!-- Pilih Metode Belajar -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-secondary d-block">Pilih Metode Belajar <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div class="form-check p-2 border rounded-3 text-center">
                                        <input class="form-check-input ms-0 me-2" type="radio" name="learning_method" value="offline" id="method_offline" {{ old('learning_method', 'offline') === 'offline' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium small" for="method_offline">
                                            🏠 Offline (Home Visit)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check p-2 border rounded-3 text-center">
                                        <input class="form-check-input ms-0 me-2" type="radio" name="learning_method" value="online" id="method_online" {{ old('learning_method') === 'online' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium small" for="method_online">
                                            💻 Online (Zoom / Video)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check p-2 border rounded-3 text-center">
                                        <input class="form-check-input ms-0 me-2" type="radio" name="learning_method" value="hybrid" id="method_hybrid" {{ old('learning_method') === 'hybrid' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium small" for="method_hybrid">
                                            🔄 Hybrid (Kombinasi)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pilih Hari -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-secondary d-block">Preferensi Hari Belajar (Bisa Pilih > 1) <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                @foreach($availableDays as $val => $dayLabel)
                                    <div class="col-6 col-md-3">
                                        <div class="form-check p-2 border rounded-3 text-center">
                                            <input class="form-check-input ms-0 me-2" type="checkbox" name="requested_days[]" value="{{ $val }}" id="day_{{ $val }}" {{ is_array(old('requested_days')) && in_array($val, old('requested_days')) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium small" for="day_{{ $val }}">
                                                {{ $dayLabel }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('requested_days')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pilih Jam & Catatan -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-secondary">Perkiraan Jam Belajar (Opsional)</label>
                                <input type="time" name="requested_time" class="form-control @error('requested_time') is-invalid @enderror" value="{{ old('requested_time') }}">
                                <small class="text-muted">Kosongkan jika fleksibel mengikuti jadwal mentor.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-secondary">Catatan Khusus (Opsional)</label>
                                <textarea name="parent_notes" class="form-control @error('parent_notes') is-invalid @enderror" rows="2" placeholder="Misal: Lebih nyaman mentor wanita, atau ada hari libur keluarga.">{{ old('parent_notes') }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                            <a href="{{ route('biaya') }}" class="btn btn-outline-secondary px-4 rounded-pill">Batal</a>
                            <button type="submit" class="btn btn-primary-custom px-4 rounded-pill">
                                <i class="bi bi-send me-1"></i> Ajukan Permohonan Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
