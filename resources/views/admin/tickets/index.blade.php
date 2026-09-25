@extends('layouts.admin')

@section('title', 'Tiket Intervensi Komplain Wali Santri')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold"><i class="bi bi-ticket-detailed-fill text-danger me-2"></i>Tiket Intervensi Komplain Wali Santri</h1>
            <p class="text-muted small mb-0">Mitigasi keluhan wali santri secara cepat dan preskriptif oleh Koordinator Pengajar sebelum terjadi mutasi santri.</p>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show shadow-sm rounded-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Status Tabs Filter -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-2">
            <ul class="nav nav-pills gap-1 flex-nowrap overflow-auto" style="padding-bottom: 2px;">
                <li class="nav-item">
                    <a class="nav-link rounded-pill py-2 px-3 {{ $statusFilter === 'open' ? 'active bg-danger text-white fw-bold' : 'text-dark' }}" 
                       href="{{ route('admin.tickets.index', ['status' => 'open']) }}">
                        <i class="bi bi-exclamation-circle-fill me-1"></i> Terbuka / Butuh Tindakan
                        <span class="badge {{ $statusFilter === 'open' ? 'bg-white text-danger' : 'bg-danger text-white' }} rounded-pill ms-1">{{ $statusCounts['open'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill py-2 px-3 {{ $statusFilter === 'in_progress' ? 'active bg-warning text-dark fw-bold' : 'text-dark' }}" 
                       href="{{ route('admin.tickets.index', ['status' => 'in_progress']) }}">
                        <i class="bi bi-arrow-repeat me-1"></i> Sedang Ditangani
                        <span class="badge {{ $statusFilter === 'in_progress' ? 'bg-white text-dark' : 'bg-warning text-dark' }} rounded-pill ms-1">{{ $statusCounts['in_progress'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill py-2 px-3 {{ $statusFilter === 'resolved' ? 'active bg-success text-white fw-bold' : 'text-dark' }}" 
                       href="{{ route('admin.tickets.index', ['status' => 'resolved']) }}">
                        <i class="bi bi-check-circle-fill me-1"></i> Selesai / Tuntas
                        <span class="badge {{ $statusFilter === 'resolved' ? 'bg-white text-success' : 'bg-success text-white' }} rounded-pill ms-1">{{ $statusCounts['resolved'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill py-2 px-3 {{ $statusFilter === 'escalated_to_mutation' ? 'active bg-dark text-white fw-bold' : 'text-dark' }}" 
                       href="{{ route('admin.tickets.index', ['status' => 'escalated_to_mutation']) }}">
                        <i class="bi bi-arrow-left-right me-1"></i> Eskalasi Mutasi
                        <span class="badge {{ $statusFilter === 'escalated_to_mutation' ? 'bg-white text-dark' : 'bg-secondary text-white' }} rounded-pill ms-1">{{ $statusCounts['escalated_to_mutation'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill py-2 px-3 {{ $statusFilter === 'all' ? 'active bg-primary text-white fw-bold' : 'text-dark' }}" 
                       href="{{ route('admin.tickets.index', ['status' => 'all']) }}">
                        <i class="bi bi-collection me-1"></i> Semua Tiket
                        <span class="badge {{ $statusFilter === 'all' ? 'bg-white text-primary' : 'bg-light text-dark' }} rounded-pill ms-1">{{ $statusCounts['all'] }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tickets Table Card -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">No. Tiket</th>
                            <th>Wali Santri & Santri</th>
                            <th>Guru Pembimbing</th>
                            <th>Kategori & Rating</th>
                            <th>Komplain & Kata Kunci</th>
                            <th>Status & Penangan</th>
                            <th class="pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">#{{ $ticket->ticket_number }}</div>
                                    <small class="text-muted">{{ $ticket->created_at->translatedFormat('d M Y, H:i') }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $ticket->student?->getDisplayName() ?? 'Santri' }}</div>
                                    <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $ticket->parent?->name ?? 'Wali Santri' }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $ticket->mentor?->getDisplayName() ?? 'Mentor' }}</div>
                                    <small class="text-muted">{{ $ticket->mentor?->specialization ?? 'Spesialis Tahsin' }}</small>
                                </td>
                                <td>
                                    <div>
                                        <span class="badge {{ $ticket->getSeverityBadgeClass() }} rounded-pill px-2">
                                            {{ $ticket->getCategoryLabel() }}
                                        </span>
                                    </div>
                                    <small class="text-warning fw-bold mt-1 d-inline-block">
                                        ⭐ {{ $ticket->feedback?->overall_rating ?? '-' }}/5 Bintang
                                    </small>
                                </td>
                                <td style="max-width: 260px;">
                                    <div class="text-truncate small text-dark fw-medium" title="{{ $ticket->parent_comment }}">
                                        "{{ $ticket->parent_comment ? \Illuminate\Support\Str::limit($ticket->parent_comment, 70) : 'Tanpa catatan ulasan tertulis' }}"
                                    </div>
                                    @if(!empty($ticket->detected_keywords))
                                        <div class="mt-1 d-flex gap-1 flex-wrap">
                                            @foreach($ticket->detected_keywords as $kw)
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill" style="font-size: 0.7rem;">
                                                    #{{ $kw }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $ticket->getStatusBadgeClass() }} rounded-pill px-2 py-1">
                                        {{ strtoupper(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                    @if($ticket->handler)
                                        <div class="small text-muted mt-1"><i class="bi bi-person-check me-1"></i>{{ $ticket->handler->name }}</div>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                        <i class="bi bi-arrow-right-circle me-1"></i> Tangani Tiket
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-ticket-detailed fs-1 d-block mb-3 text-muted opacity-50"></i>
                                    <h6 class="fw-bold text-dark">Tidak Ada Tiket Intervensi pada Kategori Ini</h6>
                                    <p class="small text-muted mb-0">Alhamdulillah, seluruh sesi pengajaran berjalan kondusif tanpa komplain aktif.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($tickets->hasPages())
                <div class="p-3 border-top">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
