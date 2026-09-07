@extends('layouts.mentor')

@section('title', 'Data Orang Tua & Wali Santri | Mentor')
@section('header', 'Data Orang Tua & Wali Santri')
@section('subheader', 'Informasi kontak dan komunikasi langsung dengan orang tua/wali santri binaan')

@section('content')
<div class="container-fluid p-0">
    <!-- Top KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Total Wali Santri</span>
                    <h4 class="fw-bold text-dark mb-0">{{ $parents->count() }} Orang Tua</h4>
                </div>
                <div class="badge bg-success-subtle text-success p-3 rounded-circle fs-4">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Kontak WhatsApp Aktif</span>
                    <h4 class="fw-bold text-success mb-0">
                        {{ $parents->filter(fn($p) => !empty($p['parent_user']?->phone ?? $p['parent']?->emergency_phone))->count() }} Terhubung
                    </h4>
                </div>
                <div class="badge bg-success-subtle text-success p-3 rounded-circle fs-4">
                    <i class="bi bi-whatsapp"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Saluran Komunikasi</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 mt-1">
                        <i class="bi bi-chat-dots-fill me-1"></i>WhatsApp & In-App
                    </span>
                </div>
                <div class="badge bg-primary-subtle text-primary p-3 rounded-circle fs-4">
                    <i class="bi bi-chat-heart-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-person-rolodex text-success me-2"></i>Buku Kontak Wali Santri</h5>
                <p class="text-muted small mb-0">Gunakan tombol WhatsApp untuk menghubungi orang tua santri terkait jadwal atau evaluasi bimbingan.</p>
            </div>
        </div>
        <div class="card-body p-0">
            @if($parents->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-people fs-1 d-block mb-3 text-secondary opacity-50"></i>
                    <h6 class="fw-bold text-dark mb-1">Belum Ada Data Orang Tua</h6>
                    <p class="small text-muted mb-0">Data orang tua akan muncul secara otomatis sesuai santri yang dialokasikan ke Anda.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0" id="tableMentorParents" style="min-width: 880px;">
                        <thead class="bg-light text-secondary small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">Nama Santri & Program</th>
                                <th>Nama Orang Tua / Wali</th>
                                <th>No. WhatsApp & Telepon</th>
                                <th>Alamat Rumah</th>
                                <th class="text-end pe-4 no-sort">Hubungi Wali</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($parents as $item)
                                @php
                                    $student = $item['student'];
                                    $parent = $item['parent'];
                                    $parentUser = $item['parent_user'];
                                    $phone = $parentUser?->phone ?? $parent?->emergency_phone;
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $phone ?? '');
                                    if (str_starts_with($cleanPhone, '0')) {
                                        $cleanPhone = '62' . substr($cleanPhone, 1);
                                    }
                                    $activeEnrollment = $student->enrollments?->first();
                                    $programName = $activeEnrollment?->program?->name ?? $student->programs?->first()?->name ?? 'Tahfidz Al-Qur\'an';
                                    $parentName = $parentUser?->name ?? 'Bapak/Ibu Wali Santri';
                                    $pNameParts = explode(' ', trim($parentName));
                                    $pInitials = strtoupper(substr($pNameParts[0], 0, 1) . (isset($pNameParts[1]) ? substr($pNameParts[1], 0, 1) : ''));
                                @endphp
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold text-dark">{{ $student->getDisplayName() }}</div>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.68rem;">
                                            {{ $programName }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-light text-secondary fw-bold d-flex align-items-center justify-content-center border" 
                                                 style="width: 34px; height: 34px; font-size: 0.78rem; flex-shrink: 0;">
                                                {{ $pInitials ?: 'WL' }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark small">{{ $parentName }}</div>
                                                <small class="text-muted d-block" style="font-size: 0.72rem;">
                                                    <i class="bi bi-envelope me-1"></i>{{ $parentUser?->email ?? '-' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($phone)
                                            <span class="badge bg-light text-dark border px-2 py-1 font-monospace" style="font-size: 0.75rem;">
                                                <i class="bi bi-telephone-fill text-success me-1"></i>{{ $phone }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="small text-secondary d-inline-block text-truncate" style="max-width: 220px;" title="{{ $parent?->address ?? $student->getFullAddress() }}">
                                            <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $parent?->address ?: ($student->getFullAddress() ?: 'Online') }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                            @if($parent?->maps_link)
                                                <a href="{{ $parent->maps_link }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1 shadow-sm d-inline-flex align-items-center gap-1 text-nowrap"
                                                   title="Buka Navigasi Lokasi Rumah di Peta" style="font-size: 0.78rem;">
                                                    <i class="bi bi-geo-alt-fill text-danger"></i> Rute Peta
                                                </a>
                                            @endif
                                            @if($phone)
                                                <a href="https://wa.me/{{ $cleanPhone }}?text=Assalamu'alaikum%20Bapak/Ibu%20{{ urlencode($parentName) }},%20saya%20Guru%20Pendamping%20Al-Qur'an%20ananda%20{{ urlencode($student->getDisplayName()) }}%20dari%20AL-HIKMAH%20LMS." 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-success-custom fw-semibold rounded-pill px-3 shadow-sm text-nowrap">
                                                    <i class="bi bi-whatsapp me-1"></i> Hubungi WA
                                                </a>
                                            @else
                                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-1">
                                                    Tidak Ada Nomor
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
