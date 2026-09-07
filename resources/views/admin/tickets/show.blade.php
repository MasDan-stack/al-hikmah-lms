@extends('layouts.admin')

@section('title', 'Detail Tiket Intervensi #' . $ticket->ticket_number)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-outline-secondary rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h1 class="h3 mb-0 text-gray-800 fw-bold">Tiket Intervensi #{{ $ticket->ticket_number }}</h1>
                <span class="badge {{ $ticket->getStatusBadgeClass() }} rounded-pill px-3 py-2 ms-2">
                    {{ strtoupper(str_replace('_', ' ', $ticket->status)) }}
                </span>
                <span class="badge {{ $ticket->getSeverityBadgeClass() }} rounded-pill px-3 py-2">
                    URGENSI: {{ strtoupper($ticket->severity) }}
                </span>
            </div>
            <p class="text-muted small mb-0 ms-4 ps-2">Dibuat otomatis oleh Sistem Deteksi Komplain Sentimen pada {{ $ticket->created_at->translatedFormat('d F Y, H:i') }} WIB.</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm">
                <i class="bi bi-list-ul me-1"></i> Kembali ke Daftar Tiket
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Kolom Kiri: Rincian Komplain & Pihak Terkait -->
        <div class="col-lg-7">
            <!-- Kartu Keluhan Utama -->
            <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-danger bg-opacity-10 py-3 border-0">
                    <h6 class="m-0 fw-bold text-danger d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-chat-quote-fill me-2"></i>Ulasan & Rincian Keluhan Wali Santri</span>
                        <span class="badge bg-danger rounded-pill px-3">Kategori: {{ $ticket->getCategoryLabel() }}</span>
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-4 bg-light mb-3">
                        <div>
                            <small class="text-muted d-block">Rating Keseluruhan Sesi:</small>
                            <span class="fs-4 text-warning fw-bold">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= ($ticket->feedback?->overall_rating ?? 0) ? '-fill' : '' }}"></i>
                                @endfor
                                <span class="fs-6 text-dark ms-2">({{ $ticket->feedback?->overall_rating ?? 0 }}/5)</span>
                            </span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Tanggal Sesi:</small>
                            <strong class="text-dark">{{ $ticket->session ? \Carbon\Carbon::parse($ticket->session->date)->translatedFormat('d M Y') : 'Sesi Terkait' }}</strong>
                            <small class="text-muted d-block">{{ $ticket->session?->time ?? '' }} WIB</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold text-uppercase">Ulasan / Catatan Lengkap Orang Tua:</label>
                        <div class="p-3 bg-light rounded-4 border-start border-4 border-danger">
                            <p class="mb-0 text-dark fst-italic fs-6">
                                "{{ $ticket->parent_comment ?: ($ticket->feedback?->comment ?: 'Tidak ada ulasan tertulis (Komplain dipicu rating rendah).') }}"
                            </p>
                        </div>
                    </div>

                    @if(!empty($ticket->detected_keywords))
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Kata Kunci Keluhan Terdeteksi Sistem:</label>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach($ticket->detected_keywords as $kw)
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2">
                                        <i class="bi bi-tag-fill me-1"></i> #{{ $kw }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($ticket->feedback && $ticket->feedback->ratings->isNotEmpty())
                        <div class="mt-4 pt-3 border-top">
                            <label class="form-label text-muted small fw-bold text-uppercase mb-2">Penilaian 4 Dimensi Mutu:</label>
                            <div class="row g-2">
                                @foreach($ticket->feedback->ratings as $r)
                                    <div class="col-6 col-sm-3">
                                        <div class="p-2 bg-light rounded-3 text-center">
                                            <small class="text-muted d-block text-truncate" style="font-size: 0.75rem;">{{ ucfirst(str_replace('_', ' ', $r->category)) }}</small>
                                            <strong class="text-warning">⭐ {{ $r->rating }}/5</strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kartu Pihak Terlibat & Kontak Langsung -->
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100 p-3">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-people-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Wali & Santri</h6>
                                <small class="text-muted">Pihak Pelapor</small>
                            </div>
                        </div>
                        <ul class="list-unstyled small mb-3">
                            <li class="mb-1"><strong>Santri:</strong> {{ $ticket->student?->getDisplayName() ?? 'Santri' }}</li>
                            <li class="mb-1"><strong>Wali:</strong> {{ $ticket->parent?->name ?? 'Orang Tua' }}</li>
                            <li><strong>Kontak:</strong> {{ $ticket->parent?->phone ?? '-' }}</li>
                        </ul>
                        @if($ticket->parent?->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', (str_starts_with($ticket->parent->phone, '0') ? '62'.substr($ticket->parent->phone, 1) : $ticket->parent->phone)) }}?text={{ urlencode('Assalamu\'alaikum Bapak/Ibu ' . ($ticket->parent->name ?? '') . ', kami dari Koordinator Akademik Al-Hikmah LMS menindaklanjuti masukan bimbingan ananda ' . ($ticket->student?->getDisplayName() ?? '') . '...') }}" 
                               target="_blank" 
                               class="btn btn-sm btn-outline-success rounded-pill w-100 mt-auto">
                                <i class="bi bi-whatsapp me-1"></i> Hubungi Wali via WhatsApp
                            </a>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100 p-3">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-person-badge-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Guru Pembimbing</h6>
                                <small class="text-muted">Pihak Terkait</small>
                            </div>
                        </div>
                        <ul class="list-unstyled small mb-3">
                            <li class="mb-1"><strong>Nama:</strong> {{ $ticket->mentor?->getDisplayName() ?? 'Guru' }}</li>
                            <li class="mb-1"><strong>Rating:</strong> ⭐ {{ $ticket->mentor?->rating ?? '5.0' }}/5.0</li>
                            <li><strong>Kontak:</strong> {{ $ticket->mentor?->user?->phone ?? '-' }}</li>
                        </ul>
                        @if($ticket->mentor?->user?->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', (str_starts_with($ticket->mentor->user->phone, '0') ? '62'.substr($ticket->mentor->user->phone, 1) : $ticket->mentor->user->phone)) }}?text={{ urlencode('Assalamu\'alaikum Ustadz/Ustazah ' . ($ticket->mentor->getDisplayName()) . ', kami dari Koordinator Pengajar Al-Hikmah LMS ingin mengonfirmasi dan berkoordinasi terkait catatan sesi belajar santri ' . ($ticket->student?->getDisplayName() ?? '') . '...') }}" 
                               target="_blank" 
                               class="btn btn-sm btn-outline-success rounded-pill w-100 mt-auto">
                                <i class="bi bi-whatsapp me-1"></i> Koordinasi Guru via WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Tindak Lanjut Koordinator & Eskalasi -->
        <div class="col-lg-5">
            <!-- Form Tindak Lanjut -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-light py-3 border-bottom">
                    <h6 class="m-0 fw-bold text-dark"><i class="bi bi-shield-check me-2 text-primary"></i>Tindak Lanjut & Rekonsiliasi</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.tickets.update', $ticket->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Status Tiket:</label>
                            <select name="status" class="form-select rounded-3" required>
                                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>🔴 Terbuka (Belum Ditangani)</option>
                                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>🟡 Sedang Ditangani / Koordinasi</option>
                                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>🟢 Selesai (Rekonsiliasi Tuntas)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Rencana Tindakan (Action Plan):</label>
                            <textarea name="action_plan" rows="3" class="form-control rounded-3" placeholder="Contoh: Mengingatkan ustadz hadir 5 menit sebelum sesi dimulai dan menghubungi wali santri untuk konfirmasi ulang jadwal.">{{ old('action_plan', $ticket->action_plan) }}</textarea>
                            <small class="text-muted">Langkah mitigasi yang diambil Koordinator Pengajar.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Catatan Solusi / Hasil Mediasi:</label>
                            <textarea name="resolution_notes" rows="3" class="form-control rounded-3" placeholder="Contoh: Wali santri telah dihubungi, ustadz telah memohon maaf atas keterlambatan teknis, dan kesepakatan sesi pengganti telah disetujui bersama.">{{ old('resolution_notes', $ticket->resolution_notes) }}</textarea>
                            <small class="text-muted">Hasil akhir komunikasi dengan wali santri dan mentor.</small>
                        </div>

                        <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 shadow-sm fw-semibold">
                            <i class="bi bi-save me-1"></i> Simpan Pembaharuan Tiket
                        </button>
                    </form>
                </div>
            </div>

            <!-- Panel Eskalasi ke Mutasi Santri -->
            @if($ticket->status !== 'escalated_to_mutation')
                <div class="card border border-danger border-opacity-25 shadow-sm rounded-4">
                    <div class="card-header bg-danger bg-opacity-10 py-3 border-0">
                        <h6 class="m-0 fw-bold text-danger"><i class="bi bi-arrow-left-right me-2"></i>Eskalasi ke Mutasi Santri (Family Blacklist)</h6>
                    </div>
                    <div class="card-body p-4">
                        <p class="small text-muted mb-3">
                            Gunakan opsi ini <strong>hanya jika</strong> hasil mediasi menemui jalan buntu dan wali santri secara tegas meminta pergantian guru pembimbing.
                        </p>
                        <form action="{{ route('admin.tickets.escalate', $ticket->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengekalasi tiket ini ke mutasi santri? Guru ini akan dimasukkan ke Family Blacklist Engine untuk keluarga santri terkait.')">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Alasan Eskalasi / Mutasi:</label>
                                <textarea name="escalation_notes" rows="2" class="form-control rounded-3" required placeholder="Jelaskan alasan ketidakcocokan yang mendasari keputusan mutasi santri..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-outline-danger rounded-pill w-100 py-2 shadow-sm fw-semibold">
                                <i class="bi bi-person-x me-1"></i> Eskalasi Tiket & Catat Log Mutasi
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-dark rounded-4 shadow-sm p-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        <div>
                            <h6 class="fw-bold mb-0">Tiket Telah Dieskalasi ke Log Mutasi</h6>
                            <small class="text-muted">Tercatat pada Family Blacklist Engine. Santri dapat dialokasikan ke guru baru melalui menu pendaftaran.</small>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
