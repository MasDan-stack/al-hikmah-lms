@extends('layouts.parent')

@section('title', 'Chat dengan ' . ($mentorUser?->name ?? 'Mentor'))
@section('header', 'Ruang Chat & Diskusi')
@section('subheader', 'Percakapan langsung bersama Pembimbing Ustaz/Ustazah')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle p-2 bg-primary-subtle text-primary fs-4">
                <i class="bi bi-person-fill"></i>
            </div>
            <div>
                <h5 class="fw-bold text-dark mb-0">{{ $mentorUser?->name ?? 'Mentor Pembimbing' }}</h5>
                <small class="text-success"><i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>Online / Siap Diskusi</small>
            </div>
        </div>
        <a href="{{ route('parent.messages.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Inbox
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="chat-box p-3 bg-light rounded-3 mb-3" style="min-height: 350px; max-height: 500px; overflow-y: auto;">
            @if($chatMessages->isEmpty())
                <div class="text-center py-5 text-muted">
                    Belum ada percakapan sebelumnya. Tuliskan pesan pertama Anda di bawah ini!
                </div>
            @else
                @foreach($chatMessages as $chat)
                    @php $isMe = ($chat->sender_id === auth()->id()); @endphp
                    <div class="d-flex mb-3 {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
                        <div class="p-3 rounded-4 shadow-sm {{ $isMe ? 'bg-primary text-white' : 'bg-white text-dark border' }}" style="max-width: 75%;">
                            <div class="small fw-semibold mb-1 {{ $isMe ? 'text-white-50' : 'text-muted' }}">
                                {{ $isMe ? 'Saya' : ($mentorUser?->name ?? 'Mentor') }} • {{ $chat->created_at->format('H:i') }}
                            </div>
                            <div>{{ $chat->message }}</div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <form action="{{ route('parent.messages.store') }}" method="POST">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $mentorUser?->id }}">
            <div class="input-group">
                <input type="text" name="message" class="form-control rounded-start-pill py-2 px-4" placeholder="Ketik pesan Anda..." required>
                <button type="submit" class="btn btn-primary rounded-end-pill px-4">
                    <i class="bi bi-send-fill me-1"></i> Kirim
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
