@extends('layouts.admin')

@section('title', 'Pesan Kontak Masuk | Admin AL-HIKMAH')

@section('content')
<div class="container-fluid py-4">
    <!-- Header & Summary Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-primary-subtle text-primary-emphasis p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small d-block text-muted">Total Pesan Masuk</span>
                        <h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4>
                    </div>
                    <i class="bi bi-inbox fs-2 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-danger-subtle text-danger-emphasis p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small d-block text-muted">Belum Dibaca / Direspon</span>
                        <h4 class="fw-bold mb-0">{{ $stats['unread'] }}</h4>
                    </div>
                    <i class="bi bi-envelope-exclamation fs-2 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-success-subtle text-success-emphasis p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small d-block text-muted">Sudah Dihubungi via WA</span>
                        <h4 class="fw-bold mb-0">{{ $stats['contacted'] }}</h4>
                    </div>
                    <i class="bi bi-whatsapp fs-2 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h5 class="fw-bold mb-1"><i class="bi bi-envelope-paper text-success me-2"></i>Pesan Masuk dari Calon Orang Tua / Wali</h5>
                <p class="text-muted small mb-0">Kelola dan follow up pertanyaan atau permohonan bimbingan via WhatsApp.</p>
            </div>
        </div>

        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success border-0 rounded-3 mb-4">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            <!-- Filter Search & Status Form -->
            <form action="{{ route('admin.contacts.index') }}" method="GET" class="row g-2 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama, email, WA, alamat, pesan..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Belum Dibaca (Unread)</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Sudah Dibaca (Read)</option>
                        <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Sudah Dihubungi (Contacted)</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Reset</a>
                    @endif
                </div>
            </form>

            <!-- Table Messages -->
            @if($messages->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-chat-left-dots fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    Belum ada pesan kontak yang masuk.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle table-hover datatable" id="tableAdminContacts">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu</th>
                                <th>Nama & Kontak</th>
                                <th>Alamat / Domisili</th>
                                <th>Isi Pesan</th>
                                <th>Status</th>
                                <th class="text-end no-sort">Aksi & Follow-Up</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $msg)
                                <tr class="{{ $msg->status === 'unread' ? 'table-warning-subtle fw-semibold' : '' }}">
                                    <td class="small text-muted" style="width: 140px;">
                                        {{ $msg->created_at->translatedFormat('d M Y') }}<br>
                                        <small>{{ $msg->created_at->format('H:i') }} WIB</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $msg->name }}</div>
                                        <small class="text-muted d-block">{{ $msg->email }}</small>
                                        <a href="{{ $msg->whatsapp_url }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-2 py-0 mt-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-whatsapp me-1"></i> {{ $msg->phone }}
                                        </a>
                                    </td>
                                    <td class="small text-secondary" style="max-width: 180px;">
                                        {{ $msg->address }}
                                    </td>
                                    <td style="max-width: 250px;">
                                        <div class="text-truncate text-secondary small" title="{{ $msg->message }}">
                                            {{ $msg->message }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $msg->status_badge_class }} rounded-pill px-3 py-2">
                                            {{ $msg->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" 
                                                    data-bs-toggle="modal" data-bs-target="#modalMsg{{ $msg->id }}">
                                                <i class="bi bi-eye me-1"></i> Detail
                                            </button>
                                            <a href="{{ $msg->whatsapp_url }}" target="_blank" class="btn btn-sm btn-success rounded-pill px-3" title="Hubungi via WhatsApp">
                                                <i class="bi bi-whatsapp"></i> Chat
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Modals Detail Pesan (di luar tabel untuk performa maksimal DataTables) -->
                @foreach($messages as $msg)
                    <div class="modal fade" id="modalMsg{{ $msg->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg text-start">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold">
                                        <i class="bi bi-envelope-open text-primary me-2"></i>Pesan dari {{ $msg->name }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body py-4">
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded-3">
                                                <small class="text-muted d-block">Nama Lengkap</small>
                                                <strong class="text-dark">{{ $msg->name }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded-3">
                                                <small class="text-muted d-block">Nomor WhatsApp</small>
                                                <strong class="text-success">{{ $msg->phone }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded-3">
                                                <small class="text-muted d-block">Alamat Email</small>
                                                <strong class="text-dark">{{ $msg->email }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded-3">
                                                <small class="text-muted d-block">Alamat Domisili</small>
                                                <span class="text-dark small">{{ $msg->address }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="small fw-bold text-muted d-block mb-1">Isi Pesan / Kebutuhan:</label>
                                        <div class="p-3 bg-light border rounded-3 text-secondary lh-base">
                                            {{ $msg->message }}
                                        </div>
                                    </div>

                                    <!-- Update Status Form -->
                                    <form action="{{ route('admin.contacts.update-status', $msg->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-5">
                                                <label class="form-label small fw-bold">Update Status Pesan:</label>
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="unread" {{ $msg->status == 'unread' ? 'selected' : '' }}>Belum Dibaca</option>
                                                    <option value="read" {{ $msg->status == 'read' ? 'selected' : '' }}>Sudah Dibaca</option>
                                                    <option value="contacted" {{ $msg->status == 'contacted' ? 'selected' : '' }}>Sudah Dihubungi via WA</option>
                                                </select>
                                            </div>
                                            <div class="col-md-7">
                                                <label class="form-label small fw-bold">Catatan Admin (Internal):</label>
                                                <input type="text" name="admin_notes" class="form-control form-control-sm" placeholder="Contoh: Sudah di-WA tawarkan guru akhwat hari Kamis" value="{{ $msg->admin_notes }}">
                                            </div>
                                            <div class="col-12 mt-3 d-flex justify-content-between flex-wrap gap-2">
                                                <a href="{{ $msg->whatsapp_url }}" target="_blank" class="btn btn-success rounded-pill px-4">
                                                    <i class="bi bi-whatsapp me-2"></i> Hubungi Langsung via WhatsApp
                                                </a>
                                                <button type="submit" class="btn btn-primary rounded-pill px-4">
                                                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer border-0 pt-0 d-flex justify-content-between">
                                    <form action="{{ route('admin.contacts.destroy', $msg->id) }}" method="POST" onsubmit="return confirm('Hapus pesan kontak ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger text-decoration-none">
                                            <i class="bi bi-trash me-1"></i> Hapus Pesan
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
