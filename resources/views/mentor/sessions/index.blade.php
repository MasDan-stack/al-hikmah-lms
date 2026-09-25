@extends('layouts.mentor')

@section('title', 'Jadwal Sesi Belajar | Mentor')
@section('header', 'Jadwal Sesi Belajar')
@section('subheader', 'Pantau jadwal mengajar, konfirmasi kehadiran santri, dan kelola status sesi')

@section('content')
<div class="container-fluid p-0">
    <!-- Alert Feedback -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 🔴 PEMBERITAHUAN WAJIB UPLOAD BUKTI FOTO (CATATAN MERAH SLIP GAJI) -->
    @php
        $missingProofCount = $sessions->filter(function($sess) {
            $isCompletedOrHadir = $sess->status === 'completed' || ($sess->confirmation && in_array($sess->confirmation->status, ['hadir', 'terlambat']));
            $hasNoProof = !($sess->confirmation && !empty($sess->confirmation->proof_image));
            return $isCompletedOrHadir && $hasNoProof;
        })->count();
    @endphp
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);">
        <div class="card-body p-4 text-white">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle bg-white text-danger p-2.5 d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 44px; height: 44px;">
                        <i class="bi bi-camera-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <h6 class="fw-bold mb-0 text-white fs-6">Kewajiban Upload Bukti Foto Mengajar</h6>
                            @if($missingProofCount > 0)
                                <span class="badge bg-white text-danger fw-bold rounded-pill px-2.5 py-1 shadow-xs" style="font-size: 0.72rem;">
                                    {{ $missingProofCount }} Sesi Belum Ada Bukti
                                </span>
                            @else
                                <span class="badge bg-white text-success fw-bold rounded-pill px-2.5 py-1 shadow-xs" style="font-size: 0.72rem;">
                                    Semua Sesi Telah Berbukti Foto
                                </span>
                            @endif
                        </div>
                        <p class="mb-0 text-white-50 small" style="max-width: 820px; line-height: 1.5;">
                            Setiap sesi bimbingan yang telah dihadiri santri <strong>wajib dilengkapi bukti foto dokumentasi mengajar</strong> di rumah murid. Kehadiran tanpa bukti foto tidak dapat divalidasi dan tidak akan masuk ke <strong>Slip Gaji / Honorarium Guru (Bagian B. Rincian Kehadiran &amp; Honor Persantri)</strong> di sistem Admin.
                        </p>
                    </div>
                </div>
                <div>
                    <span class="badge bg-white text-danger fw-bold rounded-pill px-3 py-2 shadow-xs">
                        <i class="bi bi-cash-stack me-1"></i> Rp 100.000 / Kehadiran Valid
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Total Sesi Bimbingan</span>
                    <h4 class="fw-bold text-dark mb-0">{{ $sessions->count() }} Sesi</h4>
                </div>
                <div class="badge bg-primary-subtle text-primary p-3 rounded-circle fs-4">
                    <i class="bi bi-calendar-range"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Sesi Terjadwal</span>
                    <h4 class="fw-bold text-warning mb-0">{{ $sessions->where('status', 'scheduled')->count() }} Sesi</h4>
                </div>
                <div class="badge bg-warning-subtle text-warning p-3 rounded-circle fs-4">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Sesi Selesai</span>
                    <h4 class="fw-bold text-success mb-0">{{ $sessions->where('status', 'completed')->count() }} Sesi</h4>
                </div>
                <div class="badge bg-success-subtle text-success p-3 rounded-circle fs-4">
                    <i class="bi bi-check2-all"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block">Santri Konfirmasi Hadir</span>
                    <h4 class="fw-bold text-info mb-0">{{ $sessions->where('confirmation.status', 'hadir')->count() }} Santri</h4>
                </div>
                <div class="badge bg-info-subtle text-info p-3 rounded-circle fs-4">
                    <i class="bi bi-person-check-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-calendar-check-fill text-success me-2"></i>Daftar Sesi Mengajar Santri</h5>
                <p class="text-muted small mb-0">Klik tombol status untuk memperbarui progres pelaksanaan sesi belajar.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('mentor.sessions.index', ['status' => 'all']) }}" 
                   class="btn btn-sm {{ ($status ?? 'all') === 'all' ? 'btn-success fw-bold text-white' : 'btn-light border' }} rounded-pill px-3">
                   Semua
                </a>
                <a href="{{ route('mentor.sessions.index', ['status' => 'scheduled']) }}" 
                   class="btn btn-sm {{ ($status ?? '') === 'scheduled' ? 'btn-warning fw-bold text-dark' : 'btn-light border' }} rounded-pill px-3">
                   <i class="bi bi-clock me-1"></i>Terjadwal
                </a>
                <a href="{{ route('mentor.sessions.index', ['status' => 'completed']) }}" 
                   class="btn btn-sm {{ ($status ?? '') === 'completed' ? 'btn-success fw-bold text-white' : 'btn-light border' }} rounded-pill px-3">
                   <i class="bi bi-check-circle me-1"></i>Selesai
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($sessions->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-x fs-1 d-block mb-3 text-secondary opacity-50"></i>
                    <h6 class="fw-bold text-dark mb-1">Belum Ada Sesi Belajar</h6>
                    <p class="small text-muted mb-0">Sesi bimbingan baru akan otomatis terjadwal ketika santri mengonfirmasi pendaftaran.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0 datatable" id="tableMentorSessions" style="min-width: 900px;">
                        <thead class="bg-light text-secondary small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3" style="width: 220px;">Hari & Waktu</th>
                                <th>Santri & Program</th>
                                <th>Wali & Kontak</th>
                                <th>Metode Belajar</th>
                                <th>Konfirmasi Kehadiran</th>
                                <th>Status Sesi</th>
                                <th class="text-end pe-4 no-sort" style="width: 140px;">Ubah Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                                @php
                                    $student = $session->student;
                                    $activeEnrollment = $student?->enrollments?->first();
                                    $programName = $activeEnrollment?->program?->name ?? $student?->programs?->first()?->name ?? 'Program Al-Hikmah';
                                    $parentPhone = $student?->getParentPhone();
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $parentPhone ?? '');
                                    if (str_starts_with($cleanPhone, '0')) {
                                        $cleanPhone = '62' . substr($cleanPhone, 1);
                                    }
                                @endphp
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold text-heading">
                                            {{ $session->date ? \Carbon\Carbon::parse($session->date)->locale('id')->isoFormat('dddd, D MMMM Y') : '-' }}
                                        </div>
                                        <span class="badge bg-light text-secondary border font-monospace mt-1" style="font-size: 0.72rem;">
                                            <i class="bi bi-clock-fill text-warning me-1"></i>{{ date('H:i', strtotime($session->time)) }} WIB
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-heading">{{ $student?->getDisplayName() }}</div>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.68rem;">
                                            {{ $programName }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-heading">{{ $student?->parent_name ?? $student?->parent?->user?->name ?? 'Wali Santri' }}</div>
                                        @if($parentPhone)
                                            <a href="https://wa.me/{{ $cleanPhone }}?text=Assalamu'alaikum%20Bapak/Ibu%20wali%20santri%20{{ urlencode($student?->getDisplayName() ?? '') }}" 
                                               target="_blank" 
                                               class="badge bg-success-subtle text-success border border-success-subtle text-decoration-none mt-1 d-inline-block px-2 py-1"
                                               style="font-size: 0.7rem;">
                                                <i class="bi bi-whatsapp me-1"></i>{{ $parentPhone }}
                                            </a>
                                        @else
                                            <small class="text-muted d-block">-</small>
                                        @endif
                                        <div class="small text-muted text-truncate mt-1" style="max-width: 180px; font-size: 0.72rem;" title="{{ $student?->effective_address }}">
                                            @if($student?->maps_link)
                                                <a href="{{ $student->maps_link }}" target="_blank" rel="noopener noreferrer" class="text-success fw-semibold text-decoration-none" title="Buka Rute Navigasi">
                                                    <i class="bi bi-pin-map-fill text-danger me-1"></i>{{ \Illuminate\Support\Str::limit($student->effective_address, 25) }}
                                                </a>
                                            @else
                                                <i class="bi bi-geo-alt me-1"></i>{{ $student?->effective_address ?: 'Lokasi Santri' }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($session->method === 'offline')
                                            <div>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                                    <i class="bi bi-house-door-fill me-1"></i> Offline (Home Visit)
                                                </span>
                                            </div>
                                            <div class="mt-1">
                                                @if($student?->maps_link)
                                                    <a href="{{ $student->maps_link }}" target="_blank" rel="noopener noreferrer" class="badge bg-success text-white rounded-pill px-2 py-1 text-decoration-none shadow-xs d-inline-flex align-items-center gap-1" title="Buka Rute Navigasi Google Maps/Waze">
                                                        <i class="bi bi-pin-map-fill"></i> <span>Buka Peta</span>
                                                        <i class="bi bi-box-arrow-up-right" style="font-size: 0.6rem;"></i>
                                                    </a>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1" style="font-size: 0.68rem;">
                                                        <i class="bi bi-geo-alt me-1"></i>Titik peta belum ditambahkan wali
                                                    </span>
                                                @endif
                                            </div>
                                        @elseif($session->method === 'online')
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">
                                                <i class="bi bi-camera-video-fill me-1"></i> Online
                                            </span>
                                        @else
                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-1">
                                                <i class="bi bi-arrow-repeat me-1"></i> Hybrid
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->confirmation)
                                            <div class="d-flex flex-column align-items-start gap-1">
                                                @if($session->confirmation->status === 'hadir')
                                                    <span class="badge bg-success text-white rounded-pill px-3 py-1">
                                                        <i class="bi bi-check-circle-fill me-1"></i> Hadir
                                                    </span>
                                                @elseif($session->confirmation->status === 'izin')
                                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1" title="{{ $session->confirmation->notes }}">
                                                        <i class="bi bi-info-circle-fill me-1"></i> Izin
                                                    </span>
                                                @elseif($session->confirmation->status === 'sakit')
                                                    <span class="badge bg-danger text-white rounded-pill px-3 py-1" title="{{ $session->confirmation->notes }}">
                                                        <i class="bi bi-heart-pulse-fill me-1"></i> Sakit
                                                    </span>
                                                @endif

                                                <span class="badge bg-light text-secondary rounded-pill border px-2 py-0.5" style="font-size: 0.68rem;">
                                                    {{ $session->confirmation->confirmed_by === 'mentor' ? 'Input Guru' : 'Konfirmasi Wali' }}
                                                </span>

                                                @if($session->confirmation->proof_image_url)
                                                    <a href="{{ $session->confirmation->proof_image_url }}" target="_blank" class="badge bg-light text-primary border rounded-pill text-decoration-none px-2 py-0.5 d-inline-flex align-items-center gap-1" title="Buka foto dokumentasi pengajaran">
                                                        <i class="bi bi-camera-fill text-success"></i> Foto Bukti
                                                    </a>
                                                @else
                                                    @if($session->confirmation->status === 'hadir' || $session->status === 'completed')
                                                        <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-xs" title="Wajib upload foto dokumentasi agar honor mengajar masuk ke Slip Gaji Admin">
                                                            <i class="bi bi-exclamation-triangle-fill"></i> Wajib Upload Bukti
                                                        </span>
                                                    @endif
                                                @endif

                                                @if($session->confirmation->notes)
                                                    <small class="d-block text-muted fst-italic mt-0.5" style="font-size: 0.68rem; max-width: 150px;">
                                                        "{{ \Illuminate\Support\Str::limit($session->confirmation->notes, 30) }}"
                                                    </small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge bg-light text-secondary rounded-pill border px-3 py-1">
                                                <i class="bi bi-hourglass-split me-1"></i> Belum Ada Presensi
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->status === 'completed')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                                <i class="bi bi-check2 me-1"></i>Selesai
                                            </span>
                                        @elseif($session->status === 'cancelled')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">
                                                <i class="bi bi-x me-1"></i>Dibatalkan
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1">
                                                <i class="bi bi-clock me-1"></i>Terjadwal
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4 text-nowrap">
                                        <div class="d-flex align-items-center justify-content-end gap-1.5 flex-wrap">
                                            @php
                                                $needsProof = ($session->status === 'completed' || optional($session->confirmation)->status === 'hadir') && !optional($session->confirmation)->proof_image;
                                            @endphp
                                            @if($needsProof)
                                                <a href="{{ route('mentor.sessions.confirm-attendance', $session->id) }}" 
                                                   class="btn btn-sm btn-danger text-white rounded-pill px-3 py-1 shadow-sm fw-bold d-inline-flex align-items-center gap-1.5" 
                                                   title="Wajib upload foto dokumentasi agar honor mengajar masuk ke Slip Gaji!">
                                                    <i class="bi bi-camera-fill"></i> Upload Bukti Foto
                                                </a>
                                            @else
                                                <a href="{{ route('mentor.sessions.confirm-attendance', $session->id) }}" 
                                                   class="btn btn-sm btn-success text-white rounded-pill px-2.5 py-1 shadow-xs fw-semibold d-inline-flex align-items-center gap-1" 
                                                   title="Input presensi mandiri & upload bukti foto di lokasi santri">
                                                    <i class="bi bi-camera-fill"></i> Presensi
                                                </a>
                                            @endif

                                            <form action="{{ route('mentor.sessions.update-status', $session->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <select name="status" class="form-select form-select-sm rounded-pill shadow-xs" onchange="this.form.submit()" style="font-size: 0.75rem; width: auto; display: inline-block;">
                                                    <option value="scheduled" {{ $session->status === 'scheduled' ? 'selected' : '' }}>⏳ Terjadwal</option>
                                                    <option value="completed" {{ $session->status === 'completed' ? 'selected' : '' }}>✅ Selesai</option>
                                                    <option value="cancelled" {{ $session->status === 'cancelled' ? 'selected' : '' }}>❌ Batalkan</option>
                                                </select>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Presensi Mandiri & Upload Foto Bukti Mentor -->
<div class="modal fade" id="mentorAttendanceModal" tabindex="-1" aria-labelledby="mentorAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-success text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-camera-fill fs-5"></i>
                    <h5 class="modal-title fw-bold fs-6 mb-0" id="mentorAttendanceModalLabel">Input Presensi &amp; Bukti Foto Sesi</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="attendanceProofForm" method="POST" enctype="multipart/form-data" class="p-4">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary mb-1">Santri Binaan</label>
                    <div class="p-2.5 bg-light rounded-3 border fw-semibold text-dark" id="modalStudentName">-</div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-secondary mb-1">Tanggal Sesi <span class="text-danger">*</span></label>
                        <input type="date" name="date" id="modalDate" class="form-control form-control-sm rounded-3" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-secondary mb-1">Waktu Sesi <span class="text-danger">*</span></label>
                        <input type="time" name="time" id="modalTime" class="form-control form-control-sm rounded-3" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary mb-1">Status Kehadiran <span class="text-danger">*</span></label>
                    <select name="status" id="modalStatus" class="form-select form-select-sm rounded-3" required>
                        <option value="hadir">✅ Hadir (Selesai Bimbingan - Terhitung Honor)</option>
                        <option value="izin">⚠️ Izin (Santri/Wali Berhalangan)</option>
                        <option value="sakit">🩹 Sakit (Santri Berhalangan)</option>
                    </select>
                    <small class="text-muted" style="font-size: 0.72rem;">*Status Hadir otomatis menyelesaikan sesi dan menambahkan Rp 100.000 ke Slip Honor Anda.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary mb-1">
                        <i class="bi bi-camera me-1 text-success"></i>Upload Bukti Foto Lokasi / Sesi
                    </label>
                    <input type="file" name="proof_image" class="form-control form-control-sm rounded-3" accept="image/*">
                    <small class="text-muted" style="font-size: 0.72rem;">
                        Foto dokumentasi saat mengajar di rumah murid atau tangkapan layar jika daring (JPG/PNG/WEBP, Maks. 5MB).
                    </small>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary mb-1">Catatan Pengajaran / Evaluasi</label>
                    <textarea name="notes" id="modalNotes" class="form-control form-control-sm rounded-3" rows="3" placeholder="Contoh: Sesi berjalan lancar, ananda setoran surat Al-Mulk ayat 1-10 dengan tajwid fasih."></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success text-white rounded-pill px-4 fw-semibold shadow-xs">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Presensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAttendanceModal(sessionId, studentName, sessionDate, sessionTime, currentStatus, currentNotes) {
        const form = document.getElementById('attendanceProofForm');
        form.action = "{{ url('/mentor/sessions') }}/" + sessionId + "/confirm-attendance";

        document.getElementById('modalStudentName').innerText = studentName;
        document.getElementById('modalDate').value = sessionDate || '';
        document.getElementById('modalTime').value = sessionTime || '16:00';
        document.getElementById('modalStatus').value = currentStatus || 'hadir';
        document.getElementById('modalNotes').value = currentNotes || '';

        const modal = new bootstrap.Modal(document.getElementById('mentorAttendanceModal'));
        modal.show();
    }
</script>
@endsection
