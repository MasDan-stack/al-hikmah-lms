@extends('layouts.mentor')

@section('title', 'Jadwal Sesi Belajar')
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
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar-event me-2 text-primary"></i>Daftar Sesi Belajar</h5>
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
                                <th>Santri</th>
                                <th>Metode</th>
                                <th>Status Sesi</th>
                                <th>Ubah Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $session->date ? \Carbon\Carbon::parse($session->date)->format('d M Y') : '-' }}</div>
                                        <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $session->time }} WIB</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $session->student?->user?->name ?? $session->student?->full_name }}</div>
                                        <small class="text-muted">{{ $session->notes ?? 'Tidak ada catatan tambahan' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info rounded-pill px-3">{{ ucfirst($session->method) }}</span>
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
