@extends('layouts.admin')

@section('title', 'Kelola Santri')
@section('header', 'Data Murid / Santri')
@section('subheader', 'Kelola profil santri, relasi akun Orang Tua, dan lokasi bimbingan Al-Qur\'an')

@section('content')
    @livewire('student-manager')
@endsection
