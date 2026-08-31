@extends('layouts.parent')

@section('title', 'Pilih Jadwal Belajar | AL-HIKMAH')
@section('header', 'Form Pendaftaran Program')
@section('subheader', 'Tentukan santri binaan, metode belajar, dan preferensi waktu bimbingan')

@section('content')
<div class="container-fluid p-0">
    <!-- Stepper Indicator -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="stepper-wizard mb-0">
                    <div class="stepper-line"></div>
                    <div class="stepper-step completed">
                        <div class="stepper-icon"><i class="bi bi-check-lg"></i></div>
                        <div class="stepper-label">1. Pilih Program</div>
                    </div>
                    <div class="stepper-step active">
                        <div class="stepper-icon"><i class="bi bi-calendar-check"></i></div>
                        <div class="stepper-label">2. Atur Jadwal</div>
                    </div>
                    <div class="stepper-step">
                        <div class="stepper-icon"><i class="bi bi-person-badge"></i></div>
                        <div class="stepper-label">3. Review Lembaga</div>
                    </div>
                    <div class="stepper-step">
                        <div class="stepper-icon"><i class="bi bi-credit-card"></i></div>
                        <div class="stepper-label">4. Pembayaran</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Program Overview Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="p-4 text-white" style="background: var(--primary-gradient);">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <span class="badge bg-white text-success px-3 py-1 rounded-pill fw-bold mb-2">
                                <i class="bi bi-bookmark-star-fill me-1"></i> {{ ucfirst($program->category) }}
                            </span>
                            <h4 class="fw-bold mb-1">{{ $program->name }}</h4>
                            <p class="mb-0 text-white-50 small">
                                <i class="bi bi-bar-chart-steps me-1"></i> Level: <strong>{{ $program->level }}</strong> &nbsp;•&nbsp; 
                                <i class="bi bi-hourglass-split me-1"></i> Durasi: <strong>{{ $program->duration_weeks }} Minggu</strong>
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-10 p-3 rounded-4 border border-white border-opacity-25 text-end">
                            <span class="text-white-50 small d-block mb-1"><i class="bi bi-shield-lock-fill me-1"></i> Biaya Investasi Terkunci</span>
                            <span class="fw-bold fs-4 text-white">{{ $program->formatted_price }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Form Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                <form action="{{ route('parent.enrollments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="program_id" value="{{ $program->id }}">

                    <!-- Step 1: Pilih Santri -->
                    <div class="mb-4 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">1</span>
                            <h5 class="fw-bold text-heading mb-0">Pilih Santri / Anak Binaan <span class="text-danger">*</span></h5>
                        </div>
                        <p class="text-muted small mb-3">Pilih ananda yang akan mengikuti bimbingan pada program ini.</p>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-subtle text-muted rounded-start-pill ps-3">
                                        <i class="bi bi-person-fill"></i>
                                    </span>
                                    <select name="student_id" class="form-select border-subtle rounded-end-pill py-2 @error('student_id') is-invalid @enderror" required>
                                        <option value="">-- Klik untuk memilih anak --</option>
                                        @foreach($children as $child)
                                            <option value="{{ $child->id }}" {{ old('student_id') == $child->id ? 'selected' : '' }}>
                                                {{ $child->getDisplayName() }} &nbsp;({{ $child->age }} Tahun • {{ ucfirst($child->gender ?? 'Santri') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('student_id')
                                        <div class="invalid-feedback ps-3">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4 mt-2 mt-md-0 d-flex align-items-center">
                                <a href="{{ route('parent.profile.children') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                    <i class="bi bi-plus-circle me-1"></i> Tambah Data Anak
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Pilih Metode Belajar -->
                    <div class="mb-4 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">2</span>
                            <h5 class="fw-bold text-heading mb-0">Pilih Metode Pembelajaran <span class="text-danger">*</span></h5>
                        </div>
                        <p class="text-muted small mb-3">Tentukan fleksibilitas metode kehadiran guru pembimbing.</p>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="radio" class="btn-check" name="learning_method" value="offline" id="method_offline" {{ old('learning_method', 'offline') === 'offline' ? 'checked' : '' }}>
                                <label class="method-tile w-100" for="method_offline">
                                    <div class="method-icon">🏠</div>
                                    <div class="method-title">Offline (Home Visit)</div>
                                    <div class="method-desc">Guru datang langsung mendampingi di rumah Anda.</div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <input type="radio" class="btn-check" name="learning_method" value="online" id="method_online" {{ old('learning_method') === 'online' ? 'checked' : '' }}>
                                <label class="method-tile w-100" for="method_online">
                                    <div class="method-icon">💻</div>
                                    <div class="method-title">Online (Interactive)</div>
                                    <div class="method-desc">Belajar tatap muka virtual via Zoom / Meet HD.</div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <input type="radio" class="btn-check" name="learning_method" value="hybrid" id="method_hybrid" {{ old('learning_method') === 'hybrid' ? 'checked' : '' }}>
                                <label class="method-tile w-100" for="method_hybrid">
                                    <div class="method-icon">🔄</div>
                                    <div class="method-title">Hybrid (Kombinasi)</div>
                                    <div class="method-desc">Kombinasi fleksibel pertemuan offline & online.</div>
                                </label>
                            </div>
                        </div>
                        @error('learning_method')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Step 3: Preferensi Hari Belajar -->
                    <div class="mb-4 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">3</span>
                            <h5 class="fw-bold text-heading mb-0">Preferensi Hari Bimbingan <span class="text-danger">*</span></h5>
                        </div>
                        <p class="text-muted small mb-3">Pilih hari-hari yang Anda inginkan (bisa memilih lebih dari 1 hari).</p>

                        <div class="row g-2">
                            @foreach($availableDays as $val => $dayLabel)
                                <div class="col-6 col-md-3 col-lg">
                                    <input type="checkbox" class="btn-check" name="requested_days[]" value="{{ $val }}" id="day_{{ $val }}" {{ is_array(old('requested_days')) && in_array($val, old('requested_days')) ? 'checked' : '' }}>
                                    <label class="day-chip-label" for="day_{{ $val }}">
                                        <i class="bi bi-calendar-event me-1"></i> {{ $dayLabel }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('requested_days')
                            <div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Step 4: Estimasi Jam & Catatan Khusus -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">4</span>
                            <h5 class="fw-bold text-heading mb-0">Waktu & Catatan Tambahan (Opsional)</h5>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold small text-secondary">
                                    <i class="bi bi-clock me-1 text-success"></i> Perkiraan Jam Belajar
                                </label>
                                <input type="time" name="requested_time" class="form-control rounded-pill px-3 py-2 border-subtle @error('requested_time') is-invalid @enderror" value="{{ old('requested_time') }}">
                                <span class="text-muted small d-block mt-1">Kosongkan jika fleksibel mengikuti kesediaan guru.</span>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label fw-semibold small text-secondary">
                                    <i class="bi bi-chat-left-quote me-1 text-success"></i> Catatan / Preferensi Khusus
                                </label>
                                <textarea name="parent_notes" class="form-control rounded-4 p-3 border-subtle @error('parent_notes') is-invalid @enderror" rows="2" placeholder="Contoh: Lebih nyaman dibimbing oleh ustadzah (guru wanita), ananda baru mulai dari Iqra 2, dsb.">{{ old('parent_notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Jaminan & Keamanan Informasi -->
                    <div class="alert alert-light border border-subtle rounded-4 p-3 mb-4 d-flex align-items-center gap-3">
                        <div class="p-2 rounded-circle bg-success-subtle text-success fs-4 flex-shrink-0">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="small text-muted">
                            <strong class="text-heading d-block mb-1">Jaminan Kecocokan Jadwal & Garansi Guru Berkualitas:</strong>
                            Setelah formulir dikirimkan, tim akademik AL-HIKMAH akan mencocokkan jadwal terbaik dengan mentor berdedikasi dalam waktu 1x24 jam. Biaya program telah terkunci dan tidak berubah.
                        </div>
                    </div>

                    <!-- Form Action Bar -->
                    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('biaya') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                            <i class="bi bi-arrow-left me-1"></i> Batal & Kembali
                        </a>
                        <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill fw-bold shadow-sm" style="background: var(--primary-gradient); border: none;">
                            <i class="bi bi-send-fill me-2"></i> Ajukan Permohonan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
