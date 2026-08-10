@extends('layouts.admin')

@section('title', 'Dashboard Pendamping')
@section('header', 'Panel Pendamping / Guru AL-HIKMAH')
@section('subheader', 'Selamat datang kembali, Ustaz/Ustazah ' . (auth()->user()->name ?? '') . '!')

@section('content')
<!-- Row Statistik Cards -->
@livewire('dashboard-stats')

<div class="row g-4 mt-1">
    <div class="col-12 col-lg-7">
        @livewire('session-calendar')
    </div>
    <div class="col-12 col-lg-5">
        @livewire('progress-tracker')
    </div>
</div>
@endsection
