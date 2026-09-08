@extends('layouts.mentor')

@section('title', 'Chat dengan ' . ($parentUser?->name ?? 'Orang Tua'))
@section('header', 'Ruang Chat & Diskusi')
@section('subheader', 'Percakapan bimbingan bersama Orang Tua / Wali Santri')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle p-2 bg-success-subtle text-success fs-4">
                <i class="bi bi-person-fill"></i>
            </div>
            <div>
                <h5 class="fw-bold text-dark mb-0">{{ $parentUser?->name ?? 'Orang Tua Murid' }}</h5>
                <small class="text-muted">
                    <i class="bi bi-telephone me-1"></i>{{ $parentUser?->phone ?? '-' }} • 
                    <span class="text-success"><i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>Terhubung</span>
                </small>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if($parentUser?->phone)
                @php
                    $cleanPhone = preg_replace('/[^0-9]/', '', $parentUser->phone);
                    if (str_starts_with($cleanPhone, '0')) { $cleanPhone = '62' . substr($cleanPhone, 1); }
                @endphp
                <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="btn btn-outline-success rounded-pill px-3" title="Chat via WhatsApp">
                    <i class="bi bi-whatsapp me-1"></i> Buka WhatsApp
                </a>
            @endif
            <a href="{{ route('mentor.messages.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Inbox
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="chat-box p-3 bg-light rounded-3 mb-3" style="min-height: 380px; max-height: 520px; overflow-y: auto;">
            @if($chatMessages->isEmpty())
                <div class="text-center py-5 text-muted">
                    Belum ada riwayat percakapan sebelumnya. Tuliskan pesan konsultasi pertama Anda di bawah ini!
                </div>
            @else
                @foreach($chatMessages as $chat)
                    @php $isMe = ($chat->sender_id === auth()->id()); @endphp
                    <div class="d-flex mb-3 {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
                        <div class="p-3 rounded-4 shadow-sm {{ $isMe ? 'bg-primary text-white' : 'bg-white text-dark border' }}" style="max-width: 75%;">
                            <div class="small fw-semibold mb-1 {{ $isMe ? 'text-white-50' : 'text-muted' }}">
                                {{ $isMe ? 'Saya (Guru)' : ($parentUser?->name ?? 'Orang Tua') }} • {{ $chat->created_at->format('H:i') }}
                            </div>
                            <div class="lh-base">{{ $chat->message }}</div>
                            @if($chat->student)
                                <div class="mt-1 pt-1 border-top {{ $isMe ? 'border-white-50 text-white-50' : 'border-light text-muted' }}" style="font-size: 0.75rem;">
                                    <i class="bi bi-person me-1"></i>Santri: {{ $chat->student?->user?->name ?? $chat->student?->full_name }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <form action="{{ route('mentor.messages.store') }}" method="POST">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $parentUser?->id }}">
            <div class="input-group">
                <input type="text" name="message" class="form-control rounded-start-pill py-2 px-4" placeholder="Ketik pesan balasan Anda kepada Orang Tua..." required autocomplete="off">
                <button type="submit" class="btn btn-primary rounded-end-pill px-4 fw-bold">
                    <i class="bi bi-send-fill me-1"></i> Kirim Pesan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
