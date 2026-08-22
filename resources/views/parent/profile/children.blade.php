@extends('layouts.parent')

@section('title', 'Kelola Data Anak')
@section('header', 'Kelola Data Anak Binaan')
@section('subheader', 'Tambah data anak atau daftarkan santri baru')

@section('content')
<div class="container-fluid p-0">
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                <div class="nav flex-column nav-pills">
                    <a href="{{ route('parent.profile.edit') }}" class="nav-link text-dark rounded-pill mb-1">
                        <i class="bi bi-person-gear me-2"></i> Edit Profil Diri
                    </a>
                    <a href="{{ route('parent.profile.notifications') }}" class="nav-link text-dark rounded-pill mb-1">
                        <i class="bi bi-bell me-2"></i> Preferensi Notifikasi
                    </a>
                    <a href="{{ route('parent.profile.children') }}" class="nav-link active rounded-pill mb-1">
                        <i class="bi bi-people me-2"></i> Kelola Data Anak
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show rounded-4 mb-4 shadow-sm border-0 d-flex align-items-center gap-2 p-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
                    <div class="fw-semibold">{{ session('warning') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4 shadow-sm border-0 d-flex align-items-center gap-2 p-3" role="alert">
                    <i class="bi bi-x-circle-fill fs-5 text-danger"></i>
                    <div class="fw-semibold">{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(!auth()->user()->hasActivePaidProgram() && !auth()->user()->hasPendingInvoiceOrEnrollment())
                @if($children->isNotEmpty())
                    <div class="card border-0 shadow-sm rounded-4 bg-success-subtle border border-success-subtle p-3 mb-4 d-flex flex-row justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2 text-success-emphasis">
                            <i class="bi bi-check2-circle fs-4 text-success"></i>
                            <div>
                                <span class="fw-bold">Data anak sudah terdaftar!</span>
                                <div class="small text-muted">Langkah selanjutnya adalah memilih paket program & jadwal bimbingan untuk ananda.</div>
                            </div>
                        </div>
                        <a href="{{ url('/biaya') }}" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-cart-plus me-1"></i> Pilih Program Sekarang <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                @else
                    <div class="alert alert-info border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center gap-3">
                        <i class="bi bi-info-circle-fill text-info fs-4"></i>
                        <div class="small">
                            <strong>Langkah 1 dari 3:</strong> Silakan lengkapi data calon santri pada formulir di bawah ini. Setelah tersimpan, sistem akan mengaktifkan akses ke halaman pemilihan program belajar.
                        </div>
                    </div>
                @endif
            @endif

            <!-- Form Tambah Anak Baru -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-3 mb-3"><i class="bi bi-person-plus text-primary me-2"></i>Tambah Data Anak Baru</h5>
                <form action="{{ route('parent.profile.store-child') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap Anak *</label>
                            <input type="text" name="full_name" class="form-control" placeholder="Contoh: Muhammad Rayhan" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Usia (Tahun) *</label>
                            <input type="number" name="age" class="form-control" min="4" max="25" placeholder="8" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Jenis Kelamin *</label>
                            <select name="gender" class="form-select" required>
                                <option value="L">Laki-Laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Domisili / Lokasi Belajar</label>
                            <input type="text" name="location" class="form-control" placeholder="Contoh: Jakarta Selatan / Online Home">
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-plus-circle me-1"></i> Tambahkan Anak Baru
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Daftar Anak Saat Ini -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h5 class="fw-bold text-dark mb-3">Daftar Anak Terhubung</h5>
                @if($children->isEmpty())
                    <div class="text-center py-4 text-muted">Belum ada data anak terdaftar.</div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle datatable" id="tableParentChildren">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Santri</th>
                                    <th>Usia</th>
                                    <th>Gender</th>
                                    <th>Lokasi</th>
                                    <th>Mentor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($children as $c)
                                    <tr>
                                        <td class="fw-bold text-primary">
                                            {{ $c->user?->name ?? $c->full_name }}
                                            @if($c->user?->email)
                                                <br><small class="text-muted fw-normal"><i class="bi bi-envelope me-1"></i>{{ $c->user->email }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $c->age }} Thn</td>
                                        <td>{{ $c->gender === 'L' ? 'Laki-Laki' : 'Perempuan' }}</td>
                                        <td>{{ $c->location ?? 'Online' }}</td>
                                        <td>{{ $c->mentors->first()?->user?->name ?? 'Belum ditentukan' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
