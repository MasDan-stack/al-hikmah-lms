@extends('layouts.mentor')

@section('title', 'Pesan & Diskusi dengan Orang Tua')
@section('header', 'Pesan & Komunikasi')
@section('subheader', 'Komunikasi langsung dua arah bersama Orang Tua / Wali Santri binaan')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-chat-dots-fill text-primary me-2"></i>Kotak Pesan Guru</h4>
            <p class="text-muted small mb-0">Diskusikan evaluasi tajwid, perkembangan hafalan, dan jadwal belajar bersama orang tua.</p>
        </div>
        <a href="{{ route('mentor.messages.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
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
                <i class="bi bi-chat-left-text fs-1 d-block mb-3 text-secondary opacity-50"></i>
                <h5 class="fw-bold text-dark">Belum Ada Pesan Masuk</h5>
                <p class="text-muted small">Anda dapat memulai pesan konsultasi pertama kepada orang tua santri binaan.</p>
                <a href="{{ route('mentor.messages.create') }}" class="btn btn-primary rounded-pill px-4">
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
                                    <span class="badge bg-secondary-subtle text-secondary me-2">Kepada</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary me-2">Dari</span>
                                @endif
                                {{ $otherUser?->name ?? 'Orang Tua' }}
                                @if(! $isOutgoing && ! $msg->is_read)
                                    <span class="badge bg-danger rounded-pill ms-2 small">Baru</span>
                                @endif
                            </div>
                            <small class="text-muted">{{ $msg->created_at->diffForHumans() }}</small>
                        </div>

                        <p class="text-secondary small mb-2">{{ Str::limit($msg->message, 120) }}</p>

                        @if($msg->student)
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-person me-1"></i>Santri Terkait: <strong>{{ $msg->student?->user?->name ?? $msg->student?->full_name }}</strong>
                            </small>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="text-muted" style="font-size: 0.8rem;">
                                {{ $msg->created_at->translatedFormat('d M Y, H:i') }} WIB
                            </span>
                            @if($otherUser)
                                <a href="{{ route('mentor.messages.chat', $otherUser->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-chat-text-fill me-1"></i> Buka Chat / Balas
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
