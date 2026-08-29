@extends('layouts.admin')

@section('title', 'Detail Lamaran Mentor')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-file-earmark-person me-2"></i>Detail Lamaran: {{ $application->full_name }}</h1>
        <a href="{{ route('admin.recruitment.applications.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Kiri: Data Pelamar -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-person-lines-fill me-2"></i>Informasi Pelamar</h6>
                    <span class="badge bg-secondary font-monospace">{{ $application->application_code }}</span>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-striped">
                        <tr><th width="30%">Nama Lengkap</th><td><strong>{{ $application->full_name }}</strong> ({{ $application->gender == 'male' ? 'Laki-laki' : 'Perempuan' }})</td></tr>
                        <tr><th>Email</th><td>{{ $application->email }}</td></tr>
                        <tr><th>WhatsApp</th><td><a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $application->phone) }}" target="_blank" class="text-success"><i class="bi bi-whatsapp me-1"></i>{{ $application->phone }}</a></td></tr>
                        <tr><th>TTL & Domisili</th><td>{{ $application->city ?? '-' }}, {{ $application->birth_date ? Carbon\Carbon::parse($application->birth_date)->format('d/m/Y') : '-' }} ({{ $application->address ?? '-' }})</td></tr>
                        <tr><th>Pendidikan Terakhir</th><td>{{ $application->education }} - {{ $application->institution }}</td></tr>
                        <tr><th>Spesialisasi Target</th><td><span class="badge bg-primary">{{ $application->specialization }}</span></td></tr>
                        <tr><th>Jumlah Hafalan</th><td><strong>{{ $application->hifz_total_juz }} Juz</strong></td></tr>
                        <tr><th>Pengalaman</th><td>{{ $application->experience_years }} Tahun</td></tr>
                        <tr><th>Silsilah Sanad</th><td>{{ $application->sanad_chain ?: 'Tidak dicantumkan' }}</td></tr>
                        <tr><th>Deskripsi Pengalaman</th><td><small class="text-muted">{{ $application->experience_description }}</small></td></tr>
                    </table>

                    <h6 class="mt-4 font-weight-bold text-gray-800"><i class="bi bi-folder2-open me-2 text-primary"></i>Berkas Lampiran Persyaratan</h6>
                    <div class="row mt-2">
                        @forelse($application->documents as $doc)
                            <div class="col-md-6 mb-3">
                                <div class="card border p-3 h-100 bg-light shadow-sm">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi {{ $doc->document_type == 'cv' ? 'bi-file-earmark-pdf-fill text-danger fs-3' : 'bi-file-earmark-image-fill text-info fs-3' }}"></i>
                                            <div>
                                                <span class="badge {{ $doc->document_type == 'cv' ? 'bg-danger-subtle text-danger' : 'bg-info-subtle text-info' }} text-uppercase fw-bold">{{ $doc->document_type }}</span>
                                                <div class="text-truncate small fw-semibold text-dark mt-1" style="max-width: 180px;" title="{{ $doc->file_name }}">{{ $doc->file_name }}</div>
                                            </div>
                                        </div>
                                        <span class="badge bg-secondary text-white">{{ round($doc->file_size) }} KB</span>
                                    </div>
                                    <div class="mt-2 pt-2 border-top">
                                        <a href="{{ route('admin.recruitment.applications.document', [$application->id, $doc->id]) }}" target="_blank" class="btn btn-sm btn-primary rounded-pill w-100">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Buka / Unduh Berkas
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted small ps-2">Tidak ada berkas yang diunggah.</p>
                            </div>
                        @endforelse
                    </div>

                    @if($application->testSessions->isNotEmpty())
                        <h6 class="mt-4 font-weight-bold text-gray-800"><i class="bi bi-clipboard-check me-2 text-primary"></i>Riwayat Sesi Ujian & Wawancara</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle datatable mt-2" data-no-paging="true">
                                <thead class="table-light">
                                    <tr>
                                        <th>Jenis Sesi</th>
                                        <th>Jadwal</th>
                                        <th>Skor</th>
                                        <th>Predikat</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($application->testSessions as $session)
                                        <tr>
                                            <td class="text-capitalize fw-semibold">{{ str_replace('_', ' ', $session->session_type) }}</td>
                                            <td>{{ Carbon\Carbon::parse($session->scheduled_at)->format('d/m/Y H:i') }}</td>
                                            <td><strong>{{ $session->score !== null ? $session->score : '-' }}</strong></td>
                                            <td><span class="badge bg-info text-capitalize">{{ $session->grade ?: '-' }}</span></td>
                                            <td><span class="badge bg-secondary">{{ $session->status }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kanan: Status & Aksi -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-sliders me-2"></i>Status & Aksi Seleksi</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3 text-center">
                        <small class="text-muted d-block">Status Saat Ini:</small>
                        <div class="my-2">{!! $application->status_badge !!}</div>
                        <span class="badge bg-light text-dark border">Tahap {{ $application->current_stage }} dari 5</span>
                    </div>

                    @if($application->admin_notes)
                        <div class="alert alert-light border p-2 small mb-3">
                            <strong>Catatan Admin:</strong> {{ $application->admin_notes }}
                        </div>
                    @endif

                    @if($application->rejection_reason)
                        <div class="alert alert-danger p-2 small mb-3">
                            <strong>Alasan Ditolak:</strong> {{ $application->rejection_reason }}
                        </div>
                    @endif

                    <hr>

                    <!-- Aksi Berdasarkan Status -->
                    @if($application->status === 'submitted')
                        <form action="{{ route('admin.recruitment.applications.approveDocument', $application->id) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Verifikasi & setujui dokumen berkas pelamar ini?')">
                                <i class="bi bi-check-circle me-1"></i>Setujui Berkas (Lanjut ke Tahap Tes)
                            </button>
                        </form>
                    @endif

                    @if($application->status === 'document_review')
                        <form action="{{ route('admin.recruitment.tests.generate', $application->id) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-cpu me-1"></i>Generate Soal Tes AI & Mulai Ujian
                            </button>
                        </form>
                    @endif

                    @if($application->status === 'test_scheduled')
                        @php
                            $latestSession = $application->testSessions->last();
                        @endphp
                        @if($latestSession)
                            <form action="{{ route('admin.recruitment.tests.evaluate', $latestSession->id) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-info text-white w-100">
                                    <i class="bi bi-robot me-1"></i>Evaluasi Jawaban Tes (AI Evaluator)
                                </button>
                            </form>
                        @endif
                    @endif

                    @if($application->status === 'test_completed')
                        <button type="button" class="btn btn-warning w-100 mb-2" data-bs-toggle="modal" data-bs-target="#interviewModal">
                            <i class="bi bi-calendar-event me-1"></i>Jadwalkan Wawancara & Microteaching
                        </button>
                    @endif

                    @if($application->status === 'interview_scheduled')
                        <form action="{{ route('admin.recruitment.applications.accept', $application->id) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Terima calon mentor ini dan terbitkan akun resmi otomatis?')">
                                <i class="bi bi-person-check-fill me-1"></i>Terima & Terbitkan Akun Mentor
                            </button>
                        </form>
                    @endif

                    @if($application->status === 'approved')
                        <div class="alert alert-success text-center py-2">
                            <i class="bi bi-check2-all fs-4 d-block"></i>
                            <strong>Pelamar Telah Diterima</strong>
                            <p class="small mb-0">Akun Mentor telah aktif dalam masa percobaan.</p>
                        </div>
                    @endif

                    @if(!in_array($application->status, ['rejected', 'approved', 'withdrawn']))
                        <button type="button" class="btn btn-outline-danger w-100 mt-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle me-1"></i>Tolak Lamaran
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Jadwal Wawancara -->
<div class="modal fade" id="interviewModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.recruitment.applications.scheduleInterview', $application->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Jadwalkan Wawancara & Simulasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Catatan / Link Meeting / Instruksi Wawancara</label>
                        <textarea name="notes" class="form-control" rows="3" required placeholder="Contoh: Wawancara Online via Zoom pada hari Kamis pukul 10.00 WIB. Link: https://meet.google.com/xyz"></textarea>
                    </div>
                    <small class="text-muted">Notifikasi undangan wawancara akan otomatis dikirim ke nomor WhatsApp pelamar.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Kirim Undangan Wawancara</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tolak Lamaran -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.recruitment.applications.reject', $application->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger font-weight-bold">Tolak Lamaran Calon Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan (Akan dikirim via WhatsApp secara sopan)</label>
                        <textarea name="notes" class="form-control" rows="3" required placeholder="Contoh: Kualifikasi hafalan belum memenuhi kuota standarisasi saat ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Konfirmasi Tolak</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
