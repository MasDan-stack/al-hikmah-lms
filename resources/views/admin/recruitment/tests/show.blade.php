@extends('layouts.admin')

@section('title', 'Detail Evaluasi & Data Soal Tes Calon Guru')
@section('header', 'Detail Sesi Ujian Kompetensi Guru')
@section('subheader', 'Inspeksi 15 butir soal, kunci jawaban, dan hasil pengerjaan pelamar')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold"><i class="bi bi-file-earmark-check text-primary me-2"></i>Evaluasi Tes: {{ $session->application->full_name ?? 'Calon Guru' }}</h1>
            <p class="text-muted small mb-0">No. Registrasi: <span class="fw-semibold text-primary font-monospace">{{ $session->application->application_code ?? '-' }}</span> &bull; Bidang: {{ $session->application->specialization ?? '-' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.recruitment.tests.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Tes
            </a>
            <a href="{{ route('admin.recruitment.applications.show', $session->application_id) }}" class="btn btn-sm btn-primary rounded-pill px-3">
                <i class="bi bi-person-badge me-1"></i> Lihat Profil Pelamar
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $payload = $session->ai_question_payload ?? [];
        $questions = $payload['questions'] ?? [];
        $applicantAnswers = $payload['applicant_answers'] ?? [];
        $categoryScores = $payload['category_scores'] ?? [];
        $isCompleted = ($session->status === 'completed');
    @endphp

    <!-- Top Stats Cards: Skor Total & Kategori -->
    <div class="row g-3 mb-4">
        <!-- Skor Total -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary text-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-white text-primary rounded-pill fw-bold px-3 py-1">Skor Akhir</span>
                    <i class="bi bi-trophy fs-3 text-white-50"></i>
                </div>
                <h1 class="display-5 fw-bold mb-0">{{ $session->score !== null ? number_format($session->score, 1) : '-' }}</h1>
                <small class="text-white-50 mt-1 d-block">
                    Predikat: <strong class="text-white text-uppercase">{{ str_replace('_', ' ', $session->grade ?? ($isCompleted ? 'Selesai' : 'Belum Ujian')) }}</strong>
                </small>
            </div>
        </div>

        <!-- 1. Tajwid Score -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4 border-start border-4 border-primary bg-white">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="text-muted small fw-semibold">1. Tajwid Test</div>
                    <span class="badge bg-primary-subtle text-primary rounded-pill">5 Soal</span>
                </div>
                <h3 class="fw-bold text-dark mb-0">
                    {{ $categoryScores['tajwid_test']['correct'] ?? '-' }} <span class="fs-6 text-muted">/ {{ $categoryScores['tajwid_test']['total'] ?? 5 }} Benar</span>
                </h3>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $categoryScores['tajwid_test']['percentage'] ?? 0 }}%"></div>
                </div>
                <small class="text-muted mt-1 d-block">Akurasi: {{ $categoryScores['tajwid_test']['percentage'] ?? 0 }}%</small>
            </div>
        </div>

        <!-- 2. Makharijul Huruf Score -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4 border-start border-4 border-success bg-white">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="text-muted small fw-semibold">2. Makharijul Huruf</div>
                    <span class="badge bg-success-subtle text-success rounded-pill">5 Soal</span>
                </div>
                <h3 class="fw-bold text-dark mb-0">
                    {{ $categoryScores['makharijul_huruf']['correct'] ?? '-' }} <span class="fs-6 text-muted">/ {{ $categoryScores['makharijul_huruf']['total'] ?? 5 }} Benar</span>
                </h3>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $categoryScores['makharijul_huruf']['percentage'] ?? 0 }}%"></div>
                </div>
                <small class="text-muted mt-1 d-block">Akurasi: {{ $categoryScores['makharijul_huruf']['percentage'] ?? 0 }}%</small>
            </div>
        </div>

        <!-- 3. Tahsin Score -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4 border-start border-4 border-info bg-white">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="text-muted small fw-semibold">3. Tahsin Al-Qur'an</div>
                    <span class="badge bg-info-subtle text-info rounded-pill">5 Soal</span>
                </div>
                <h3 class="fw-bold text-dark mb-0">
                    {{ $categoryScores['tahsin']['correct'] ?? '-' }} <span class="fs-6 text-muted">/ {{ $categoryScores['tahsin']['total'] ?? 5 }} Benar</span>
                </h3>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $categoryScores['tahsin']['percentage'] ?? 0 }}%"></div>
                </div>
                <small class="text-muted mt-1 d-block">Akurasi: {{ $categoryScores['tahsin']['percentage'] ?? 0 }}%</small>
            </div>
        </div>
    </div>

    <!-- Data Soal 15 Butir & Analisis Jawaban -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-primary mb-0"><i class="bi bi-collection me-2"></i>Bank Data Soal & Analisis Pengerjaan ({{ count($questions) }} Soal)</h5>
                <small class="text-muted">Rincian butir soal pilihan ganda, kunci jawaban resmi, dan jawaban yang dipilih calon guru.</small>
            </div>
            <div>
                @if($isCompleted)
                    <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill fw-bold">
                        <i class="bi bi-check2-all me-1"></i> Sudah Dikerjakan & Dinilai
                    </span>
                @else
                    <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2 rounded-pill fw-bold">
                        <i class="bi bi-hourglass-split me-1"></i> Menunggu Calon Guru Mengerjakan
                    </span>
                @endif
            </div>
        </div>

        <div class="card-body p-4">
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

                    $answerData = $applicantAnswers[$index] ?? null;
                    $submittedOpt = $answerData['submitted_answer'] ?? null;
                    $correctAnsOpt = (int) ($q['correct_answer'] ?? 0);
                    $isAnswerCorrect = $answerData['is_correct'] ?? false;
                @endphp

                <div class="p-4 rounded-4 mb-4 border {{ $loop->even ? 'bg-light bg-opacity-50' : 'bg-white' }}">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary text-white rounded-pill px-3 py-1 fw-bold">
                                No. {{ $index + 1 }}
                            </span>
                            <span class="badge {{ $badgeClass }} border rounded-pill px-3 py-1 fw-semibold">
                                {{ $category }}
                            </span>
                        </div>

                        @if($isCompleted && $answerData !== null)
                            @if($isAnswerCorrect)
                                <span class="badge bg-success text-white px-3 py-1 rounded-pill">
                                    <i class="bi bi-check-circle-fill me-1"></i> Jawaban Benar (+1)
                                </span>
                            @else
                                <span class="badge bg-danger text-white px-3 py-1 rounded-pill">
                                    <i class="bi bi-x-circle-fill me-1"></i> Jawaban Salah (0)
                                </span>
                            @endif
                        @endif
                    </div>

                    <h6 class="fw-bold text-dark mb-3 lh-base">
                        {{ $q['question'] ?? 'Pertanyaan' }}
                    </h6>

                    <!-- Opsi Jawaban -->
                    <div class="row g-2 mb-3">
                        @foreach($q['options'] ?? [] as $optIdx => $optText)
                            @php
                                $letter = chr(65 + $optIdx);
                                $isOfficialCorrect = ($optIdx === $correctAnsOpt);
                                $isSelectedByCandidate = ($submittedOpt !== null && $optIdx === (int) $submittedOpt);

                                $cardBorder = 'border-light-subtle';
                                $bgClass = 'bg-white';

                                if ($isOfficialCorrect) {
                                    $cardBorder = 'border-success border-2 shadow-sm';
                                    $bgClass = 'bg-success bg-opacity-10';
                                } elseif ($isSelectedByCandidate && !$isAnswerCorrect) {
                                    $cardBorder = 'border-danger border-2 shadow-sm';
                                    $bgClass = 'bg-danger bg-opacity-10';
                                }
                            @endphp

                            <div class="col-md-6">
                                <div class="p-3 rounded-3 border {{ $cardBorder }} {{ $bgClass }} d-flex align-items-center justify-content-between h-100">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge {{ $isOfficialCorrect ? 'bg-success text-white' : ($isSelectedByCandidate ? 'bg-danger text-white' : 'bg-light text-dark border') }} rounded-circle fw-bold" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                            {{ $letter }}
                                        </span>
                                        <span class="small fw-semibold text-dark">{{ $optText }}</span>
                                    </div>

                                    <div>
                                        @if($isOfficialCorrect)
                                            <span class="badge bg-success text-white rounded-pill px-2 py-1 small" title="Kunci Jawaban Resmi">
                                                <i class="bi bi-check-lg me-1"></i>Kunci
                                            </span>
                                        @endif
                                        @if($isSelectedByCandidate)
                                            <span class="badge {{ $isAnswerCorrect ? 'bg-primary' : 'bg-danger' }} text-white rounded-pill px-2 py-1 small ms-1" title="Jawaban Pilihan Pelamar">
                                                Pilihan Guru
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Penjelasan Ilmiah Tajwid -->
                    @if(!empty($q['explanation']))
                        <div class="p-3 rounded-3 bg-light border border-info border-opacity-25 mt-2">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-info-circle text-info mt-1"></i>
                                <div>
                                    <small class="fw-bold text-dark d-block">Kaidah Tajwid & Pembahasan:</small>
                                    <small class="text-muted">{{ $q['explanation'] }}</small>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                    <p class="mb-0">Data butir soal belum di-generate untuk sesi ini.</p>
                </div>
            @endforelse
        </div>

        <div class="card-footer bg-light p-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <small class="text-muted">
                Catatan Evaluasi: <strong>{{ $session->evaluator_notes ?? 'Tersimpan otomatis oleh sistem.' }}</strong>
            </small>
            <a href="{{ route('admin.recruitment.applications.show', $session->application_id) }}" class="btn btn-primary rounded-pill px-4 fw-semibold">
                <i class="bi bi-person-check me-1"></i> Proses Keputusan Lamaran
            </a>
        </div>
    </div>
</div>
@endsection
