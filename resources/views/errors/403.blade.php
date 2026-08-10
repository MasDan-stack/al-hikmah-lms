@extends('layout.auth-layout')

@section('title', 'Forbidden')
@section('code', '403')
@section('message', 'Akses ke halaman ini ditolak.')

@section('auth-content')
    <div class="text-center">
        <h1 class="display-1 fw-bold">403</h1>
        <h2 class="text-uppercase mb-3">Akses Terlarang</h2>
        <p class="text-muted mb-4">
            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Mungkin Anda perlu login dengan hak akses yang
            berbeda.
        </p>
        <a href="{{ route('login') }}" class="btn btn-danger btn-lg">
            <i class="bi bi-box-arrow-in-left me-2"></i>Kembali Login
        </a>
    </div>
@endsection
