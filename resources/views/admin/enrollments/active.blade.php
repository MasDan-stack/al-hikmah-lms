@extends('layouts.admin')

@section('title', 'Santri & Jadwal Bimbingan Aktif')
@section('header', 'Santri & Jadwal Bimbingan Aktif')
@section('subheader', 'Daftar seluruh santri aktif pasca-kesepakatan jadwal dan pelunasan pembayaran')

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <!-- Filter Bar -->
            <form method="GET" action="{{ route('admin.active-enrollments.index') }}" class="row g-3 align-items-center mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Cari santri atau wali..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="mentor_id" class="form-select bg-light" onchange="this.form.submit()">
                        <option value="">-- Semua Mentor --</option>
                        @foreach($mentors as $m)
                            <option value="{{ $m->id }}" {{ request('mentor_id') == $m->id ? 'selected' : '' }}>{{ $m->getDisplayName() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="program_id" class="form-select bg-light" onchange="this.form.submit()">
                        <option value="">-- Semua Program --</option>
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}" {{ request('program_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.active-enrollments.index') }}" class="btn btn-outline-secondary w-100 rounded-pill">Reset</a>
                </div>
            </form>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Santri & Program</th>
                            <th>Data Orang Tua / Wali</th>
                            <th>Mentor Pembimbing</th>
                            <th>Jadwal Bimbingan</th>
                            <th>Status Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeEnrollments as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark fs-6">{{ $item->student->getDisplayName() }}</div>
                                    <small class="text-muted d-block">{{ $item->student->age }} Th ({{ ucfirst($item->student->gender) }})</small>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small mt-1">
                                        {{ $item->program->name }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->student->parent?->user?->name ?? $item->student->getParentNameAttribute() }}</div>
                                    @php $phone = $item->student->getParentPhone(); @endphp
                                    @if($phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $phone) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill py-0 px-2 my-1">
                                            <i class="bi bi-whatsapp me-1"></i> {{ $phone }}
                                        </a>
                                    @endif
                                    <div class="small text-muted text-truncate" style="max-width: 250px;" title="{{ $item->student->getFullAddress() }}">
                                        <i class="bi bi-geo-alt text-danger me-1"></i>{{ $item->student->getFullAddress() }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">{{ $item->mentor?->getDisplayName() ?? 'Belum Ditugaskan' }}</div>
                                    <small class="text-muted">{{ $item->mentor?->user?->email }}</small>
                                </td>
                                <td>
                                    <div class="small"><strong>Hari:</strong> {{ $item->effective_days_label }}</div>
                                    <div class="small"><strong>Jam:</strong> {{ $item->effective_time_label }}</div>
                                    <small class="text-success fw-semibold"><i class="bi bi-calendar-check me-1"></i>Mulai: {{ $item->start_date_label }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-success rounded-pill px-3 py-1 mb-1"><i class="bi bi-check-circle me-1"></i> LUNAS</span>
                                    <div class="small fw-bold text-dark">Rp {{ number_format($item->payment?->amount ?? $item->program_price, 0, ',', '.') }}</div>
                                    <small class="text-muted">{{ $item->payment?->invoice_number }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    Belum ada santri aktif yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $activeEnrollments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
