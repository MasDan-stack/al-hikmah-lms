<!-- resources/views/errors/401.blade.php -->
@extends('layouts.auth-layout')

@section('title', 'Unauthorized')
@section('code', '401')
@section('message', 'Anda tidak memiliki akses ke halaman ini.')

@section('auth-content')
    <div class="text-center">
        <h1 class="display-1 fw-bold">401</h1>
        <h2 class="text-uppercase mb-3">Akses Ditolak</h2>
        <p class="text-muted mb-4">
            Anda tidak memiliki hak akses untuk melihat halaman ini. Silakan login dengan akun yang sesuai.
        </p>
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
            <i class="bi bi-box-arrow-in-left me-2"></i>Login Sekarang
        </a>
    </div>
@endsection
