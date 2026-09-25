@extends('layouts.mentor')

@section('title', 'Ujian Tes Kompetensi Guru - AL-HIKMAH LMS')
@section('header', 'Sesi Evaluasi Kompetensi Guru')
@section('subheader', 'Kerjakan 15 butir soal seleksi kompetensi Tajwid, Makharijul Huruf, dan Tahsin Al-Qur\'an')

@section('content')
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <!-- 1. Header Card & Instruksi (Islamic Editorial Minimalist) -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 text-white overflow-hidden position-relative"
                 style="background: linear-gradient(135deg, #064e3b 0%, #047857 100%);">
                <div class="card-body p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <span class="badge bg-white bg-opacity-20 text-white border border-white border-opacity-25 fw-bold px-3 py-2 rounded-pill mb-3">
                                <i class="bi bi-mortarboard-fill me-1"></i> Ujian Seleksi Calon Guru
                            </span>
                            <h3 class="fw-bold mb-2 text-white" style="letter-spacing: -0.02em;">Tes Kompetensi Tajwid, Makharijul Huruf &amp; Tahsin</h3>
                            <p class="mb-0 text-white-50" style="font-size: 0.95rem; line-height: 1.6;">
                                Sesi evaluasi terbagi dalam 3 kategori standar kompetensi: <strong>Kaidah Tajwid</strong>, <strong>Makharijul Huruf</strong>, dan <strong>Metodologi Tahsin</strong>.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-4 mt-md-0">
                            <div class="rounded-4 p-3 d-inline-block text-center text-white"
                                 style="background: rgba(255, 255, 255, 0.12); border: 1.5px solid rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px); min-width: 150px;">
                                <small class="d-block text-uppercase fw-semibold text-white-50" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Soal</small>
                                <span class="fs-1 fw-bold lh-1 text-white">{{ count($questions) }}</span>
                                <small class="d-block text-white-50 mt-1" style="font-size: 0.75rem;">3 Kategori &bull; 60 Menit</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. 3 Category Banner Overview -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100 border-start border-4" style="border-left-color: #064e3b !important;">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge rounded-circle p-2" style="background-color: #ecfdf5; color: #064e3b;"><i class="bi bi-book"></i></span>
                            <h6 class="fw-bold mb-0 text-dark">1. Tajwid Test</h6>
                        </div>
                        <small class="text-muted">5 Butir Soal (Nun Mati, Mim Sukun, Mad, Ra', Ghunnah)</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100 border-start border-4" style="border-left-color: #b45309 !important;">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge rounded-circle p-2" style="background-color: #fef3c7; color: #b45309;"><i class="bi bi-soundwave"></i></span>
                            <h6 class="fw-bold mb-0 text-dark">2. Makharijul Huruf</h6>
                        </div>
                        <small class="text-muted">5 Butir Soal (Halqiyah, Lisan, Dhad, Hams, Al-Jauf)</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100 border-start border-4" style="border-left-color: #0f766e !important;">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge rounded-circle p-2" style="background-color: #ccfbf1; color: #0f766e;"><i class="bi bi-mic"></i></span>
                            <h6 class="fw-bold mb-0 text-dark">3. Tahsin &amp; Metodologi</h6>
                        </div>
                        <small class="text-muted">5 Butir Soal (Talaqqi, Lahn Jali/Khafi, Gharib, Waqaf)</small>
                    </div>
                </div>
            </div>

            <!-- 3. Petunjuk Pengerjaan -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 p-4" style="background-color: #fafaf9; border: 1px solid rgba(6, 78, 59, 0.12) !important;">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 40px; height: 40px; background-color: #ecfdf5; color: #064e3b;">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Petunjuk Pengisian Soal:</h6>
                        <ul class="mb-0 small text-secondary ps-3" style="line-height: 1.6;">
                            <li>Pilihlah salah satu opsi jawaban (A, B, C, atau D) yang paling tepat untuk setiap nomor pertanyaan.</li>
                            <li>Pastikan seluruh 15 butir soal telah terjawab sebelum menekan tombol <strong>Kirim Jawaban Tes</strong>.</li>
                            <li>Nilai hasil ujian dan analisis per kategori akan langsung dihitung secara otomatis dan tersimpan ke panel admin.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 4. Form Pengerjaan Tes -->
            <form action="{{ route('mentor.recruitment.submit-test', $session->id) }}" method="POST" id="testForm">
                @csrf

                @forelse($questions as $index => $q)
                    @php
                        $category = $q['category'] ?? 'Tajwid Test';
                        $catCode = $q['category_code'] ?? 'tajwid_test';
                        $badgeStyle = match($catCode) {
                            'tajwid_test' => 'background-color: #ecfdf5; color: #064e3b; border-color: rgba(6, 78, 59, 0.2);',
                            'makharijul_huruf' => 'background-color: #fef3c7; color: #b45309; border-color: rgba(180, 83, 9, 0.2);',
                            'tahsin' => 'background-color: #ccfbf1; color: #0f766e; border-color: rgba(15, 118, 110, 0.2);',
                            default => 'background-color: #f1f5f9; color: #475569; border-color: rgba(71, 85, 105, 0.2);',
                        };
                    @endphp

                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white" id="question_card_{{ $index }}" style="border: 1px solid rgba(6, 78, 59, 0.08) !important;">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-color: rgba(6, 78, 59, 0.06) !important;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge text-white rounded-pill px-3 py-2 fw-bold" style="background-color: #064e3b;">
                                    Soal {{ $index + 1 }} dari {{ count($questions) }}
                                </span>
                                <span class="badge border rounded-pill px-3 py-2 fw-semibold" style="{{ $badgeStyle }}">
                                    <i class="bi bi-tag-fill me-1"></i> {{ $category }}
                                </span>
                            </div>
                            @if(isset($q['difficulty']))
                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-1.5 small">
                                    Tingkat: {{ ucfirst($q['difficulty']) }}
                                </span>
                            @endif
                        </div>
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-dark mb-4 lh-base" style="font-size: 1.08rem;">
                                {{ $q['question'] ?? 'Pertanyaan' }}
                            </h5>

                            <div class="d-flex flex-column gap-3">
                                @foreach($q['options'] ?? [] as $optIndex => $optionText)
                                    @php
                                        $inputName = "answers[{$index}]";
                                        $inputId = "q_{$index}_opt_{$optIndex}";
                                        $letter = chr(65 + $optIndex); // A, B, C, D
                                    @endphp
                                    <label class="test-option-label d-flex align-items-center gap-3 cursor-pointer" for="{{ $inputId }}">
                                        <input class="form-check-input mt-0 fs-5 flex-shrink-0 test-option-input" type="radio" name="{{ $inputName }}" id="{{ $inputId }}" value="{{ $optIndex }}" required>
                                        <div class="d-flex align-items-center gap-2 flex-grow-1">
                                            <span class="test-option-letter fw-bold rounded-circle">
                                                {{ $letter }}
                                            </span>
                                            <span class="text-dark fw-semibold test-option-text">{{ $optionText }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                        <i class="bi bi-clipboard-x fs-1 text-muted mb-2"></i>
                        <h5 class="fw-bold text-dark">Belum ada butir soal tersedia</h5>
                        <p class="text-muted small">Silakan hubungi admin panitia rekrutmen.</p>
                    </div>
                @endforelse

                @if(count($questions) > 0)
                    <!-- Tombol Aksi Submit -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-5" style="border: 1px solid rgba(6, 78, 59, 0.1) !important;">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Sudah selesai memeriksa {{ count($questions) }} butir jawaban Anda?</h6>
                                <small class="text-muted">Pastikan semua butir soal terisi lengkap sebelum mengirimkan berkas jawaban.</small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('mentor.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4" onclick="return confirm('Apakah Anda yakin ingin keluar? Perubahan jawaban yang belum disimpan akan hilang.');">
                                    Batal
                                </a>
                                <button type="submit" class="btn text-white rounded-pill px-4 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2"
                                        style="background-color: #064e3b; border-color: #064e3b;"
                                        onclick="return confirm('Apakah Anda yakin ingin mengirim seluruh jawaban tes kompetensi ini?');">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Kirim Jawaban Tes ({{ count($questions) }} Soal)</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>

<style>
.cursor-pointer {
    cursor: pointer;
}
.test-option-label {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    background-color: #fafaf9;
    border: 1.5px solid rgba(6, 78, 59, 0.12);
    border-radius: 12px;
    padding: 14px 18px;
}
.test-option-label:hover {
    background-color: #f0fdf4;
    border-color: rgba(6, 78, 59, 0.35);
}
.test-option-letter {
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: #e7e5e4;
    color: #1c1917;
    font-size: 0.85rem;
    transition: all 0.2s ease;
    flex-shrink: 0;
}
.test-option-input {
    accent-color: #064e3b;
}
.test-option-label:has(.test-option-input:checked) {
    background-color: #ecfdf5 !important;
    border-color: #064e3b !important;
    box-shadow: 0 0 0 3px rgba(6, 78, 59, 0.15) !important;
}
.test-option-label:has(.test-option-input:checked) .test-option-letter {
    background-color: #064e3b !important;
    color: #ffffff !important;
}
.test-option-label:has(.test-option-input:checked) .test-option-text {
    color: #064e3b !important;
}
</style>
@endsection
