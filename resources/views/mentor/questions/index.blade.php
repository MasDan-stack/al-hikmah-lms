@extends('layouts.mentor')

@section('title', 'Bank Soal Evaluasi')

@section('content')
<div class="container-fluid">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="section-badge mb-2"><i class="bi bi-journal-check"></i> Bank Soal Evaluasi</div>
            <h1 class="h3 fw-bold mb-1">Daftar Soal & Kuis <span class="text-gradient">AL-HIKMAH</span></h1>
            <p class="text-muted small mb-0">Kelola bank soal evaluasi Tajwid, Fiqih, Tahsin, dan Bahasa Arab terstandar untuk kuis santri.</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            @if($trashCount > 0)
                <a href="{{ route('mentor.questions.trash') }}" class="btn btn-outline-danger position-relative rounded-3 px-3">
                    <i class="bi bi-trash3 me-1"></i> Tong Sampah
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $trashCount }}
                    </span>
                </a>
            @endif
            <a href="{{ route('mentor.questions.print', request()->all()) }}" target="_blank" class="btn btn-outline-primary rounded-3 px-3 py-2 fw-semibold">
                <i class="bi bi-printer-fill me-1"></i> Cetak Lembar Soal (PDF)
            </a>
            <a href="{{ route('mentor.questions.generate') }}" class="btn btn-primary-custom rounded-3 px-4 py-2 fw-semibold">
                <i class="bi bi-cpu-fill me-2"></i> Generate Soal Baru (AI)
            </a>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--card-bg);">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('mentor.questions.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-secondary small" for="programSelect">Program Belajar</label>
                    <select name="program_id" id="programSelect" class="form-select rounded-3">
                        <option value="">-- Semua Program (10 Program) --</option>
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}" {{ request('program_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-secondary small" for="typeSelect">Tipe Soal</label>
                    <select name="type" id="typeSelect" class="form-select rounded-3">
                        <option value="">-- Semua Tipe --</option>
                        <option value="multiple_choice" {{ request('type') === 'multiple_choice' ? 'selected' : '' }}>📝 Pilihan Ganda</option>
                        <option value="essay" {{ request('type') === 'essay' ? 'selected' : '' }}>✍️ Essay / Uraian</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-secondary small" for="diffSelect">Tingkat</label>
                    <select name="difficulty" id="diffSelect" class="form-select rounded-3">
                        <option value="">-- Semua Tingkat --</option>
                        <option value="Mudah" {{ request('difficulty') === 'Mudah' ? 'selected' : '' }}>🟢 Mudah</option>
                        <option value="Sedang" {{ request('difficulty') === 'Sedang' ? 'selected' : '' }}>🟡 Sedang</option>
                        <option value="Sulit" {{ request('difficulty') === 'Sulit' ? 'selected' : '' }}>🔴 Sulit (HOTS)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-secondary small" for="topicInput">Cari Topik Materi</label>
                    <input type="text" name="topic" id="topicInput" class="form-control rounded-3" placeholder="Contoh: Nun Mati, Makharijul Huruf, Haid..." value="{{ request('topic') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom w-100 rounded-3"><i class="bi bi-search me-1"></i> Filter</button>
                    <a href="{{ route('mentor.questions.index') }}" class="btn btn-outline-secondary rounded-3" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Data Table Card -->
    <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg);">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tableBankSoal">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Program</th>
                            <th>Tipe & Tingkat</th>
                            <th>Topik Materi</th>
                            <th style="min-width: 250px;">Pertanyaan Soal</th>
                            <th>Kunci Jawaban</th>
                            <th class="text-center no-sort" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $index => $q)
                            <tr>
                                <td>{{ $questions->firstItem() + $index }}</td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-semibold">
                                        <i class="bi bi-journal-bookmark me-1"></i> {{ $q->program->name ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($q->isEssay())
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 small">
                                                <i class="bi bi-pencil-square me-1"></i> Essay
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark border rounded-pill px-2 py-1 small">
                                                <i class="bi bi-ui-radios me-1 text-secondary"></i> Pilgan (A-D)
                                            </span>
                                        @endif

                                        @if($q->difficulty === 'Mudah')
                                            <span class="badge bg-success text-white rounded-pill px-2 py-0" style="font-size: 0.75rem;">🟢 Mudah</span>
                                        @elseif($q->difficulty === 'Sedang')
                                            <span class="badge bg-warning text-dark rounded-pill px-2 py-0" style="font-size: 0.75rem;">🟡 Sedang</span>
                                        @else
                                            <span class="badge bg-danger text-white rounded-pill px-2 py-0" style="font-size: 0.75rem;">🔴 Sulit (HOTS)</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark d-block">{{ $q->topic }}</span>
                                    @if($q->created_by_ai)
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-0 small"><i class="bi bi-stars"></i> AI Generated</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 320px;" title="{{ $q->question }}">
                                        {{ $q->question }}
                                    </div>
                                </td>
                                <td>
                                    @if($q->isEssay())
                                        <span class="badge bg-primary-subtle text-primary fw-bold me-1">Jawaban Uraian</span>
                                        <small class="text-muted text-truncate d-inline-block align-middle" style="max-width: 140px;">
                                            {{ $q->essay_answer ?: 'Terlampir' }}
                                        </small>
                                    @else
                                        <span class="badge bg-emerald-badge text-emerald fw-bold me-1">{{ $q->correct_option_label }}</span>
                                        <small class="text-muted text-truncate d-inline-block align-middle" style="max-width: 140px;">
                                            {{ $q->correct_option_text }}
                                        </small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-circle" data-bs-toggle="modal" data-bs-target="#previewModal{{ $q->id }}" title="Preview Soal">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <form method="POST" action="{{ route('mentor.questions.destroy', $q->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin memindahkan soal ini ke Tong Sampah?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Hapus ke Tong Sampah">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Preview Modal -->
                                    <div class="modal fade text-start" id="previewModal{{ $q->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content modal-premium border-0 shadow-lg rounded-4">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title fw-bold text-success"><i class="bi bi-card-text me-2"></i>Detail Butir Soal Evaluasi</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                                        <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill fw-semibold">{{ $q->program->name ?? '-' }}</span>
                                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill">{{ $q->topic }}</span>
                                                        <span class="badge bg-info-subtle text-info px-3 py-1 rounded-pill">{{ $q->type_label }}</span>
                                                        <span class="badge bg-light text-dark border px-3 py-1 rounded-pill">Tingkat: {{ $q->difficulty }}</span>
                                                    </div>

                                                    <div class="p-3 rounded-3 mb-3" style="background: var(--bg-primary);">
                                                        <h6 class="fw-bold text-secondary small text-uppercase mb-2">Pertanyaan Soal:</h6>
                                                        <p class="fs-5 fw-semibold text-dark mb-0">{!! nl2br(e($q->question)) !!}</p>
                                                    </div>

                                                    @if($q->isEssay())
                                                        <div class="p-3 rounded-3 border border-primary-subtle bg-primary-subtle text-dark mb-3">
                                                            <h6 class="fw-bold text-primary mb-1"><i class="bi bi-key-fill me-1"></i> Kunci Jawaban / Jawaban Ideal:</h6>
                                                            <p class="mb-0 small text-secondary-emphasis">{!! nl2br(e($q->essay_answer ?: 'Jawaban ideal terlampir.')) !!}</p>
                                                        </div>

                                                        @if($q->rubric)
                                                            <div class="p-3 rounded-3 border bg-light mb-3">
                                                                <h6 class="fw-bold text-secondary mb-1"><i class="bi bi-card-checklist me-1"></i> Pedoman Skor & Rubrik Penilaian:</h6>
                                                                <p class="mb-0 small text-muted">{{ $q->rubric }}</p>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <h6 class="fw-bold text-secondary small text-uppercase mb-2">Pilihan Jawaban:</h6>
                                                        <div class="row g-2 mb-3">
                                                            @foreach($q->options ?? [] as $optIdx => $optText)
                                                                <div class="col-md-6">
                                                                    <div class="p-3 rounded-3 border d-flex align-items-center gap-3 {{ $optIdx == $q->correct_answer ? 'border-success bg-success-subtle' : 'bg-light' }}">
                                                                        <span class="badge {{ $optIdx == $q->correct_answer ? 'bg-success text-white' : 'bg-secondary text-white' }} rounded-circle px-2 py-1">
                                                                            {{ chr(65 + $optIdx) }}
                                                                        </span>
                                                                        <span class="fw-medium text-dark">{{ $optText }}</span>
                                                                        @if($optIdx == $q->correct_answer)
                                                                            <i class="bi bi-check-circle-fill text-success ms-auto fs-5"></i>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    @if($q->explanation)
                                                        <div class="p-3 rounded-3 border border-warning-subtle bg-warning-subtle text-dark">
                                                            <h6 class="fw-bold text-warning-emphasis mb-1"><i class="bi bi-lightbulb-fill text-warning me-1"></i> Penjelasan Kaidah Materi:</h6>
                                                            <p class="mb-0 small text-secondary-emphasis">{{ $q->explanation }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                    <h6 class="fw-bold text-secondary mb-1">Belum Ada Soal Terdaftar</h6>
                                    <p class="text-muted small mb-3">Klik tombol di bawah untuk membuat paket soal otomatis menggunakan AI.</p>
                                    <a href="{{ route('mentor.questions.generate') }}" class="btn btn-primary-custom btn-sm rounded-3 px-4">
                                        <i class="bi bi-cpu-fill me-1"></i> Generate Soal Sekarang
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="mt-4 d-flex justify-content-end">
                {{ $questions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
