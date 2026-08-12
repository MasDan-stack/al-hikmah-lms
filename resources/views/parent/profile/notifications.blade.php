@extends('layouts.parent')

@section('title', 'Preferensi Notifikasi')
@section('header', 'Preferensi Notifikasi')
@section('subheader', 'Atur metode pemberitahuan jadwal & pengingat SPP')

@section('content')
<div class="container-fluid p-0">
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                <div class="nav flex-column nav-pills">
                    <a href="{{ route('parent.profile.edit') }}" class="nav-link text-dark rounded-pill mb-1">
                        <i class="bi bi-person-gear me-2"></i> Edit Profil Diri
                    </a>
                    <a href="{{ route('parent.profile.notifications') }}" class="nav-link active rounded-pill mb-1">
                        <i class="bi bi-bell me-2"></i> Preferensi Notifikasi
                    </a>
                    <a href="{{ route('parent.profile.children') }}" class="nav-link text-dark rounded-pill mb-1">
                        <i class="bi bi-people me-2"></i> Kelola Data Anak
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h5 class="fw-bold text-dark border-bottom pb-3 mb-3">Pengaturan Notifikasi System</h5>
                <form action="{{ route('parent.profile.update-notifications') }}" method="POST">
                    @csrf
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="notifWa" checked>
                        <label class="form-check-label fw-semibold" for="notifWa">Pemberitahuan WhatsApp Jadwal Bimbingan</label>
                        <div class="small text-muted">Menerima pesan pengingat 1 jam sebelum bimbingan dimulai.</div>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="notifEmail" checked>
                        <label class="form-check-label fw-semibold" for="notifEmail">Notifikasi Email Tagihan SPP & Invoice</label>
                        <div class="small text-muted">Menerima e-invoice resmi saat pembayaran lunas.</div>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" id="notifProgress" checked>
                        <label class="form-check-label fw-semibold" for="notifProgress">Laporan Capaian Hafalan Mingguan</label>
                        <div class="small text-muted">Ringkasan mingguan nilai tajwid & hafalan ananda.</div>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-save me-1"></i> Simpan Preferensi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
