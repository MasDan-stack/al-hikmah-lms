@extends('layouts.mentor')

@section('title', 'Catat Progres Massal')
@section('header', 'Catat Progres Massal')
@section('subheader', 'Input pencatatan hafalan dan evaluasi untuk beberapa santri sekaligus')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-layers-fill text-success me-2"></i>Form Progres Massal (Multi-Student)</h4>
            <p class="text-muted small mb-0">Hemat waktu dengan mencatat progres santri dalam satu kali submit.</p>
        </div>
        <div>
            <a href="{{ route('mentor.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 mb-4 shadow-sm">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan pengisian:</h6>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('mentor.progress.bulk-store') }}" method="POST">
        @csrf

        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-card-checklist me-2 text-primary"></i>Daftar Entry Progres Santri</h5>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" id="addRowBtn">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Baris Santri
                </button>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="bulkTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 200px;">Santri Binaan *</th>
                                <th style="width: 150px;">Sesi Belajar</th>
                                <th style="width: 140px;">Kategori *</th>
                                <th style="width: 130px;">Surah / Juz</th>
                                <th style="width: 120px;">Ayat (Mulai-Selesai)</th>
                                <th style="width: 100px;">Nilai (Fluent/Tajwid)</th>
                                <th style="width: 100px;">Adab</th>
                                <th>Catatan / PR</th>
                                <th style="width: 50px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="bulkTableBody">
                            <!-- Row 0 -->
                            <tr class="bulk-row">
                                <td>
                                    <select name="entries[0][student_id]" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih Santri --</option>
                                        @foreach($students as $st)
                                            <option value="{{ $st->id }}">{{ $st->user?->name ?? $st->full_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="entries[0][session_id]" class="form-select form-select-sm">
                                        <option value="">-- Tanpa Sesi --</option>
                                        @foreach($sessions as $ses)
                                            <option value="{{ $ses->id }}">{{ $ses->date->format('d M') }} ({{ $ses->time }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="entries[0][kategori]" class="form-select form-select-sm" required>
                                        <option value="Hafalan Baru">Hafalan Baru</option>
                                        <option value="Muraja'ah">Muraja'ah</option>
                                        <option value="Tajwid & Bacaan">Tajwid & Bacaan</option>
                                        <option value="Adab & Akhlak">Adab & Akhlak</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="entries[0][surah_start]" class="form-select-sm form-control form-control-sm mb-1" placeholder="Nama Surah">
                                    <input type="number" name="entries[0][juz]" class="form-control form-control-sm" placeholder="Juz (1-30)" min="1" max="30">
                                </td>
                                <td>
                                    <input type="text" name="entries[0][ayat_start]" class="form-control form-control-sm mb-1" placeholder="Ayat Mulai">
                                    <input type="text" name="entries[0][ayat_end]" class="form-control form-control-sm" placeholder="Ayat Selesai">
                                </td>
                                <td>
                                    <input type="number" name="entries[0][nilai_fluent]" class="form-control form-control-sm mb-1" placeholder="Fluent (0-100)" min="0" max="100">
                                    <input type="number" name="entries[0][nilai_tajwid]" class="form-control form-control-sm" placeholder="Tajwid (0-100)" min="0" max="100">
                                </td>
                                <td>
                                    <select name="entries[0][nilai_adab]" class="form-select form-select-sm">
                                        <option value="90">Sangat Baik (90)</option>
                                        <option value="80" selected>Baik (80)</option>
                                        <option value="70">Cukup (70)</option>
                                        <option value="60">Perlu Bimbingan (60)</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="entries[0][catatan_evaluasi]" class="form-control form-control-sm mb-1" rows="1" placeholder="Catatan evaluasi..."></textarea>
                                    <input type="text" name="entries[0][homework]" class="form-control form-control-sm" placeholder="Tugas rumah / PR">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger removeRowBtn" disabled>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Pastikan seluruh santri yang dipilih sudah benar sebelum menyimpan.</small>
                <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm">
                    <i class="bi bi-check-all me-2"></i>Simpan Semua Progres Massal
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let rowIdx = 1;
        const tbody = document.getElementById('bulkTableBody');
        const addBtn = document.getElementById('addRowBtn');

        function updateRemoveButtons() {
            const rows = tbody.querySelectorAll('.bulk-row');
            rows.forEach((row) => {
                const btn = row.querySelector('.removeRowBtn');
                if (rows.length === 1) {
                    btn.disabled = true;
                } else {
                    btn.disabled = false;
                }
            });
        }

        addBtn.addEventListener('click', function () {
            const firstRow = tbody.querySelector('.bulk-row');
            const newRow = firstRow.cloneNode(true);

            // Update indices
            newRow.querySelectorAll('input, select, textarea').forEach((input) => {
                const name = input.getAttribute('name');
                if (name) {
                    const newName = name.replace(/\[\d+\]/, '[' + rowIdx + ']');
                    input.setAttribute('name', newName);
                    if (input.tagName === 'INPUT' || input.tagName === 'TEXTAREA') {
                        input.value = '';
                    }
                }
            });

            tbody.appendChild(newRow);
            rowIdx++;
            updateRemoveButtons();
        });

        tbody.addEventListener('click', function (e) {
            if (e.target.closest('.removeRowBtn')) {
                const row = e.target.closest('.bulk-row');
                const rows = tbody.querySelectorAll('.bulk-row');
                if (rows.length > 1) {
                    row.remove();
                    updateRemoveButtons();
                }
            }
        });

        updateRemoveButtons();
    });
</script>
@endpush
