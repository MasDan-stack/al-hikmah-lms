@extends('layouts.parent')

@section('title', 'Tulis Pesan Baru')
@section('header', 'Tulis Pesan Baru')
@section('subheader', 'Kirim pesan langsung ke mentor pembimbing ananda')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0"><i class="bi bi-pencil-square text-primary me-2"></i>Kirim Pesan Ke Mentor</h4>
        <a href="{{ route('parent.messages.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 style-container" style="max-width: 700px;">
        <form action="{{ route('parent.messages.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Pilih Mentor Tujuan *</label>
                <select name="receiver_id" class="form-select" required>
                    <option value="">-- Pilih Mentor Pembimbing --</option>
                    @foreach($mentors as $m)
                        <option value="{{ $m->user?->id }}">{{ $m->user?->name ?? $m->full_name }} ({{ $m->specialization ?? 'Mentor' }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Santri Terkait (Opsional)</label>
                <select name="student_id" class="form-select">
                    <option value="">-- Semua / Umum --</option>
                    @foreach($children as $c)
                        <option value="{{ $c->id }}">{{ $c->user?->name ?? $c->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Isi Pesan / Pertanyaan *</label>
                <textarea name="message" class="form-control" rows="5" placeholder="Tuliskan pertanyaan atau konsultasi Anda mengenai bimbingan Al-Qur'an..." required></textarea>
            </div>

            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-send me-1"></i> Kirim Pesan Now
            </button>
        </form>
    </div>
</div>
@endsection
