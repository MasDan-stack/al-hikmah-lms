@extends('layouts.mentor')

@section('title', 'Detail Santri - ' . ($student->user?->name ?? $student->full_name))
@section('header', 'Detail & Riwayat Progres Santri')
@section('subheader', 'Informasi santri dan catatan perkembangan hafalan/tajwid')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4">
        <a href="{{ route('mentor.students.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Santri
        </a>
    </div>

    <div class="row g-4">
        <!-- Student Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <div class="text-center mb-3">
                    <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-2" style="width: 80px; height: 80px; font-size: 2.5rem;">
                        <i class="bi bi-person"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ $student->user?->name ?? $student->full_name }}</h5>
                    <span class="badge bg-success-subtle text-success rounded-pill px-3">Santri Aktif</span>
                </div>
                <hr>
                <div class="mb-2">
                    <small class="text-muted d-block">Usia / Jenis Kelamin</small>
                    <span class="fw-semibold text-dark">{{ $student->age }} Tahun ({{ ucfirst($student->gender) }})</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Lokasi / Area</small>
                    <span class="fw-semibold text-dark">{{ $student->location ?? '-' }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Orang Tua / Wali</small>
                    <span class="fw-semibold text-dark">{{ $student->parent?->user?->name ?? '-' }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Kontak & WA Wali</small>
                    @php $phone = $student->getParentPhone(); @endphp
                    @if($phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $phone) }}" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 py-1 mt-1">
                            <i class="bi bi-whatsapp me-1"></i> Hubungi Wali Santri
                        </a>
                    @else
                        <span class="fw-semibold text-dark">-</span>
                    @endif
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Alamat Lengkap (Home Visit / Offline)</small>
                    <div class="p-2 bg-light rounded-3 small text-dark mt-1">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $student->getFullAddress() }}
                    </div>
                </div>

                @php 
                    $activeEnrollment = $student->enrollments()->where('status', 'active')->latest()->first(); 
                @endphp
                @if($activeEnrollment)
                <div class="mb-2 pt-2 border-top">
                    <small class="text-muted d-block">Jadwal Bimbingan Disepakati:</small>
                    <div class="small fw-semibold text-primary mt-1">
                        <i class="bi bi-calendar3 me-1"></i> {{ $activeEnrollment->effective_days_label }}
                    </div>
                    <div class="small text-secondary">
                        <i class="bi bi-clock me-1"></i> {{ $activeEnrollment->effective_time_label }}
                    </div>
                </div>
                @endif

                
                <div class="mt-4">
                    <a href="{{ route('report.download', $student->id) }}" class="btn btn-outline-danger w-100 rounded-pill fw-bold" target="_blank">
                        <i class="bi bi-file-earmark-pdf me-2"></i> Cetak Laporan PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- Progress History -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-journal-check me-2 text-primary"></i>Riwayat Progres Belajar</h5>
                    <a href="{{ route('mentor.progress.create', ['student_id' => $student->id]) }}" class="btn btn-sm btn-success rounded-pill px-3">
                        <i class="bi bi-plus-lg me-1"></i> Catat Progres
                    </a>
                </div>
                <div class="card-body p-4">
                    @if($progresses->isEmpty())
                        <div class="text-center py-5 text-muted">
                            Belum ada riwayat progres hafalan/bacaan untuk santri ini.
                        </div>
                    @else
                        <div class="timeline">
                            @foreach($progresses as $prog)
                                <div class="p-3 mb-3 border rounded-3 bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-primary rounded-pill px-3">{{ ucfirst($prog->kategori) }}</span>
                                        <small class="text-muted">{{ $prog->created_at->format('d M Y, H:i') }}</small>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">
                                        {{ $prog->surah_start ?? 'Surah' }} 
                                        @if($prog->ayat_start) (Ayat {{ $prog->ayat_start }} - {{ $prog->ayat_end }}) @endif
                                        <span class="badge bg-info-subtle text-info ms-2">Juz {{ $prog->juz ?? 1 }}</span>
                                    </h6>
                                    <div class="d-flex gap-3 my-2 small">
                                        <span>Kelancaran: <strong>{{ $prog->nilai_fluent ?? '-' }}</strong></span>
                                        <span>Tajwid: <strong>{{ $prog->nilai_tajwid ?? '-' }}</strong></span>
                                        <span>Adab: <strong>{{ $prog->nilai_adab ?? '-' }}</strong></span>
                                    </div>
                                    @if($prog->catatan_evaluasi)
                                        <p class="small text-secondary mb-1"><em>"{{ $prog->catatan_evaluasi }}"</em></p>
                                    @endif
                                    @if($prog->homework)
                                        <div class="small text-success"><strong>Tugas/Homework:</strong> {{ $prog->homework }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
