@extends('layouts.mentor')

@section('title', 'Daftar Santri Binaan')
@section('header', 'Daftar Santri Binaan')
@section('subheader', 'Daftar seluruh santri yang berada dalam bimbingan Anda')

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-people-fill me-2 text-success"></i>Santri Binaan Anda</h5>
            <a href="{{ route('mentor.progress.create') }}" class="btn btn-success rounded-pill px-4 btn-sm fw-bold">
                <i class="bi bi-plus-lg me-1"></i> Catat Progres
            </a>
        </div>
        <div class="card-body p-4">
            @if($students->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                    Belum ada santri yang ditautkan ke akun bimbingan Anda.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle table-hover datatable" id="tableMentorStudents">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Santri</th>
                                <th>Usia / Gender</th>
                                <th>Lokasi</th>
                                <th>Orang Tua / Wali</th>
                                <th class="no-sort">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $student->user?->name ?? $student->full_name }}</div>
                                        <small class="text-muted">{{ $student->user?->email ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark rounded-pill">{{ $student->age }} Tahun</span>
                                        <span class="badge bg-light text-dark rounded-pill">{{ ucfirst($student->gender) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary small"><i class="bi bi-geo-alt me-1"></i>{{ $student->location ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold">{{ $student->parent?->user?->name ?? 'Orang Tua' }}</div>
                                        <small class="text-muted">{{ $student->parent?->emergency_phone ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('mentor.students.show', $student->id) }}" class="btn btn-sm btn-outline-primary rounded-pill me-1">
                                            <i class="bi bi-eye me-1"></i> Detail & Riwayat
                                        </a>
                                        <a href="{{ route('mentor.progress.create', ['student_id' => $student->id]) }}" class="btn btn-sm btn-outline-success rounded-pill">
                                            <i class="bi bi-pencil me-1"></i> Input Progres
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(method_exists($students, 'links'))
                    <div class="mt-4">
                        {{ $students->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
