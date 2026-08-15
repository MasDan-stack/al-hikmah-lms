@extends('layouts.mentor')

@section('title', 'Jadwal Sesi Belajar | Mentor')
@section('header', 'Jadwal Sesi Belajar')
@section('subheader', 'Kelola status dan jadwal sesi mengajar santri')

@section('content')
<div class="container-fluid p-0">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar-event me-2 text-primary"></i>Daftar Sesi Belajar Santri</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('mentor.sessions.index', ['status' => 'all']) }}" class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill">Semua</a>
                <a href="{{ route('mentor.sessions.index', ['status' => 'scheduled']) }}" class="btn btn-sm {{ $status === 'scheduled' ? 'btn-warning' : 'btn-outline-warning' }} rounded-pill">Terjadwal</a>
                <a href="{{ route('mentor.sessions.index', ['status' => 'completed']) }}" class="btn btn-sm {{ $status === 'completed' ? 'btn-success' : 'btn-outline-success' }} rounded-pill">Selesai</a>
            </div>
        </div>
        <div class="card-body p-4">
            @if($sessions->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                    Belum ada sesi belajar yang ditemukan.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal & Waktu</th>
                                <th>Santri & Program</th>
                                <th>Kontak Wali & Alamat</th>
                                <th>Metode Belajar</th>
                                <th>Konfirmasi Wali</th>
                                <th>Status Sesi</th>
                                <th>Ubah Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                                @php
                                    $student = $session->student;
                                    $activeEnrollment = $student?->enrollments?->first();
                                    $programName = $activeEnrollment?->program?->name ?? $student?->programs?->first()?->name ?? 'Program Al-Hikmah';
                                    $parentPhone = $student?->getParentPhone();
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $session->date ? \Carbon\Carbon::parse($session->date)->locale('id')->isoFormat('dddd, D MMMM Y') : '-' }}</div>
                                        <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ date('H:i', strtotime($session->time)) }} WIB</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $student?->getDisplayName() }}</div>
                                        <span class="badge bg-primary-subtle text-primary rounded-pill small">{{ $programName }}</span>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-secondary">{{ $student?->parent_name }}</div>
                                        @if($parentPhone)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', str_starts_with($parentPhone, '0') ? '62'.substr($parentPhone, 1) : $parentPhone) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-2 py-0 my-1">
                                                <i class="bi bi-whatsapp me-1"></i> {{ $parentPhone }}
                                            </a>
                                        @else
                                            <small class="text-muted d-block">-</small>
                                        @endif
                                        <div class="small text-muted text-truncate" style="max-width: 200px;">{{ $student?->getFullAddress() }}</div>
                                    </td>
                                    <td>
                                        @if($session->method === 'offline')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">
                                                <i class="bi bi-house-door me-1"></i> Offline (Home Visit)
                                            </span>
                                        @elseif($session->method === 'online')
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2">
                                                <i class="bi bi-camera-video me-1"></i> Online
                                            </span>
                                        @else
                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-2">
                                                <i class="bi bi-arrow-repeat me-1"></i> Hybrid
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->confirmation)
                                            @if($session->confirmation->status === 'hadir')
                                                <span class="badge bg-success text-white rounded-pill px-3 py-2">
                                                    <i class="bi bi-check-circle me-1"></i> Hadir
                                                </span>
                                            @elseif($session->confirmation->status === 'izin')
                                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2" title="{{ $session->confirmation->notes }}">
                                                    <i class="bi bi-info-circle me-1"></i> Izin
                                                </span>
                                            @elseif($session->confirmation->status === 'sakit')
                                                <span class="badge bg-danger text-white rounded-pill px-3 py-2" title="{{ $session->confirmation->notes }}">
                                                    <i class="bi bi-heart-pulse me-1"></i> Sakit
                                                </span>
                                            @endif
                                            @if($session->confirmation->notes)
                                                <small class="d-block text-muted fst-italic mt-1" style="max-width: 160px;">"{{ \Illuminate\Support\Str::limit($session->confirmation->notes, 30) }}"</small>
                                            @endif
                                        @else
                                            <span class="badge bg-light text-secondary rounded-pill border px-3 py-2">
                                                <i class="bi bi-hourglass-split me-1"></i> Belum Konfirmasi
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->status === 'completed')
                                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">Selesai</span>
                                        @elseif($session->status === 'cancelled')
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-2">Dibatalkan</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2">Terjadwal</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('mentor.sessions.update-status', $session->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <select name="status" class="form-select form-select-sm rounded-pill d-inline-block w-auto me-1" onchange="this.form.submit()">
                                                <option value="scheduled" {{ $session->status === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                                                <option value="completed" {{ $session->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                                                <option value="cancelled" {{ $session->status === 'cancelled' ? 'selected' : '' }}>Batalkan</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $sessions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
