@extends('layouts.mentor')

@section('title', 'Tulis Pesan Baru ke Orang Tua')
@section('header', 'Tulis Pesan Baru')
@section('subheader', 'Kirim pesan konsultasi bimbingan kepada Orang Tua / Wali Santri')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-pencil-square text-primary me-2"></i>Formulir Pesan Baru</h4>
            <p class="text-muted small mb-0">Pilih orang tua murid dan sampaikan pesan bimbingan belajar.</p>
        </div>
        <a href="{{ route('mentor.messages.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Inbox
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-md-5">
                @if($parents->isEmpty())
                    <div class="text-center py-4">
                        <i class="bi bi-exclamation-circle fs-1 text-warning d-block mb-3"></i>
                        <h5 class="fw-bold">Belum Ada Orang Tua Terhubung</h5>
                        <p class="text-muted small">Anda belum memiliki santri binaan aktif yang terhubung dengan akun orang tua.</p>
                        <a href="{{ route('mentor.students.index') }}" class="btn btn-primary rounded-pill px-4">Lihat Santri Binaan</a>
                    </div>
                @else
                    <form action="{{ route('mentor.messages.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Pilih Orang Tua / Wali Santri <span class="text-danger">*</span></label>
                            <select name="receiver_id" class="form-select @error('receiver_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Orang Tua Tujuan --</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}">
                                        {{ $parent->name }} ({{ $parent->phone ?? $parent->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('receiver_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Santri Terkait (Opsional)</label>
                            <select name="student_id" class="form-select @error('student_id') is-invalid @enderror">
                                <option value="">-- Tidak Terkait Santri Tertentu --</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">
                                        {{ $student->user?->name ?? $student->full_name }} (Wali: {{ $student->parent?->user?->name ?? 'Orang Tua' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Isi Pesan Konsultasi <span class="text-danger">*</span></label>
                            <textarea name="message" rows="5" class="form-control @error('message') is-invalid @enderror" 
                                      placeholder="Tuliskan pesan Anda kepada orang tua (misal: perkembangan tajwid ananda, evaluasi hafalan surah, atau koordinasi waktu bimbingan)..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('mentor.messages.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                                <i class="bi bi-send-fill me-2"></i> Kirim Pesan
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
