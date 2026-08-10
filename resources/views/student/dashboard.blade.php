@extends('layouts.admin')

@section('title', 'Dashboard Santri')
@section('header', 'Ruang Belajar Santri')
@section('subheader', 'Semangat belajar & muroja\'ah Al-Qur\'an, ' . (auth()->user()->name ?? 'Santri') . '!')

@section('content')
<!-- Row Statistik Cards -->
@livewire('dashboard-stats')

<div class="row g-4 mt-1">
    <div class="col-12 col-lg-7">
        @livewire('progress-tracker')
    </div>
    <div class="col-12 col-lg-5">
        @livewire('session-calendar')
    </div>
</div>
@endsection
