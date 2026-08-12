@extends('layouts.parent')

@section('title', 'Tabel Daftar Sesi Bimbingan')
@section('header', 'Daftar Sesi Bimbingan')
@section('subheader', 'Tabel filter sesi belajar berdasarkan status')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-list-columns-reverse text-primary me-2"></i>Daftar Sesi Bimbingan</h4>
            <p class="text-muted small mb-0">Filter dan cari riwayat sesi bimbingan anak Anda.</p>
        </div>
        <a href="{{ route('parent.schedules.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-calendar-event me-1"></i> Kembali ke Ringkasan
        </a>
    </div>

    <!-- Filter Buttons -->
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <a href="{{ route('parent.schedules.list', ['status' => 'all']) }}" class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">
            Semua Sesi
        </a>
        <a href="{{ route('parent.schedules.list', ['status' => 'scheduled']) }}" class="btn btn-sm {{ $status === 'scheduled' ? 'btn-warning' : 'btn-outline-warning' }} rounded-pill px-3">
            Terjadwal
        </a>
        <a href="{{ route('parent.schedules.list', ['status' => 'in_progress']) }}" class="btn btn-sm {{ $status === 'in_progress' ? 'btn-info' : 'btn-outline-info' }} rounded-pill px-3">
            Sedang Berlangsung
        </a>
        <a href="{{ route('parent.schedules.list', ['status' => 'completed']) }}" class="btn btn-sm {{ $status === 'completed' ? 'btn-success' : 'btn-outline-success' }} rounded-pill px-3">
            Selesai
        </a>
        <a href="{{ route('parent.schedules.list', ['status' => 'cancelled']) }}" class="btn btn-sm {{ $status === 'cancelled' ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3">
            Dibatalkan
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        @if($sessions->isEmpty())
            <div class="text-center py-4 text-muted">
                Tidak ada sesi bimbingan yang sesuai dengan filter ini.
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal & Jam</th>
                            <th>Santri</th>
                            <th>Metode</th>
                            <th>Mentor</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $ses)
                            <tr>
                                <td class="fw-bold text-primary">{{ $ses->date->format('d/m/Y') }} ({{ $ses->time }})</td>
                                <td class="fw-semibold">{{ $ses->student?->user?->name ?? $ses->student?->full_name }}</td>
                                <td><span class="badge bg-info-subtle text-info rounded-pill">{{ ucfirst($ses->method) }}</span></td>
                                <td>{{ $ses->mentor?->user?->name ?? 'Ustaz' }}</td>
                                <td>
                                    @if($ses->status === 'completed')
                                        <span class="badge bg-success-subtle text-success rounded-pill">Selesai</span>
                                    @elseif($ses->status === 'in_progress')
                                        <span class="badge bg-primary-subtle text-primary rounded-pill">Sedang Berlangsung</span>
                                    @elseif($ses->status === 'cancelled')
                                        <span class="badge bg-danger-subtle text-danger rounded-pill">Dibatalkan</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning rounded-pill">Terjadwal</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('parent.schedules.show', $ses->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
