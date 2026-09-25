@extends('layouts.mentor')

@section('title', 'Daftar Santri Binaan | Mentor')
@section('header', 'Daftar Santri Binaan')
@section('subheader', 'Kelola data santri, jadwal bimbingan aktif, dan pantau progres hafalan Al-Qur\'an')

@section('content')
<div class="container-fluid p-0">
    <!-- Alert Feedback -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Top KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Total Santri Binaan</span>
                    <h4 class="fw-bold text-dark mb-0">{{ $students->total() ?? $students->count() }} Santri</h4>
                </div>
                <div class="badge bg-success-subtle text-success p-3 rounded-circle fs-4">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Program Aktif</span>
                    <h4 class="fw-bold text-primary mb-0">Tahfidz & Tahsin</h4>
                </div>
                <div class="badge bg-primary-subtle text-primary p-3 rounded-circle fs-4">
                    <i class="bi bi-book-half"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Aksi Cepat</span>
                    <a href="{{ route('mentor.progress.create') }}" class="btn btn-sm btn-success-custom fw-bold rounded-pill px-3 mt-1 shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Catat Progres Baru
                    </a>
                </div>
                <div class="badge bg-warning-subtle text-warning p-3 rounded-circle fs-4">
                    <i class="bi bi-journal-plus"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-person-lines-fill text-success me-2"></i>Santri dalam Bimbingan Anda</h5>
                <p class="text-muted small mb-0">Daftar santri aktif yang telah dialokasikan ke jadwal mengajar Anda.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('mentor.progress.create') }}" class="btn btn-success-custom fw-bold rounded-pill px-4 btn-sm shadow-sm">
                    <i class="bi bi-pencil-square me-1"></i> Catat Progres
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($students->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-people fs-1 d-block mb-3 text-secondary opacity-50"></i>
                    <h6 class="fw-bold text-dark mb-1">Belum Ada Santri Binaan</h6>
                    <p class="small text-muted mb-0">Santri yang telah mendaftar dan dialokasikan oleh Admin akan muncul di daftar ini.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0" id="tableMentorStudents" style="min-width: 880px;">
                        <thead class="bg-light text-secondary small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">Nama Santri</th>
                                <th>Usia & Gender</th>
                                <th>Program Belajar</th>
                                <th>Orang Tua / Wali</th>
                                <th>Lokasi / Alamat</th>
                                <th class="text-end pe-4 no-sort">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php
                                    $activeEnrollment = $student->enrollments?->first();
                                    $programName = $activeEnrollment?->program?->name ?? $student->programs?->first()?->name ?? 'Tahfidz Al-Qur\'an';
                                    $parent = $student->parent;
                                    $parentUser = $parent?->user;
                                    $phone = $parentUser?->phone ?? $parent?->emergency_phone;
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $phone ?? '');
                                    if (str_starts_with($cleanPhone, '0')) {
                                        $cleanPhone = '62' . substr($cleanPhone, 1);
                                    }
                                    $nameParts = explode(' ', trim($student->getDisplayName()));
                                    $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
                                @endphp
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-success-subtle text-success fw-bold d-flex align-items-center justify-content-center border border-success-subtle shadow-xs" 
                                                 style="width: 40px; height: 40px; font-size: 0.85rem; flex-shrink: 0;">
                                                {{ $initials ?: 'ST' }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $student->getDisplayName() }}</div>
                                                <small class="text-muted">{{ $student->user?->email ?? 'Akun Santri' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border rounded-pill px-2 py-1 small">
                                            {{ $student->age ? $student->age . ' Tahun' : '-' }}
                                        </span>
                                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-1 small">
                                            {{ $student->gender === 'L' || $student->gender === 'laki-laki' ? 'Ikhwan' : 'Akhwat' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                            {{ $programName }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark small">{{ $parentUser?->name ?? $student->parent_name ?? 'Wali Santri' }}</div>
                                        @if($phone)
                                            <a href="https://wa.me/{{ $cleanPhone }}?text=Assalamu'alaikum%20Bapak/Ibu%20wali%20santri%20{{ urlencode($student->getDisplayName()) }}" 
                                               target="_blank" 
                                               class="badge bg-success-subtle text-success border border-success-subtle text-decoration-none mt-1 d-inline-block px-2 py-1"
                                               style="font-size: 0.7rem;">
                                                <i class="bi bi-whatsapp me-1"></i>{{ $phone }}
                                            </a>
                                        @else
                                            <small class="text-muted d-block">-</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-secondary small d-inline-block text-truncate" style="max-width: 180px;" title="{{ $student->getFullAddress() }}">
                                            <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $student->getFullAddress() ?: ($student->location ?? 'Online') }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('mentor.students.show', $student->id) }}" 
                                               class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                                               title="Lihat Detail & Riwayat Progres">
                                                <i class="bi bi-eye me-1"></i> Detail
                                            </a>
                                            <a href="{{ route('mentor.progress.create', ['student_id' => $student->id]) }}" 
                                               class="btn btn-sm btn-outline-success rounded-pill px-3"
                                               title="Input Catatan Mutaba'ah & Progres">
                                                <i class="bi bi-pencil-square me-1"></i> Progres
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(method_exists($students, 'links'))
                    <div class="p-3 border-top d-flex justify-content-end">
                        {{ $students->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
