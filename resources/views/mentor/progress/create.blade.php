@extends('layouts.mentor')

@section('title', 'Catat Progres Hafalan')
@section('header', 'Form Catat Progres Hafalan & Evaluasi')
@section('subheader', 'Input capaian surah, ayat, tajwid, dan adab santri')

@section('content')
    <!-- Flash Alert Notification Messages -->
    @if (session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between p-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between p-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 p-3" role="alert">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
                <span class="fw-bold">Gagal Menyimpan Progres! Periksa input berikut:</span>
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-pencil-square me-2 text-success"></i>Formulir Catatan Progres Belajar</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('mentor.progress.store') }}" method="POST">
                        @csrf

                        <div class="row g-3 mb-4">
                            <!-- Santri -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small">Pilih Santri <span class="text-danger">*</span></label>
                                <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Santri --</option>
                                    @foreach($students as $st)
                                        <option value="{{ $st->id }}" {{ (string)old('student_id', $selectedStudentId) === (string)$st->id ? 'selected' : '' }}>
                                            {{ $st->user?->name ?? $st->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Sesi -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small">Pilih Sesi Belajar (Opsional)</label>
                                <select name="session_id" class="form-select @error('session_id') is-invalid @enderror">
                                    <option value="">-- Sesi Belajar Terkait --</option>
                                    @foreach($sessions as $sess)
                                        <option value="{{ $sess->id }}" {{ (string)old('session_id', $selectedSessionId) === (string)$sess->id ? 'selected' : '' }}>
                                            {{ $sess->date ? \Carbon\Carbon::parse($sess->date)->locale('id')->isoFormat('dddd, D MMMM Y') : '' }} - {{ $sess->student?->user?->name ?? $sess->student?->full_name }} ({{ date('H:i', strtotime($sess->time)) }} WIB)
                                        </option>
                                    @endforeach
                                </select>
                                @error('session_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Kategori -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary small">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                                    <option value="Tahfidz" {{ old('kategori', 'Tahfidz') === 'Tahfidz' ? 'selected' : '' }}>Tahfidz (Hafalan)</option>
                                    <option value="Tahsin" {{ old('kategori') === 'Tahsin' ? 'selected' : '' }}>Tahsin (Bacaan)</option>
                                    <option value="Iqra" {{ old('kategori') === 'Iqra' ? 'selected' : '' }}>Iqra & Hijaiyah</option>
                                    <option value="Adab" {{ old('kategori') === 'Adab' ? 'selected' : '' }}>Adab & Doa</option>
                                </select>
                                @error('kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Juz -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary small">Juz Al-Qur'an (1-30)</label>
                                <input type="number" name="juz" class="form-control @error('juz') is-invalid @enderror" min="1" max="30" placeholder="Misal: 30" value="{{ old('juz', 30) }}">
                                @error('juz') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Surah & Ayat -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary small">Surah</label>
                                <input type="text" name="surah_start" class="form-control @error('surah_start') is-invalid @enderror" placeholder="Contoh: An-Naba" value="{{ old('surah_start') }}">
                                @error('surah_start') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-secondary small">Ayat Mulai</label>
                                <input type="text" name="ayat_start" class="form-control @error('ayat_start') is-invalid @enderror" placeholder="1" value="{{ old('ayat_start') }}">
                                @error('ayat_start') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-secondary small">Ayat Selesai</label>
                                <input type="text" name="ayat_end" class="form-control @error('ayat_end') is-invalid @enderror" placeholder="10" value="{{ old('ayat_end') }}">
                                @error('ayat_end') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Nilai -->
                            <div class="col-md-2">
                                <label class="form-label fw-semibold text-secondary small">Nilai Tajwid</label>
                                <input type="number" name="nilai_tajwid" class="form-control @error('nilai_tajwid') is-invalid @enderror" min="0" max="100" placeholder="85" value="{{ old('nilai_tajwid', 85) }}">
                                @error('nilai_tajwid') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold text-secondary small">Kelancaran</label>
                                <input type="number" name="nilai_fluent" class="form-control @error('nilai_fluent') is-invalid @enderror" min="0" max="100" placeholder="90" value="{{ old('nilai_fluent', 90) }}">
                                @error('nilai_fluent') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold text-secondary small">Nilai Adab</label>
                                <select name="nilai_adab" class="form-select @error('nilai_adab') is-invalid @enderror">
                                    <option value="100" {{ (string)old('nilai_adab') === '100' ? 'selected' : '' }}>Mumtaz (100)</option>
                                    <option value="90" {{ (string)old('nilai_adab', '90') === '90' ? 'selected' : '' }}>Sangat Baik (90)</option>
                                    <option value="80" {{ (string)old('nilai_adab') === '80' ? 'selected' : '' }}>Baik (80)</option>
                                    <option value="70" {{ (string)old('nilai_adab') === '70' ? 'selected' : '' }}>Cukup (70)</option>
                                    <option value="60" {{ (string)old('nilai_adab') === '60' ? 'selected' : '' }}>Perlu Bimbingan (60)</option>
                                </select>
                                @error('nilai_adab') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Ujian Mutqin Checkbox -->
                            <div class="col-12">
                                <div class="form-check form-switch p-3 bg-light rounded-3 border">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="is_mutqin_test" value="1" id="is_mutqin_test" {{ old('is_mutqin_test') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-dark small" for="is_mutqin_test">
                                        <i class="bi bi-patch-check-fill text-warning me-1"></i> Tandai sebagai Ujian Kelulusan Mutqin Juz (Menetapkan kelulusan Mutqin pada Juz yang dipilih jika lulus)
                                    </label>
                                </div>
                            </div>

                            <!-- Catatan Evaluasi & Homework -->
                            <div class="col-12">
                                <label class="form-label fw-semibold text-secondary small">Catatan Evaluasi Mentor</label>
                                <textarea name="catatan_evaluasi" class="form-control @error('catatan_evaluasi') is-invalid @enderror" rows="3" placeholder="Contoh: Pengucapan makhraj huruf 'Ain perlu diperbaiki, hafalan lancar.">{{ old('catatan_evaluasi') }}</textarea>
                                @error('catatan_evaluasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-secondary small">Tugas / Murajaah Rumah</label>
                                <input type="text" name="homework" class="form-control @error('homework') is-invalid @enderror" placeholder="Contoh: Murajaah An-Naba ayat 1-20 di rumah." value="{{ old('homework') }}">
                                @error('homework') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('mentor.dashboard') }}" class="btn btn-light rounded-pill px-4 me-2">Batal</a>
                            <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold">
                                <i class="bi bi-save me-2"></i> Simpan Catatan Progres
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
