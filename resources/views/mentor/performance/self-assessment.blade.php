@extends('layouts.mentor')

@section('title', 'Refleksi Diri & Evaluasi Bulanan')

@section('content')
<div class="container-fluid py-2">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('mentor.performance.index') }}" class="text-decoration-none">Kinerja Saya</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Evaluasi Diri</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-primary-emphasis d-flex align-items-center gap-2">
                ✍️ Refleksi Diri &amp; Evaluasi Bulanan (Periode {{ $selectedMonth }})
            </h4>
        </div>
        <div>
            <a href="{{ route('mentor.performance.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Alert / Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <form method="POST" action="{{ route('mentor.performance.self-assessment.store') }}">
                    @csrf
                    <input type="hidden" name="period" value="{{ $selectedMonth }}">

                    <div class="mb-4 text-center">
                        <label class="form-label fw-bold d-block mb-1">Penilaian Mandiri Terhadap Performa Mengajar Bulan Ini</label>
                        <small class="text-muted d-block mb-3">Berikan skor objektif antara 1 sampai 100 untuk dedikasi dan kualitas mengajar antum</small>
                        <div class="d-flex justify-content-center align-items-center gap-3">
                            <input type="range" name="self_score" min="50" max="100" value="{{ $assessment->self_score ?? 85 }}" class="form-range w-50" id="scoreRange" oninput="document.getElementById('scoreDisplay').innerText = this.value">
                            <span class="badge bg-primary rounded-pill fs-5 px-3 py-2" id="scoreDisplay">{{ $assessment->self_score ?? 85 }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Capaian &amp; Hal Positif yang Berhasil Diraih Bulan Ini <span class="text-danger">*</span></label>
                        <textarea name="strengths" rows="3" class="form-control rounded-3" placeholder="Contoh: Seluruh santri berhasil menyelesaikan hafalan Juz 30 tepat waktu dan antusiasme belajar meningkat..." required>{{ $assessment->strengths ?? '' }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Tantangan / Hambatan Mengajar yang Dihadapi <span class="text-danger">*</span></label>
                        <textarea name="challenges" rows="3" class="form-control rounded-3" placeholder="Contoh: Beberapa santri sering izin karena kegiatan sekolah formal di hari kerja..." required>{{ $assessment->challenges ?? '' }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small">Rencana Perbaikan / Inisiatif Bulan Depan <span class="text-danger">*</span></label>
                        <textarea name="action_plan" rows="3" class="form-control rounded-3" placeholder="Contoh: Menyiapkan ice-breaking bernuansa islami dan memberikan laporan berkala via WA ke orang tua..." required>{{ $assessment->action_plan ?? '' }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('mentor.performance.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i class="bi bi-save me-1"></i> Simpan Evaluasi Diri
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
