@extends('layouts.mentor')

@section('title', 'Tetapkan Target Hafalan Santri')
@section('header', 'Tetapkan Target Baru')
@section('subheader', 'Buat target setoran untuk santri individu atau penugasan massal sekaligus')

@section('content')
<div class="container-fluid p-0">
    <div class="row g-4">
        <!-- Single Target Assignment -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-3"><i class="bi bi-person-fill text-success me-2"></i>Penugasan Santri Tunggal</h5>
                <form action="{{ route('mentor.targets.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pilih Santri Binaan</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">-- Pilih Santri --</option>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">{{ $st->getDisplayName() }} ({{ $st->user?->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Sesi Belajar Terkait (Opsional)</label>
                        <select name="learning_session_id" class="form-select">
                            <option value="">-- Tanpa Sesi Tertentu --</option>
                            @foreach($sessions as $sess)
                                <option value="{{ $sess->id }}">{{ $sess->date->format('d/m/Y') }} &bull; {{ $sess->start_time }} - {{ $sess->end_time }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tanggal Target</label>
                        <input type="date" name="target_date" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Surat</label>
                        <input type="text" name="surah_name" class="form-control" placeholder="Contoh: QS. Al-Mulk" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Mulai Ayat</label>
                            <input type="number" name="start_ayat" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Sampai Ayat</label>
                            <input type="number" name="end_ayat" class="form-control" value="10" min="1" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Catatan / Arahan Ustadz</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Perhatikan hukum ikhfa dan mad thabi'i..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-semibold">
                        <i class="bi bi-send-fill me-1"></i> Tetapkan Target Santri
                    </button>
                </form>
            </div>
        </div>

        <!-- Bulk Target Assignment -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-3"><i class="bi bi-people-fill text-primary me-2"></i>Penugasan Massal (Bulk Assign)</h5>
                <form action="{{ route('mentor.targets.bulk-assign') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pilih Banyak Santri (Centang)</label>
                        <div class="border rounded-3 p-3 bg-light-subtle" style="max-height: 180px; overflow-y: auto;">
                            @foreach($students as $st)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="student_ids[]" value="{{ $st->id }}" id="st_bulk_{{ $st->id }}">
                                    <label class="form-check-label small" for="st_bulk_{{ $st->id }}">
                                        <strong>{{ $st->getDisplayName() }}</strong> ({{ $st->age }} Th)
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tanggal Target</label>
                        <input type="date" name="target_date" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Surat</label>
                        <input type="text" name="surah_name" class="form-control" placeholder="Contoh: QS. An-Naba" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Mulai Ayat</label>
                            <input type="number" name="start_ayat" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Sampai Ayat</label>
                            <input type="number" name="end_ayat" class="form-control" value="20" min="1" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Catatan Arahan Massal</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Target pekanan halaqah..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold">
                        <i class="bi bi-people-fill me-1"></i> Tetapkan untuk Semua yang Dipilih
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
