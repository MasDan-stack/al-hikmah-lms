@extends('layouts.parent')

@section('title', 'Pesan & Komunikasi')
@section('header', 'Pesan & Komunikasi')
@section('subheader', 'Komunikasi dua arah dengan Mentor bimbingan ananda')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-chat-dots-fill text-primary me-2"></i>Kotak Pesan</h4>
            <p class="text-muted small mb-0">Hubungi mentor pembimbing untuk konsultasi perkembangan Al-Qur'an anak.</p>
        </div>
        <a href="{{ route('parent.messages.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-pencil-square me-1"></i> Tulis Pesan Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        @if($messages->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-chat-left-text fs-1 d-block mb-3 text-secondary"></i>
                <h5 class="fw-bold text-dark">Belum Ada Percakapan</h5>
                <p class="text-muted small">Mulai komunikasi pertama Anda dengan mentor pembimbing ananda.</p>
                <a href="{{ route('parent.messages.create') }}" class="btn btn-primary rounded-pill px-4">
                    Kirim Pesan Pertama
                </a>
            </div>
        @else
            <div class="list-group list-group-flush">
                @foreach($messages as $msg)
                    @php
                        $isOutgoing = ($msg->sender_id === $user->id);
                        $otherUser = $isOutgoing ? $msg->receiver : $msg->sender;
                    @endphp
                    <div class="list-group-item px-0 py-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="fw-bold text-dark fs-6">
                                @if($isOutgoing)
                                    <span class="badge bg-secondary-subtle text-dark me-2">Kepada</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary me-2">Dari</span>
                                @endif
                                {{ $otherUser?->name ?? 'Pengguna' }}
                            </div>
                            <small class="text-muted">{{ $msg->created_at->diffForHumans() }}</small>
                        </div>

                        <p class="text-secondary small mb-2">{{ Str::limit($msg->message, 120) }}</p>

                        @if($msg->student)
                            <small class="text-muted d-block mb-2"><i class="bi bi-person me-1"></i>Santri Terkait: {{ $msg->student?->user?->name ?? $msg->student?->full_name }}</small>
                        @endif

                        @if(! $isOutgoing && ! $msg->is_read)
                            <span class="badge bg-danger rounded-pill">Belum Dibaca</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
