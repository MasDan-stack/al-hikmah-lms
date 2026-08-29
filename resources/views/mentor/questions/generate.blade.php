@extends('layouts.mentor')

@section('title', 'AI Auto-Generate Soal & Evaluasi')

@section('content')
<div class="container-fluid pb-5">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="section-badge mb-2"><i class="bi bi-stars"></i> Powered by Al-Hikmah Smart AI</div>
            <h1 class="h3 fw-bold mb-1">AI Auto-Generate Soal & <span class="text-gradient">Evaluasi</span></h1>
            <p class="text-muted small mb-0">Hasilkan 3 hingga 25 butir soal Pilihan Ganda maupun Essay/Uraian secara otomatis dan instan sesuai kurikulum terstandar.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('mentor.questions.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
                <i class="bi bi-arrow-left me-1"></i> Bank Soal
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

    <!-- Info Banner Panduan & Fleksibilitas Topik AI -->
    <div class="alert border-0 rounded-4 shadow-sm d-flex align-items-start gap-3 p-3 mb-4" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(13, 202, 240, 0.08) 100%); border-left: 4px solid var(--accent-emerald, #10b981) !important;">
        <div class="d-flex align-items-center justify-content-center rounded-circle bg-white text-success shadow-xs flex-shrink-0" style="width: 40px; height: 40px;">
            <i class="bi bi-robot fs-5"></i>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                <strong class="text-dark fw-bold">Al-Hikmah AI Generator & Search Engine</strong>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0 small fw-medium">Pilihan Ganda & Essay</span>
            </div>
            <p class="small text-secondary mb-0">
                Pilih <strong>Program Belajar</strong> untuk melihat inspirasi topik otomatis, atau ketik topik materi khusus yang Anda inginkan (Tajwid, Makharijul Huruf, Fiqih Nisa, Nahwu-Sharaf, Adab, dsb). Jika kolom topik dikosongkan, AI akan bertindak sebagai mesin pencari silabus cerdas yang memilihkan materi terbaik secara otomatis.
            </p>
        </div>
    </div>

    <!-- AI Generator Config Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 prayer-box" style="background: var(--card-bg);">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="fw-bold mb-0 text-success"><i class="bi bi-sliders me-2"></i> Konfigurasi Parameter Pembuatan Soal</h5>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 small">
                    <i class="bi bi-cpu-fill me-1"></i> AI Active: <strong class="text-uppercase">{{ $activeProvider ?? 'AI' }}</strong> ({{ $activeModel ?? 'Standard' }})
                </span>
            </div>

            <form id="aiGenerateForm">
                @csrf
                <div class="row g-3">
                    <!-- 1. Program Belajar Dropdown -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small" for="programSelect">
                            1. Target Program Belajar <span class="text-danger">*</span>
                        </label>
                        <select name="program_id" id="programSelect" class="form-select rounded-3 py-2" required>
                            <option value="">-- Pilih Program Belajar (10 Program) --</option>
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}" data-name="{{ $p->name }}" {{ $loop->first ? 'selected' : '' }}>
                                    {{ $p->name }} ({{ $p->level ?? 'Semua Tingkat' }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">Inspirasi topik di bawah akan otomatis menyesuaikan dengan program ini.</small>
                    </div>

                    <!-- 2. Tipe Soal (Pilihan Ganda, Essay, Campuran) -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small">
                            2. Format & Tipe Soal <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex flex-wrap gap-2 pt-1">
                            <div class="form-check form-check-inline border rounded-3 px-3 py-1 bg-light">
                                <input class="form-check-input" type="radio" name="question_type" id="typeMC" value="multiple_choice" checked>
                                <label class="form-check-label fw-semibold text-dark small" for="typeMC">
                                    <i class="bi bi-ui-radios me-1 text-primary"></i> Pilihan Ganda (A-D)
                                </label>
                            </div>
                            <div class="form-check form-check-inline border rounded-3 px-3 py-1 bg-light">
                                <input class="form-check-input" type="radio" name="question_type" id="typeEssay" value="essay">
                                <label class="form-check-label fw-semibold text-dark small" for="typeEssay">
                                    <i class="bi bi-pencil-square me-1 text-success"></i> Soal Essay / Uraian
                                </label>
                            </div>
                            <div class="form-check form-check-inline border rounded-3 px-3 py-1 bg-light">
                                <input class="form-check-input" type="radio" name="question_type" id="typeMixed" value="mixed">
                                <label class="form-check-label fw-semibold text-dark small" for="typeMixed">
                                    <i class="bi bi-shuffle me-1 text-info"></i> Campuran
                                </label>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-1">Pilih format butir soal yang ingin dihasilkan.</small>
                    </div>

                    <!-- 3. Tingkat Kesulitan -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small">
                            3. Tingkat Kesulitan <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex gap-3 pt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="difficulty" id="diffMudah" value="Mudah">
                                <label class="form-check-label fw-medium text-success" for="diffMudah">🟢 Mudah</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="difficulty" id="diffSedang" value="Sedang" checked>
                                <label class="form-check-label fw-medium text-warning" for="diffSedang">🟡 Sedang</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="difficulty" id="diffSulit" value="Sulit">
                                <label class="form-check-label fw-medium text-danger" for="diffSulit">🔴 Sulit (HOTS & Analisis)</label>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-1">Pilih tingkat kompleksitas evaluasi pemahaman santri.</small>
                    </div>

                    <!-- 4. Counter Slider Jumlah Soal -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small d-flex justify-content-between" for="countInput">
                            <span>4. Jumlah Butir Soal</span>
                            <span class="badge bg-success" id="countBadge">5 Soal</span>
                        </label>
                        <input type="range" name="count" id="countInput" class="form-range py-2" min="3" max="25" step="1" value="5">
                        <div class="d-flex justify-content-between small text-muted">
                            <span>3 Soal</span>
                            <span>5 Soal</span>
                            <span>10 Soal</span>
                            <span>15 Soal</span>
                            <span>25 Soal</span>
                        </div>
                    </div>

                    <!-- 5. Input Topik Materi & Inspirasi Dinamis -->
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold text-secondary small mb-0" for="topicInput">
                                5. Topik, Tema Silabus, atau Ide Pertanyaan Khusus
                            </label>
                            <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 text-muted small" id="btnClearTopic">
                                <i class="bi bi-x-circle me-1"></i> Kosongkan (Random AI Mode)
                            </button>
                        </div>
                        <textarea name="topic" id="topicInput" class="form-control rounded-3 py-2" rows="2" placeholder="Contoh: Hukum Nun Mati & Tanwin, Makharijul Huruf, Fiqih Nisa Thaharah, I'rab Jumlah Ismiyyah... (Kosongkan jika ingin AI mencari topik otomatis)"></textarea>
                        <small class="text-muted d-block mt-1">Anda bebas mengetik topik materi apapun, atau klik salah satu inspirasi topik kurikulum di bawah ini:</small>
                        
                        <!-- Dynamic Quick Suggestion Pills Container -->
                        <div class="mt-2" id="suggestionPillsWrapper">
                            <div class="d-flex flex-wrap gap-1 align-items-center" id="suggestionPillsList">
                                <!-- Rendered dynamically by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" id="btnGenerate" class="btn btn-primary-custom w-100 py-3 rounded-3 fw-bold fs-5 shadow-sm">
                        <i class="bi bi-cpu-fill me-2"></i> Generate Paket Soal dengan AI
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
            <h5 class="fw-bold text-success mb-1" id="loadingStageTitle">Al-Hikmah Smart AI Sedang Merumuskan Soal...</h5>
            <p class="text-muted small mb-3" id="loadingStageDesc">Menganalisis parameter materi kurikulum dan merumuskan butir evaluasi...</p>
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
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i class="bi bi-pencil-square me-2 text-success"></i>Tinjau & Sesuaikan Hasil Soal
                    </h5>
                    <p class="text-muted small mb-0">Periksa redaksi pertanyaan, opsi, kunci jawaban, atau rubrik essay sebelum disimpan atau dicetak.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-emerald-badge text-emerald px-3 py-2 rounded-pill fw-semibold" id="readyCountBadge">
                        0 Soal Siap
                    </span>
                    <button type="button" id="btnPrintPreview" class="btn btn-outline-primary rounded-3 px-3 py-2 fw-semibold">
                        <i class="bi bi-printer-fill me-1"></i> Cetak / Unduh PDF
                    </button>
                </div>
            </div>

            <!-- AI Fallback / Quota Notice Banner (Tampil hanya jika API Key bermasalah / kuota habis) -->
            <div id="aiFallbackNotice" class="alert alert-warning border-0 rounded-4 shadow-sm d-flex align-items-start gap-3 p-3 mb-4 d-none">
                <i class="bi bi-exclamation-triangle-fill text-warning fs-4 flex-shrink-0"></i>
                <div class="flex-grow-1">
                    <strong class="d-block text-dark fw-bold mb-1">Catatan Akses AI & Bank Kurikulum:</strong>
                    <p class="small text-secondary mb-1">
                        Paket soal di bawah disajikan menggunakan <strong>Bank Kurikulum Al-Hikmah</strong> karena penyedia AI mengembalikan respon:
                    </p>
                    <div class="bg-white p-2 rounded-3 border border-warning-subtle small font-monospace text-dark mb-2" id="aiFallbackReason">
                        Access to model denied. Please make sure you are eligible for using the model.
                    </div>
                    <div class="small text-muted">
                        💡 <em>Solusi AI Murni:</em> Aktifkan kupon/kuota gratis pada konsol Alibaba Cloud DashScope, atau gunakan <strong>Google Gemini API Key</strong> gratis dari <a href="https://aistudio.google.com/app/apikey" target="_blank" class="fw-bold text-success text-decoration-underline">Google AI Studio</a>.
                    </div>
                </div>
            </div>

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
                        <button type="button" id="btnRegenerate" class="btn btn-outline-secondary rounded-3 px-3 py-2 fw-semibold w-100 w-md-auto">
                            <i class="bi bi-arrow-clockwise me-1"></i> Generate Ulang
                        </button>
                        <button type="button" id="btnPrintPreviewBottom" class="btn btn-outline-primary rounded-3 px-3 py-2 fw-semibold w-100 w-md-auto">
                            <i class="bi bi-printer-fill me-1"></i> Cetak Lembar Soal (PDF)
                        </button>
                        <button type="submit" id="btnSaveBatch" class="btn btn-primary-custom rounded-3 px-4 py-2 fw-bold w-100 w-md-auto">
                            <i class="bi bi-cloud-arrow-up-fill me-2"></i> Simpan ke Bank Soal
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
    .question-card-item.is-essay {
        border-left-color: #3b82f6 !important;
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
    .suggestion-pill {
        cursor: pointer;
        transition: all 0.15s ease-in-out;
    }
    .suggestion-pill:hover {
        background: var(--primary-lighter) !important;
        border-color: var(--primary) !important;
        color: var(--primary) !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 10 Program Topic Inspirations Database
    const programTopicInspirations = {
        "Iqra & Dasar Al-Qur'an": [
            { icon: "🔤", label: "Huruf Hijaiyah Tunggal", topic: "Pengenalan Bentuk Huruf Hijaiyah Tunggal dan Bunyi Lafalnya" },
            { icon: "📏", label: "Tanda Harakat", topic: "Tanda Baca Fathah, Kasrah, dan Dhammah Dasar" },
            { icon: "🔗", label: "Huruf Sambung", topic: "Perubahan Bentuk Huruf Hijaiyah di Awal, Tengah, dan Akhir Kata" },
            { icon: "🔍", label: "Huruf Mirip", topic: "Membedakan Titik dan Makhraj Huruf yang Mirip (Ba, Ta, Tsa, Nun, Ya)" },
            { icon: "✨", label: "Tanwin & Sukun", topic: "Kaidah Bacaan Tanwin (An, In, Un) dan Tanda Sukun (Mati)" }
        ],
        "Tahsin Dasar": [
            { icon: "📖", label: "Nun Sukun & Tanwin", topic: "Hukum Nun Sukun & Tanwin (Izhar, Idgham Bighunnah, Idgham Bilaghunnah, Iqlab, Ikhfa)" },
            { icon: "🗣️", label: "Makharijul Huruf", topic: "Wilayah Makharijul Huruf Pokok (Al-Halq, Al-Lisan, Asy-Syafatain, Al-Jauf)" },
            { icon: "⚡", label: "Sifat Qalqalah", topic: "Kaidah Huruf Qalqalah (Baju Di Toko) serta Tingkatan Sughra & Kubra" },
            { icon: "📏", label: "Mad Thabi'i", topic: "Hukum Mad Ashli (Thabi'i) dan Kadar Panjang Harakat 2 Ketukan" },
            { icon: "🌙", label: "Mim Sukun", topic: "Hukum Mim Sukun (Ikhfa Syafawi, Idgham Mimi, Izhar Syafawi)" }
        ],
        "Adab & Doa Harian": [
            { icon: "🤲", label: "Doa Aktivitas Harian", topic: "Doa Bangun Tidur, Masuk/Keluar Masjid, Makan, dan Doa Kedua Orang Tua" },
            { icon: "👨‍👩‍👧", label: "Birrul Walidain", topic: "Adab Berbakti Kepada Orang Tua dan Guru dalam Islam" },
            { icon: "📖", label: "Adab Tilawah", topic: "Adab Membaca Al-Qur'an (Thaharah, Menghadap Qiblat, Tartil, dan Khusyu')" },
            { icon: "💬", label: "Adab Berbicara", topic: "Adab Berkomunikasi Santun, Menghindari Ghibah, dan Menebar Salam" },
            { icon: "🌟", label: "Dzikir & Doa", topic: "Doa Sapujagat dan Dzikir Pagi Petang Pilihan" }
        ],
        "Tahfidz Al-Qur'an": [
            { icon: "📜", label: "Sambung Ayat Juz 30", topic: "Latihan Sambung Ayat Surah-surah Pendek Juz 30 (An-Naba s.d. An-Nas)" },
            { icon: "🧩", label: "Tertib Surah", topic: "Urutan Nama Surah dan Jumlah Ayat dalam Juz Amma" },
            { icon: "💡", label: "Pesan Pokok Surah", topic: "Asbabun Nuzul dan Pesan Inti Surah Al-Ikhlas, Al-Falaq, An-Nas, dan Al-Ma'un" },
            { icon: "🎯", label: "Mutasyabihat Juz 30", topic: "Mengenal Keserupaan Lafadz Ayat (Mutasyabihat) pada Juz 30" }
        ],
        "Belajar dari Nol (Dewasa)": [
            { icon: "🔤", label: "Makhraj Huruf Dewasa", topic: "Pengenalan Huruf Hijaiyah dan Makhraj Fonetik untuk Pembelajar Dewasa" },
            { icon: "🕌", label: "Bacaan Shalat Fardhu", topic: "Tahsin Bacaan Surah Al-Fatihah, Tasyahhud, dan Doa Shalat Wajib" },
            { icon: "📖", label: "Mengeja Kalimah Al-Qur'an", topic: "Latihan Praktis Mengeja Lafadz Ayat-ayat Pendek Al-Qur'an" },
            { icon: "💧", label: "Panduan Wudhu", topic: "Tata Cara dan Doa Thaharah Wudhu yang Sempurna Sesuai Sunnah" }
        ],
        "Tahsin Dewasa": [
            { icon: "🎯", label: "Sifatul Huruf Lanjutan", topic: "Sifatul Huruf Berlawanan dan Tunggal (Hams/Jahr, Isti'la/Istifal, Istithalah Dhad, Tafkhim/Tarqiq)" },
            { icon: "📐", label: "Mad Far'i Lengkap", topic: "Mad Wajib Muttashil, Mad Jaiz Munfashil, Mad Lazim, dan Mad 'Aridh Lissukun" },
            { icon: "🛑", label: "Waqaf & Ibtida'", topic: "Kaidah Waqaf Lazim, Tam, Kafi, Hasan, Qabih, dan Tata Cara Ibtida'" },
            { icon: "📜", label: "Bacaan Gharib", topic: "Kaidah Bacaan Gharib Riwayat Hafsh (Saktah, Imalah, Isymam, Tashil, Naql)" }
        ],
        "Kelas Muslimah": [
            { icon: "🌸", label: "Fiqih Nisa Thaharah", topic: "Hukum Fiqih Thaharah Wanita: Membedakan Darah Haid, Nifas, dan Istihadhah serta Konsekuensi Ibadah" },
            { icon: "🧕", label: "Kisah Shahabiyah", topic: "Keteladanan Shahabiyah Mulia (Khadijah, Aisyah, Fathimah, Asma binti Abi Bakr)" },
            { icon: "🏡", label: "Keluarga Sakinah", topic: "Tadabbur Ayat-ayat Pembinaan Keluarga Sakinah (Q.S. An-Nisa & An-Nur)" },
            { icon: "🛡️", label: "Adab & Hijab Syar'i", topic: "Kaidah Menjaga Kehormatan, Adab Pergaulan, dan Hijab Sesuai Tuntunan Syari'at" },
            { icon: "🤲", label: "Doa & Dzikir Muslimah", topic: "Dzikir Harian, Doa Perlindungan Keluarga, dan Amalan Wanita Saat Berhalangan Shalat" }
        ],
        "Tahfidz Dewasa": [
            { icon: "👑", label: "Surah Al-Mulk", topic: "Sambung Ayat dan Tadabbur Makna Surah Al-Mulk (Ayat 1-30)" },
            { icon: "📖", label: "Surah Pilihan (Yasin & Kahfi)", topic: "Hafalan dan Pemahaman Surah Yasin, Al-Kahfi, serta As-Sajdah" },
            { icon: "🔄", label: "Metode Murajaah Dewasa", topic: "Metodologi Murajaah Mandiri dan Menjaga Hafalan di Tengah Kesibukan Profesi" },
            { icon: "⚖️", label: "Tajwid Tilawah Hafidz", topic: "Kaidah Tajwid Aplikatif dalam Mempertahankan Kualitas Bacaan Saat Menghafal" }
        ],
        "Bahasa Arab Dasar": [
            { icon: "🗣️", label: "Mufrodat Harian", topic: "Kosakata Benda Sekolah, Rumah, Anggota Tubuh, dan Lingkungan Sekitar" },
            { icon: "🔢", label: "Bilangan Arab", topic: "Mengenal Angka dan Kaidah Bilangan Bahasa Arab (1 s.d. 100)" },
            { icon: "👥", label: "Dhomir (Kata Ganti)", topic: "Penggunaan Kata Ganti Dhomir Munfashil (Huwa, Hiya, Anta, Anti, Ana, Nahnu)" },
            { icon: "👋", label: "Percakapan & Sapaan", topic: "Percakapan Perkenalan (Ta'aruf), Sapaan Harian, dan Ungkapan Sopan Santun" },
            { icon: "👉", label: "Isim Isyarah", topic: "Kaidah Kata Tunjuk Dekat & Jauh (Hadza, Hadzihi, Dzalika, Tilka)" }
        ],
        "Nahwu & Sharaf": [
            { icon: "📚", label: "Pembagian Kata (Kalam)", topic: "Pembagian Kata Bahasa Arab: Tanda-tanda Isim, Fi'il, dan Huruf" },
            { icon: "⚖️", label: "Kaidah I'rab", topic: "Mengenal 4 Macam I'rab (Rafa', Nashab, Jar, Jazm) dan Tanda Asli/Cabangnya" },
            { icon: "🏗️", label: "Struktur Kalimat", topic: "Jumlah Ismiyyah (Mubtada-Khabar) dan Jumlah Fi'liyyah (Fi'il, Fa'il, Maf'ul Bih)" },
            { icon: "🔄", label: "Tashrif Fi'il Tsulatsi", topic: "Wazan Morfologi (Sharaf) Tashrif Istilahi & Lughawi Fi'il Tsulatsi Mujarrad" },
            { icon: "🔗", label: "Idhafah & Mudhaf Ilaih", topic: "Kaidah Penyusunan Frasa Kepemilikan (Idhafah) dan Hukum Majrur" }
        ]
    };

    const aiForm = document.getElementById('aiGenerateForm');
    const programSelect = document.getElementById('programSelect');
    const topicInput = document.getElementById('topicInput');
    const btnClearTopic = document.getElementById('btnClearTopic');
    const suggestionPillsList = document.getElementById('suggestionPillsList');
    const countInput = document.getElementById('countInput');
    const countBadge = document.getElementById('countBadge');
    const btnGenerate = document.getElementById('btnGenerate');

    const skeletonLoader = document.getElementById('skeletonLoader');
    const reviewWorkspace = document.getElementById('reviewWorkspace');
    const questionsContainer = document.getElementById('questionsContainer');
    const readyCountBadge = document.getElementById('readyCountBadge');

    const storeBatchForm = document.getElementById('storeBatchForm');
    const storeProgramId = document.getElementById('storeProgramId');
    const storeTopic = document.getElementById('storeTopic');
    const storeDifficulty = document.getElementById('storeDifficulty');
    const btnRegenerate = document.getElementById('btnRegenerate');
    const btnSaveBatch = document.getElementById('btnSaveBatch');
    const btnPrintPreview = document.getElementById('btnPrintPreview');
    const btnPrintPreviewBottom = document.getElementById('btnPrintPreviewBottom');

    const aiAlertContainer = document.getElementById('aiAlertContainer');
    const aiAlertTitle = document.getElementById('aiAlertTitle');
    const aiAlertMessage = document.getElementById('aiAlertMessage');

    // Current generated questions cache
    let currentGeneratedQuestions = [];

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

    // Update Slider Count Badge
    if (countInput && countBadge) {
        countInput.addEventListener('input', function() {
            countBadge.textContent = this.value + ' Soal';
        });
    }

    // Clear Topic Button (Switch to AI Explorer mode)
    if (btnClearTopic && topicInput) {
        btnClearTopic.addEventListener('click', function() {
            topicInput.value = '';
            topicInput.focus();
        });
    }

    // Render Dynamic Suggestion Pills based on Selected Program
    function renderProgramTopicSuggestions() {
        const selectedOpt = programSelect.options[programSelect.selectedIndex];
        const progName = selectedOpt ? selectedOpt.getAttribute('data-name') || selectedOpt.text.split(' (')[0] : '';
        const list = programTopicInspirations[progName] || [
            { icon: "📖", label: "Tajwid & Makhraj", topic: "Kaidah Tajwid Pokok, Makharijul Huruf, dan Sifat Huruf" },
            { icon: "🕌", label: "Fiqih Ibadah", topic: "Kaidah Thaharah dan Fiqih Shalat Berdasarkan Dalil Shahih" },
            { icon: "📜", label: "Tafsir Ayat Pilihan", topic: "Tadabbur Makna dan Pesan Pokok Surah Pendek Al-Qur'an" },
            { icon: "🗣️", label: "Bahasa Arab", topic: "Mufrodat dan Pola Kalimat Bahasa Arab Dasar" }
        ];

        suggestionPillsList.innerHTML = '<span class="small text-muted me-1"><i class="bi bi-lightbulb text-warning"></i> Inspirasi Topik:</span>';
        
        list.forEach(item => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-light border rounded-pill py-1 px-2 small suggestion-pill';
            btn.setAttribute('data-topic', item.topic);
            btn.innerHTML = `${item.icon} ${item.label}`;
            btn.addEventListener('click', function() {
                topicInput.value = item.topic;
            });
            suggestionPillsList.appendChild(btn);
        });

        // Add "🎲 Topik Bebas / AI Explorer" Pill
        const randomBtn = document.createElement('button');
        randomBtn.type = 'button';
        randomBtn.className = 'btn btn-sm btn-outline-success rounded-pill py-1 px-2 small suggestion-pill fw-semibold';
        randomBtn.innerHTML = '🎲 Topik Bebas / Silabus Acak';
        randomBtn.title = 'Biarkan AI memilihkan materi terbaik secara otomatis';
        randomBtn.addEventListener('click', function() {
            topicInput.value = '';
        });
        suggestionPillsList.appendChild(randomBtn);
    }

    if (programSelect) {
        programSelect.addEventListener('change', renderProgramTopicSuggestions);
        renderProgramTopicSuggestions();
    }

    // AJAX Generate Event
    aiForm.addEventListener('submit', function(e) {
        e.preventDefault();
        hideAlert();
        
        const programId = programSelect.value;
        const topic = topicInput.value.trim();
        const count = countInput.value;
        const difficulty = document.querySelector('input[name="difficulty"]:checked').value;
        const questionType = document.querySelector('input[name="question_type"]:checked').value;

        if (!programId) {
            showAlert('Pilihan Program Wajib', 'Mohon pilih Program Belajar terlebih dahulu.');
            return;
        }

        // Show Skeleton & Disable Button
        btnGenerate.disabled = true;
        btnGenerate.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memproses Paket Soal...';
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
            { t: 0, title: 'Al-Hikmah Smart AI Sedang Memproses...', desc: 'Menganalisis parameter materi dan merumuskan butir evaluasi...' },
            { t: 3, title: 'Menganalisis Topik & Silabus Program...', desc: 'Menyusun butir pertanyaan, studi kasus, kaidah tajwid/fiqih, dan dalil...' },
            { t: 7, title: 'Menyusun Opsi Jawaban & Rubrik Penilaian...', desc: 'Menentukan kunci jawaban akurat dan menyusun rubrik penilaian komprehensif...' },
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
                topic: topic || null,
                count: parseInt(count),
                difficulty: difficulty,
                question_type: questionType
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
            btnGenerate.innerHTML = '<i class="bi bi-cpu-fill me-2"></i> Generate Paket Soal dengan AI';

            if (response.status === 'success' && Array.isArray(response.data) && response.data.length > 0) {
                currentGeneratedQuestions = response.data;

                // Handle Fallback notice banner
                const fallbackNotice = document.getElementById('aiFallbackNotice');
                const reasonEl = document.getElementById('aiFallbackReason');
                if (fallbackNotice) {
                    if (response.is_fallback) {
                        fallbackNotice.classList.remove('d-none');
                        if (reasonEl && response.ai_error) {
                            reasonEl.textContent = response.ai_error;
                        }
                    } else {
                        fallbackNotice.classList.add('d-none');
                    }
                }

                // Populate Store Form Hidden Inputs
                storeProgramId.value = programId;
                storeTopic.value = topic || (response.program_name + ' (Kurikulum Standar)');
                storeDifficulty.value = difficulty;

                renderQuestions(response.data);
                reviewWorkspace.classList.remove('d-none');
                
                // Scroll to Workspace
                reviewWorkspace.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                showAlert('Gagal Menghasilkan Soal', response.message || 'Terjadi kesalahan sistem saat memproses butir soal.');
            }
        })
        .catch(err => {
            stopProgressTimer();
            skeletonLoader.classList.add('d-none');
            btnGenerate.disabled = false;
            btnGenerate.innerHTML = '<i class="bi bi-cpu-fill me-2"></i> Generate Paket Soal dengan AI';
            console.error(err);
            showAlert('Koneksi AI Terputus', err.message || 'Terjadi kendala saat memproses soal. Silakan coba sesaat lagi.');
        });
    });

    // Print Preview Buttons Click Handler (Mencetak persis paket soal di layar)
    function handlePrintAction() {
        const totalCards = document.querySelectorAll('.question-card-item').length;
        if (totalCards === 0) {
            showAlert('Belum Ada Soal', 'Silakan generate soal terlebih dahulu sebelum mencetak lembar soal.');
            return;
        }

        const originalAction = storeBatchForm.action;
        const originalTarget = storeBatchForm.target;
        
        storeBatchForm.action = "{{ route('mentor.questions.print') }}";
        storeBatchForm.target = "_blank";
        storeBatchForm.submit();
        
        // Restore original form action & target for subsequent storage
        setTimeout(() => {
            storeBatchForm.action = originalAction;
            storeBatchForm.target = originalTarget || "_self";
        }, 100);
    }

    if (btnPrintPreview) btnPrintPreview.addEventListener('click', handlePrintAction);
    if (btnPrintPreviewBottom) btnPrintPreviewBottom.addEventListener('click', handlePrintAction);

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

    // Render Question Review Cards (Supporting Multiple Choice & Essay)
    function renderQuestions(items) {
        questionsContainer.innerHTML = '';
        
        items.forEach((q, idx) => {
            const card = document.createElement('div');
            const isEssay = (q.type === 'essay');
            card.className = `card border-0 shadow-sm rounded-4 question-card-item p-4 ${isEssay ? 'is-essay' : ''}`;
            card.setAttribute('data-question-idx', idx);
            card.setAttribute('data-question-type', isEssay ? 'essay' : 'multiple_choice');

            let bodyHtml = '';

            if (isEssay) {
                bodyHtml = `
                    <input type="hidden" name="questions[${idx}][type]" value="essay">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Teks Pertanyaan Soal Essay / Uraian:</label>
                        <textarea name="questions[${idx}][question]" class="form-control rounded-3" rows="2" required>${escapeHtml(q.question)}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-primary"><i class="bi bi-key-fill me-1"></i> Kunci Jawaban / Jawaban Ideal yang Diharapkan:</label>
                        <textarea name="questions[${idx}][essay_answer]" class="form-control rounded-3" rows="3" placeholder="Tuliskan kunci jawaban ideal...">${escapeHtml(q.essay_answer || '')}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary"><i class="bi bi-card-checklist me-1"></i> Pedoman Skor & Rubrik Penilaian:</label>
                        <input type="text" name="questions[${idx}][rubric]" class="form-control rounded-3" value="${escapeHtml(q.rubric || 'Penilaian berdasarkan kelengkapan dalil, argumen, dan ketepatan kaidah.')}">
                    </div>

                    <div>
                        <label class="form-label fw-semibold small text-secondary"><i class="bi bi-lightbulb-fill text-warning me-1"></i> Penjelasan Kaidah & Rujukan:</label>
                        <textarea name="questions[${idx}][explanation]" class="form-control rounded-3" rows="2">${escapeHtml(q.explanation || '')}</textarea>
                    </div>
                `;
            } else {
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

                bodyHtml = `
                    <input type="hidden" name="questions[${idx}][type]" value="multiple_choice">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Teks Pertanyaan Soal Pilihan Ganda:</label>
                        <textarea name="questions[${idx}][question]" class="form-control rounded-3" rows="2" required>${escapeHtml(q.question)}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Pilihan Jawaban (Tandai Radio Button untuk Jawaban Benar):</label>
                        <div class="row g-2 options-row">
                            ${optionsHtml}
                        </div>
                    </div>

                    <div>
                        <label class="form-label fw-semibold small text-secondary"><i class="bi bi-lightbulb-fill text-warning me-1"></i> Penjelasan Kaidah Materi:</label>
                        <textarea name="questions[${idx}][explanation]" class="form-control rounded-3" rows="2">${escapeHtml(q.explanation || '')}</textarea>
                    </div>
                `;
            }

            card.innerHTML = `
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge ${isEssay ? 'bg-primary' : 'bg-success'} rounded-circle p-2 fs-6" style="width:36px; height:36px; display:inline-flex; align-items:center; justify-content:center;">${idx + 1}</span>
                        <span class="badge ${isEssay ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-success-subtle text-success border border-success-subtle'} rounded-pill px-3 py-1 small">
                            ${isEssay ? '✍️ Soal Essay / Uraian' : '📝 Pilihan Ganda'}
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 small">
                            ${escapeHtml(q.difficulty || 'Sedang')}
                        </span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle remove-card-btn" title="Hapus Butir Soal Ini">
                        <i class="bi bi-trash fs-5"></i>
                    </button>
                </div>
                ${bodyHtml}
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
            const numBadge = card.querySelector('.badge.rounded-circle');
            if (numBadge) numBadge.textContent = newIdx + 1;
            
            // Reindex Inputs
            const typeInput = card.querySelector('input[name*="[type]"]');
            if (typeInput) typeInput.name = `questions[${newIdx}][type]`;

            const questionText = card.querySelector('textarea[name*="[question]"]');
            if (questionText) questionText.name = `questions[${newIdx}][question]`;

            const essayAnswerText = card.querySelector('textarea[name*="[essay_answer]"]');
            if (essayAnswerText) essayAnswerText.name = `questions[${newIdx}][essay_answer]`;

            const rubricInput = card.querySelector('input[name*="[rubric]"]');
            if (rubricInput) rubricInput.name = `questions[${newIdx}][rubric]`;

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
        readyCountBadge.textContent = total + ' Soal Siap';
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
</script>
@endpush
