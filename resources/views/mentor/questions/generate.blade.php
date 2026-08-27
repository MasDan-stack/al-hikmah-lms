@extends('layouts.mentor')

@section('title', 'AI Auto-Generate Soal & Evaluasi')

@section('content')
<div class="container-fluid pb-5">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="section-badge mb-2"><i class="bi bi-stars"></i> Powered by Al-Hikmah Smart AI</div>
            <h1 class="h3 fw-bold mb-1">AI Auto-Generate Soal & <span class="text-gradient">Evaluasi</span></h1>
            <p class="text-muted small mb-0">Hasilkan 5 hingga 20 butir soal pilihan ganda secara otomatis dan instan menggunakan kecerdasan Al-Hikmah AI Engine.</p>
        </div>
        <div>
            <a href="{{ route('mentor.questions.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Bank Soal
            </a>
        </div>
    </div>

    <!-- Alert Box for Error / Info Messages -->
    <div id="aiAlertContainer" class="d-none mb-4">
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-start gap-3 p-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-4 text-danger flex-shrink-0"></i>
            <div class="flex-grow-1">
                <strong class="d-block mb-1 text-danger-emphasis" id="aiAlertTitle">Terjadi Kendala</strong>
                <span class="small text-secondary" id="aiAlertMessage">Pesan error akan muncul di sini.</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>

    <!-- Info Banner Kuota Harian AI -->
    <div class="alert border-0 rounded-4 shadow-sm d-flex align-items-start gap-3 p-3 mb-4" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(13, 202, 240, 0.08) 100%); border-left: 4px solid var(--accent-emerald, #10b981) !important;">
        <div class="d-flex align-items-center justify-content-center rounded-circle bg-white text-success shadow-xs flex-shrink-0" style="width: 38px; height: 38px;">
            <i class="bi bi-lightbulb-fill fs-5"></i>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                <strong class="text-dark fw-bold">Panduan & Kuota Harian AI Generator</strong>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0 small fw-medium">Kuota 10–20 Sesi / Hari</span>
            </div>
            <p class="small text-secondary mb-0">
                Untuk menjaga kenyamanan bersama dan akurasi materi, setiap mentor dialokasikan hingga <strong>10–20 sesi generate per hari</strong>. Kami sarankan membuat <strong>5–10 butir soal per sesi</strong> agar susunan pertanyaan lebih terfokus dan berkualitas. Kuota akan diperbarui secara otomatis setiap harinya.
            </p>
        </div>
    </div>

    <!-- AI Generator Config Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 prayer-box" style="background: var(--card-bg);">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0 text-success"><i class="bi bi-sliders me-2"></i> Konfigurasi Parameter AI</h5>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 small">
                    <i class="bi bi-cpu-fill me-1"></i> Al-Hikmah Smart AI Active
                </span>
            </div>

            <form id="aiGenerateForm">
                @csrf
                <div class="row g-3">
                    <!-- Program Belajar Dropdown -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small" for="programSelect">Target Program Belajar (Folder Bank Soal) <span class="text-danger">*</span></label>
                        <select name="program_id" id="programSelect" class="form-select rounded-3 py-2" required>
                            <option value="">-- Pilih Program Belajar --</option>
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->level ?? 'Semua Tingkat' }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">Pilih program di mana butir soal yang di-generate ini akan disimpan.</small>
                    </div>

                    <!-- Tingkat Kesulitan -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small">Tingkat Kesulitan <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3 pt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="difficulty" id="diffMudah" value="Mudah">
                                <label class="form-check-input-label fw-medium text-success" for="diffMudah">🟢 Mudah</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="difficulty" id="diffSedang" value="Sedang" checked>
                                <label class="form-check-input-label fw-medium text-warning" for="diffSedang">🟡 Sedang</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="difficulty" id="diffSulit" value="Sulit">
                                <label class="form-check-input-label fw-medium text-danger" for="diffSulit">🔴 Sulit (HOTS)</label>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-1">Pilih tingkat kompleksitas soal (Mudah, Sedang, atau Analisis Mendalam).</small>
                    </div>

                    <!-- Input Topik Materi Bebas -->
                    <div class="col-md-8">
                        <label class="form-label fw-semibold text-secondary small" for="topicInput">Topik, Tema Bebas, atau Ide Pertanyaan Soal <span class="text-danger">*</span></label>
                        <textarea name="topic" id="topicInput" class="form-control rounded-3 py-2" rows="2" placeholder="Contoh: Cara mengenal diri dalam Al-Qur'an dan dalil suratnya, Makna Surah Al-Fatihah, Hukum Nun Mati & Tanwin, Fiqih Shalat, Kisah Nabi Musa AS..." required minlength="3"></textarea>
                        <small class="text-muted d-block mt-1">Bebas memasukkan topik apapun: materi silabus, studi tematik ayat Al-Qur'an, Hadits, Fiqih, Aqidah-Akhlak, Sirah, maupun Bahasa Arab.</small>
                        
                        <!-- Quick Suggestion Pills -->
                        <div class="mt-2 d-flex flex-wrap gap-1 align-items-center">
                            <span class="small text-muted me-1"><i class="bi bi-lightbulb text-warning"></i> Inspirasi Topik:</span>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill py-0 px-2 small suggestion-pill" data-topic="Cara Mengenal Diri dan Hakikat Penciptaan Manusia dalam Al-Qur'an">🌟 Mengenal Diri (Al-Qur'an)</button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill py-0 px-2 small suggestion-pill" data-topic="Tafsir Ayat Kursi dan Keagungan Tauhid">📖 Tafsir Ayat Kursi</button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill py-0 px-2 small suggestion-pill" data-topic="Hukum Nun Mati dan Tanwin">🕌 Nun Mati & Tanwin</button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill py-0 px-2 small suggestion-pill" data-topic="Syarat, Rukun & Pembatal Shalat Fardhu">⚖️ Fiqih Shalat</button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill py-0 px-2 small suggestion-pill" data-topic="Kisah Keteladanan Nabi Ibrahim AS dalam Al-Qur'an">📜 Kisah Nabi Ibrahim</button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill py-0 px-2 small suggestion-pill" data-topic="Percakapan & Kosakata Bahasa Arab Harian">🗣️ Bahasa Arab Harian</button>
                        </div>
                    </div>

                    <!-- Counter Slider Jumlah Soal -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary small d-flex justify-content-between" for="countInput">
                            <span>Jumlah Butir Soal</span>
                            <span class="badge bg-success" id="countBadge">5 Soal</span>
                        </label>
                        <input type="range" name="count" id="countInput" class="form-range py-2" min="5" max="20" step="1" value="5">
                        <div class="d-flex justify-content-between small text-muted">
                            <span>5 Soal</span>
                            <span>10 Soal</span>
                            <span>15 Soal</span>
                            <span>20 Soal</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" id="btnGenerate" class="btn btn-primary-custom w-100 py-3 rounded-3 fw-bold fs-5 shadow-sm">
                        <i class="bi bi-cpu-fill me-2"></i> Generate Soal Otomatis dengan AI
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Animated Pulse Skeleton Loader (Hidden by default) -->
    <div id="skeletonLoader" class="d-none mb-4">
        <div class="p-4 text-center rounded-4 mb-4 border border-success-subtle shadow-sm" style="background: rgba(16, 185, 129, 0.06);">
            <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-success-subtle text-success mb-3 shadow-sm">
                <div class="spinner-border text-success" style="width: 2.5rem; height: 2.5rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <h5 class="fw-bold text-success mb-1" id="loadingStageTitle">Al-Hikmah Smart AI Sedang Memproses...</h5>
            <p class="text-muted small mb-3" id="loadingStageDesc">Menganalisis parameter materi dan merumuskan butir soal...</p>
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-white border rounded-pill shadow-xs">
                <span class="spinner-grow spinner-grow-sm text-success" role="status"></span>
                <span class="small text-secondary fw-semibold">Waktu Proses: <span id="loadingElapsedTime" class="text-success fw-bold">0</span> detik</span>
            </div>
        </div>

        <div class="row g-3">
            @for($i = 1; $i <= 3; $i++)
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4 skeleton-pulse">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="skeleton-box rounded-circle" style="width: 40px; height: 40px;"></div>
                            <div class="skeleton-box rounded-3" style="width: 60%; height: 24px;"></div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6"><div class="skeleton-box rounded-3" style="height: 48px;"></div></div>
                            <div class="col-md-6"><div class="skeleton-box rounded-3" style="height: 48px;"></div></div>
                            <div class="col-md-6"><div class="skeleton-box rounded-3" style="height: 48px;"></div></div>
                            <div class="col-md-6"><div class="skeleton-box rounded-3" style="height: 48px;"></div></div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <!-- Generated Questions Review Area (Hidden by default) -->
    <form id="storeBatchForm" method="POST" action="{{ route('mentor.questions.store-batch') }}">
        @csrf
        <input type="hidden" name="program_id" id="storeProgramId">
        <input type="hidden" name="topic" id="storeTopic">
        <input type="hidden" name="difficulty" id="storeDifficulty">

        <div id="reviewWorkspace" class="d-none">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-pencil-square me-2 text-success"></i>Tinjau & Edit Hasil Generate AI
                </h5>
                <span class="badge bg-emerald-badge text-emerald px-3 py-2 rounded-pill fw-semibold" id="readyCountBadge">
                    0 Soal Siap Disimpan
                </span>
            </div>

            <p class="text-muted small mb-4">Anda dapat mengubah redaksi pertanyaan, opsi jawaban, memilih kunci jawaban yang benar, atau menghapus butir soal yang tidak sesuai sebelum disimpan ke Bank Soal.</p>

            <!-- Container Kartu Soal Dinamis -->
            <div id="questionsContainer" class="d-flex flex-column gap-4 mb-5"></div>

            <!-- Sticky Bottom Action Bar -->
            <div class="card border-0 shadow-lg rounded-4 sticky-bottom-bar p-3" style="background: var(--card-bg); border-top: 2px solid var(--primary) !important;">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle text-primary fs-5"></i>
                        <span class="text-secondary small fw-medium" id="bottomStatusText">Pastikan seluruh butir soal telah diperiksa dengan seksama.</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 w-100 w-md-auto">
                        <button type="button" id="btnRegenerate" class="btn btn-outline-secondary rounded-3 px-4 py-2 fw-semibold w-100 w-md-auto">
                            <i class="bi bi-arrow-clockwise me-1"></i> Generate Ulang
                        </button>
                        <button type="submit" id="btnSaveBatch" class="btn btn-primary-custom rounded-3 px-4 py-2 fw-bold w-100 w-md-auto">
                            <i class="bi bi-cloud-arrow-up-fill me-2"></i> Simpan Semua ke Bank Soal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .skeleton-pulse {
        background: var(--card-bg);
        animation: pulse 1.5s infinite ease-in-out;
    }
    .skeleton-box {
        background: rgba(0, 0, 0, 0.08);
        border-radius: var(--radius-sm);
    }
    [data-bs-theme="dark"] .skeleton-box {
        background: rgba(255, 255, 255, 0.1);
    }
    @keyframes pulse {
        0% { opacity: 0.6; }
        50% { opacity: 1; }
        100% { opacity: 0.6; }
    }
    .sticky-bottom-bar {
        position: sticky;
        bottom: 1.5rem;
        z-index: 1010;
    }
    .question-card-item {
        border-left: 4px solid var(--primary) !important;
        transition: all 0.2s ease;
    }
    .question-card-item:hover {
        box-shadow: var(--shadow-md) !important;
    }
    .option-pill-item {
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        transition: all 0.2s ease;
        background: var(--card-bg);
    }
    .option-pill-item.is-selected {
        border-color: var(--primary) !important;
        background: var(--primary-lighter) !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const aiForm = document.getElementById('aiGenerateForm');
    const btnGenerate = document.getElementById('btnGenerate');
    const skeletonLoader = document.getElementById('skeletonLoader');
    const reviewWorkspace = document.getElementById('reviewWorkspace');
    const questionsContainer = document.getElementById('questionsContainer');
    const countInput = document.getElementById('countInput');
    const countBadge = document.getElementById('countBadge');
    const readyCountBadge = document.getElementById('readyCountBadge');
    
    const storeBatchForm = document.getElementById('storeBatchForm');
    const storeProgramId = document.getElementById('storeProgramId');
    const storeTopic = document.getElementById('storeTopic');
    const storeDifficulty = document.getElementById('storeDifficulty');
    const btnRegenerate = document.getElementById('btnRegenerate');
    const btnSaveBatch = document.getElementById('btnSaveBatch');

    const aiAlertContainer = document.getElementById('aiAlertContainer');
    const aiAlertTitle = document.getElementById('aiAlertTitle');
    const aiAlertMessage = document.getElementById('aiAlertMessage');

    function showAlert(title, message) {
        if (aiAlertContainer && aiAlertTitle && aiAlertMessage) {
            aiAlertTitle.textContent = title;
            aiAlertMessage.textContent = message;
            aiAlertContainer.classList.remove('d-none');
            aiAlertContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            alert(`${title}: ${message}`);
        }
    }

    function hideAlert() {
        if (aiAlertContainer) {
            aiAlertContainer.classList.add('d-none');
        }
    }

    // Update Slider Badge
    if (countInput && countBadge) {
        countInput.addEventListener('input', function() {
            countBadge.textContent = this.value + ' Soal';
        });
    }

    // Suggestion Pills
    document.querySelectorAll('.suggestion-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            document.getElementById('topicInput').value = this.getAttribute('data-topic');
        });
    });

    // AJAX Generate Event
    aiForm.addEventListener('submit', function(e) {
        e.preventDefault();
        hideAlert();
        
        const programId = document.getElementById('programSelect').value;
        const topic = document.getElementById('topicInput').value.trim();
        const count = countInput.value;
        const difficulty = document.querySelector('input[name="difficulty"]:checked').value;

        if (!programId) {
            showAlert('Pilihan Program Wajib', 'Mohon pilih Program Belajar terlebih dahulu.');
            return;
        }

        if (!topic || topic.length < 3) {
            showAlert('Topik Materi Terlalu Pendek', 'Mohon masukkan topik silabus minimal 3 karakter.');
            return;
        }

        // Show Skeleton & Disable Button
        btnGenerate.disabled = true;
        btnGenerate.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memproses Generate Soal...';
        skeletonLoader.classList.remove('d-none');
        reviewWorkspace.classList.add('d-none');
        questionsContainer.innerHTML = '';

        // Progress Stages & Timer
        const stageTitle = document.getElementById('loadingStageTitle');
        const stageDesc = document.getElementById('loadingStageDesc');
        const elapsedTimeEl = document.getElementById('loadingElapsedTime');
        let elapsedSeconds = 0;
        if (elapsedTimeEl) elapsedTimeEl.textContent = '0';

        const stages = [
            { t: 0, title: 'Al-Hikmah Smart AI Sedang Memproses...', desc: 'Menganalisis parameter materi dan merumuskan butir soal...' },
            { t: 3, title: 'Menganalisis Topik & Kaidah Materi...', desc: 'AI sedang merumuskan studi kasus materi, potongan ayat, dan variasi butir soal...' },
            { t: 7, title: 'Menyusun Pilihan Ganda & Kunci Jawaban...', desc: 'Membangun 4 opsi jawaban berkualitas dan menentukan kunci jawaban yang akurat...' },
            { t: 12, title: 'Memverifikasi Pembahasan & Dalil...', desc: 'Menyempurnakan penjelasan materi/kaidah dan memformat hasil...' },
            { t: 18, title: 'Hampir Selesai...', desc: 'Melakukan validasi akhir struktur data soal sebelum ditampilkan...' }
        ];

        const timerInterval = setInterval(() => {
            elapsedSeconds++;
            if (elapsedTimeEl) elapsedTimeEl.textContent = elapsedSeconds;

            for (let i = stages.length - 1; i >= 0; i--) {
                if (elapsedSeconds >= stages[i].t) {
                    if (stageTitle) stageTitle.textContent = stages[i].title;
                    if (stageDesc) stageDesc.textContent = stages[i].desc;
                    break;
                }
            }
        }, 1000);

        const stopProgressTimer = () => {
            clearInterval(timerInterval);
        };

        fetch("{{ route('mentor.questions.preview') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                program_id: programId,
                topic: topic,
                count: count,
                difficulty: difficulty
            })
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                throw new Error(data.message || `Layanan AI mengembalikan kode HTTP ${res.status}`);
            }
            return data;
        })
        .then(response => {
            stopProgressTimer();
            skeletonLoader.classList.add('d-none');
            btnGenerate.disabled = false;
            btnGenerate.innerHTML = '<i class="bi bi-cpu-fill me-2"></i> Generate Soal Otomatis dengan AI';

            if (response.status === 'success' && Array.isArray(response.data) && response.data.length > 0) {
                // Populate Store Form Hidden Inputs
                storeProgramId.value = programId;
                storeTopic.value = topic;
                storeDifficulty.value = difficulty;

                renderQuestions(response.data);
                reviewWorkspace.classList.remove('d-none');
                
                // Scroll to Workspace
                reviewWorkspace.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                showAlert('Gagal Menghasilkan Soal', response.message || 'Terjadi kesalahan sistem saat memproses soal.');
            }
        })
        .catch(err => {
            stopProgressTimer();
            skeletonLoader.classList.add('d-none');
            btnGenerate.disabled = false;
            btnGenerate.innerHTML = '<i class="bi bi-cpu-fill me-2"></i> Generate Soal Otomatis dengan AI';
            console.error(err);
            showAlert('Koneksi AI Terputus', err.message || 'Terjadi kendala koneksi atau batas kuota harian sistem tercapai. Silakan coba sesaat lagi.');
        });
    });

    // Regenerate Button Click
    if (btnRegenerate) {
        btnRegenerate.addEventListener('click', function() {
            aiForm.dispatchEvent(new Event('submit'));
        });
    }

    // Submit Validation for Batch Store
    if (storeBatchForm) {
        storeBatchForm.addEventListener('submit', function(e) {
            const totalCards = document.querySelectorAll('.question-card-item').length;
            if (totalCards === 0) {
                e.preventDefault();
                showAlert('Tidak Ada Soal', 'Mohon sediakan minimal 1 butir soal sebelum menyimpan ke Bank Soal.');
                return false;
            }

            if (btnSaveBatch) {
                btnSaveBatch.disabled = true;
                btnSaveBatch.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyimpan ke Bank Soal...';
            }
        });
    }

    // Render Question Review Cards
    function renderQuestions(items) {
        questionsContainer.innerHTML = '';
        
        items.forEach((q, idx) => {
            const card = document.createElement('div');
            card.className = 'card border-0 shadow-sm rounded-4 question-card-item p-4';
            card.setAttribute('data-question-idx', idx);

            let optionsHtml = '';
            const labels = ['A', 'B', 'C', 'D'];
            const correctAns = parseInt(q.correct_answer) || 0;
            
            (q.options || ['', '', '', '']).forEach((optText, optIdx) => {
                const isChecked = (optIdx === correctAns) ? 'checked' : '';
                const isSelectedClass = (optIdx === correctAns) ? 'is-selected' : '';

                optionsHtml += `
                    <div class="col-md-6">
                        <div class="p-2 rounded-3 option-pill-item d-flex align-items-center gap-2 ${isSelectedClass}" data-opt-idx="${optIdx}">
                            <div class="form-check mb-0">
                                <input class="form-check-input correct-ans-radio" type="radio" name="questions[${idx}][correct_answer]" value="${optIdx}" id="opt_${idx}_${optIdx}" ${isChecked} required>
                                <label class="form-check-label fw-bold text-success" for="opt_${idx}_${optIdx}">${labels[optIdx]}</label>
                            </div>
                            <input type="text" name="questions[${idx}][options][${optIdx}]" class="form-control form-control-sm border-0 bg-transparent" value="${escapeHtml(optText)}" required>
                        </div>
                    </div>
                `;
            });

            card.innerHTML = `
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success rounded-circle p-2 fs-6" style="width:36px; height:36px; display:inline-flex; align-items:center; justify-content:center;">${idx + 1}</span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 small">
                            <i class="bi bi-stars"></i> Al-Hikmah Smart AI
                        </span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle remove-card-btn" title="Hapus Butir Soal Ini">
                        <i class="bi bi-trash fs-5"></i>
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small text-secondary">Teks Pertanyaan Soal:</label>
                    <textarea name="questions[${idx}][question]" class="form-control rounded-3" rows="2" required>${escapeHtml(q.question)}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small text-secondary">Pilihan Jawaban (Tandai Radio Button untuk Jawaban Benar):</label>
                    <div class="row g-2 options-row">
                        ${optionsHtml}
                    </div>
                </div>

                <div>
                    <label class="form-label fw-semibold small text-secondary">Penjelasan Kaidah Materi:</label>
                    <textarea name="questions[${idx}][explanation]" class="form-control rounded-3" rows="2">${escapeHtml(q.explanation || '')}</textarea>
                </div>
            `;

            questionsContainer.appendChild(card);
        });

        updateCountBadges();
        attachOptionChangeListeners();

        // Attach Remove Buttons Event
        document.querySelectorAll('.remove-card-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const card = this.closest('.question-card-item');
                card.remove();
                reindexCards();
                updateCountBadges();
            });
        });
    }

    function attachOptionChangeListeners() {
        document.querySelectorAll('.correct-ans-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const card = this.closest('.question-card-item');
                if (card) {
                    card.querySelectorAll('.option-pill-item').forEach(item => {
                        item.classList.remove('is-selected');
                    });
                    const parentPill = this.closest('.option-pill-item');
                    if (parentPill) {
                        parentPill.classList.add('is-selected');
                    }
                }
            });
        });
    }

    function reindexCards() {
        document.querySelectorAll('.question-card-item').forEach((card, newIdx) => {
            card.setAttribute('data-question-idx', newIdx);
            const numBadge = card.querySelector('.badge.bg-success');
            if (numBadge) numBadge.textContent = newIdx + 1;
            
            // Reindex Input Names
            const questionText = card.querySelector('textarea[name*="[question]"]');
            if (questionText) questionText.name = `questions[${newIdx}][question]`;

            const explanationText = card.querySelector('textarea[name*="[explanation]"]');
            if (explanationText) explanationText.name = `questions[${newIdx}][explanation]`;

            const optionsInputs = card.querySelectorAll('input[name*="[options]"]');
            optionsInputs.forEach((optInput, optIdx) => {
                optInput.name = `questions[${newIdx}][options][${optIdx}]`;
            });

            const radios = card.querySelectorAll('input[name*="[correct_answer]"]');
            radios.forEach((radio, optIdx) => {
                radio.name = `questions[${newIdx}][correct_answer]`;
                radio.id = `opt_${newIdx}_${optIdx}`;
                const label = radio.nextElementSibling;
                if (label) label.htmlFor = `opt_${newIdx}_${optIdx}`;
            });
        });
        attachOptionChangeListeners();
    }

    function updateCountBadges() {
        const total = document.querySelectorAll('.question-card-item').length;
        readyCountBadge.textContent = total + ' Soal Siap Disimpan';
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
</script>
@endpush

