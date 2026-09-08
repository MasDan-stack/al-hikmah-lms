@extends('layouts.mentor')

@section('title', 'Ujian Tes Kompetensi Guru - AL-HIKMAH LMS')
@section('header', 'Sesi Evaluasi Kompetensi Guru')
@section('subheader', 'Kerjakan 15 butir soal seleksi kompetensi Tajwid, Makharijul Huruf, dan Tahsin Al-Qur\'an')

@section('content')
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <!-- Header Card & Instruksi -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary text-white overflow-hidden position-relative">
                <div class="card-body p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <span class="badge bg-white text-primary fw-bold px-3 py-2 rounded-pill mb-2">
                                <i class="bi bi-mortarboard-fill me-1"></i> Ujian Seleksi Calon Guru
                            </span>
                            <h3 class="fw-bold mb-2">Tes Kompetensi Tajwid, Makharijul Huruf & Tahsin</h3>
                            <p class="mb-0 text-white-50">
                                Sesi evaluasi terbagi dalam 3 kategori standar kompetensi: <strong>Kaidah Tajwid</strong>, <strong>Makharijul Huruf</strong>, dan <strong>Metodologi Tahsin</strong>.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="bg-white bg-opacity-25 rounded-4 p-3 d-inline-block text-center text-white border border-white border-opacity-25">
                                <small class="d-block text-uppercase fw-semibold" style="font-size: 0.75rem;">Total Soal</small>
                                <span class="fs-2 fw-bold">{{ count($questions) }}</span>
                                <small class="d-block" style="font-size: 0.75rem;">3 Kategori &bull; 60 Menit</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3 Category Banner Overview -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100 border-start border-4 border-primary">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary text-white rounded-circle p-2"><i class="bi bi-book"></i></span>
                            <h6 class="fw-bold mb-0 text-dark">1. Tajwid Test</h6>
                        </div>
                        <small class="text-muted">5 Butir Soal (Nun Mati, Mim Sukun, Mad, Ra', Ghunnah)</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100 border-start border-4 border-success">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-success text-white rounded-circle p-2"><i class="bi bi-soundwave"></i></span>
                            <h6 class="fw-bold mb-0 text-dark">2. Makharijul Huruf</h6>
                        </div>
                        <small class="text-muted">5 Butir Soal (Halqiyah, Lisan, Dhad, Hams, Al-Jauf)</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100 border-start border-4 border-info">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-info text-white rounded-circle p-2"><i class="bi bi-mic"></i></span>
                            <h6 class="fw-bold mb-0 text-dark">3. Tahsin & Metodologi</h6>
                        </div>
                        <small class="text-muted">5 Butir Soal (Talaqqi, Lahn Jali/Khafi, Gharib, Waqaf)</small>
                    </div>
                </div>
            </div>

            <!-- Petunjuk Pengerjaan -->
            <div class="alert alert-info border-0 shadow-sm rounded-4 d-flex align-items-start gap-3 p-4 mb-4">
                <i class="bi bi-info-circle-fill fs-3 text-info"></i>
                <div>
                    <h6 class="fw-bold mb-1">Petunjuk Pengisian Soal:</h6>
                    <ul class="mb-0 small ps-3">
                        <li>Pilihlah salah satu opsi jawaban (A, B, C, atau D) yang paling tepat untuk setiap nomor pertanyaan.</li>
                        <li>Pastikan seluruh 15 butir soal telah terjawab sebelum menekan tombol <strong>Kirim Jawaban Tes</strong>.</li>
                        <li>Nilai hasil ujian dan analisis per kategori akan langsung dihitung secara otomatis dan tersimpan ke panel admin.</li>
                    </ul>
                </div>
            </div>

            <!-- Form Pengerjaan Tes -->
            <form action="{{ route('mentor.recruitment.submit-test', $session->id) }}" method="POST" id="testForm">
                @csrf

                @forelse($questions as $index => $q)
                    @php
                        $category = $q['category'] ?? 'Tajwid Test';
                        $catCode = $q['category_code'] ?? 'tajwid_test';
                        $badgeClass = match($catCode) {
                            'tajwid_test' => 'bg-primary-subtle text-primary border-primary',
                            'makharijul_huruf' => 'bg-success-subtle text-success border-success',
                            'tahsin' => 'bg-info-subtle text-info border-info',
                            default => 'bg-secondary-subtle text-secondary',
                        };
                    @endphp

                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white" id="question_card_{{ $index }}">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary text-white rounded-pill px-3 py-2 fw-bold">
                                    Soal {{ $index + 1 }} dari {{ count($questions) }}
                                </span>
                                <span class="badge {{ $badgeClass }} border rounded-pill px-3 py-2 fw-semibold">
                                    <i class="bi bi-tag-fill me-1"></i> {{ $category }}
                                </span>
                            </div>
                            @if(isset($q['difficulty']))
                                <span class="badge bg-light text-muted border rounded-pill px-3 py-1 small">
                                    Tingkat: {{ $q['difficulty'] }}
                                </span>
                            @endif
                        </div>
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-dark mb-4 lh-base">
                                {{ $q['question'] ?? 'Pertanyaan' }}
                            </h5>

                            <div class="d-flex flex-column gap-3">
                                @foreach($q['options'] ?? [] as $optIndex => $optionText)
                                    @php
                                        $inputName = "answers[{$index}]";
                                        $inputId = "q_{$index}_opt_{$optIndex}";
                                        $letter = chr(65 + $optIndex); // A, B, C, D
                                    @endphp
                                    <label class="option-label border rounded-3 p-3 d-flex align-items-center gap-3 cursor-pointer transition-all" for="{{ $inputId }}">
                                        <input class="form-check-input mt-0 fs-5 flex-shrink-0" type="radio" name="{{ $inputName }}" id="{{ $inputId }}" value="{{ $optIndex }}" required>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-secondary-subtle text-dark fw-bold rounded-circle" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                                {{ $letter }}
                                            </span>
                                            <span class="text-dark fw-semibold">{{ $optionText }}</span>
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
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-5">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Sudah selesai memeriksa 15 butir jawaban Anda?</h6>
                                <small class="text-muted">Pastikan semua kategori (Tajwid, Makharijul Huruf, Tahsin) terjawab lengkap.</small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('mentor.dashboard') }}" class="btn btn-light rounded-pill px-4" onclick="return confirm('Apakah Anda yakin ingin keluar? Perubahan jawaban yang belum disimpan akan hilang.');">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow-sm" onclick="return confirm('Apakah Anda yakin ingin mengirim seluruh jawaban tes kompetensi ini?');">
                                    <i class="bi bi-check-circle-fill me-2"></i>Kirim Jawaban Tes (15 Soal)
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
.option-label {
    transition: all 0.2s ease-in-out;
    background-color: #fafbfc;
}
.option-label:hover {
    background-color: #f0f7ff;
    border-color: #0d6efd !important;
}
.option-label:has(input:checked) {
    background-color: #e8f3ff;
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.2);
}
</style>
@endsection
