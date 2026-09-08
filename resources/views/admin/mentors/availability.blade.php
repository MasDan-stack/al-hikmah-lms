@extends('layouts.admin')

@section('title', 'Matriks Ketersediaan & Alokasi Guru')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-calendar3-range-fill text-success me-2"></i>Matriks Ketersediaan & Alokasi Guru
            </h3>
            <p class="text-muted small mb-0">Pantau jam mengajar aktif serta sisa jam kosong seluruh guru (0 s/d 6) untuk alokasi santri yang transparan dan akurat.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-success-custom fw-bold shadow-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#assignModal">
                <i class="bi bi-person-plus-fill me-1"></i> Alokasikan Santri Baru
            </button>
            <button type="button" onclick="window.print()" class="btn btn-light border rounded-pill px-3 shadow-sm">
                <i class="bi bi-printer me-1"></i> Cetak Matriks
            </button>
        </div>
    </div>

    <!-- Alert Feedback -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- ⚠️ NOTIFIKASI TULISAN: Guru yang Belum Mengisi Jadwal (Jadwal Kosong) -->
    @if($unfilledMentors->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-warning bg-opacity-10 border-start border-4 border-warning">
            <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-warning text-dark p-2 rounded-3 fs-5">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </span>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">
                            ⚠️ Notifikasi Peringatan Admin: Terdapat {{ $unfilledMentors->count() }} Pengajar Belum Mengisi Jadwal (Jadwal Kosong)
                        </h6>
                        <p class="text-secondary small mb-0">
                            Pengajar berikut belum menginput jadwal ketersediaan mengajarnya: 
                            <span class="badge bg-white text-danger border fw-bold">{{ $unfilledMentors->map(fn($m)=>$m->getDisplayName())->implode(', ') }}</span>. 
                            Seluruh jam di hari Senin s/d Ahad berstatus <strong>Kosong Tidak Mengajar</strong>.
                        </p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small">
                        <i class="bi bi-info-circle text-primary me-1"></i>{{ count($matrix) - $unfilledMentors->count() }} dari {{ count($matrix) }} Guru Sudah Mengisi
                    </span>
                </div>
            </div>
        </div>
    @endif

    <!-- 🏖️ INFO HARI BEBAS / LIBUR GURU -->
    @php
        $holidayMentors = collect($matrix)->filter(function($item) {
            return collect($item['schedule'])->contains(fn($sch) => $sch['availability']?->is_holiday);
        });
    @endphp
    @if($holidayMentors->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary bg-opacity-10 border-start border-4 border-primary">
            <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-primary text-white p-2 rounded-3 fs-5">
                        <i class="bi bi-calendar-x-fill"></i>
                    </span>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">
                            🏖️ Jadwal Hari Bebas / Libur Guru Terdaftar: {{ $holidayMentors->count() }} Pengajar Memilih Hari Libur
                        </h6>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            @foreach($holidayMentors as $hm)
                                @php
                                    $hDays = collect($hm['schedule'])->filter(fn($s) => $s['availability']?->is_holiday)->keys()->map(fn($d) => $dayLabels[$d] ?? $d)->implode(', ');
                                @endphp
                                <span class="badge bg-white text-dark border shadow-xs">
                                    <strong class="text-primary">{{ $hm['mentor']->getDisplayName() }}</strong>: Libur {{ $hDays }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Alert Santri Belum Teralokasi & Permintaan Jadwal Baru -->
    @if($unassignedStudents->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 bg-info-subtle mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-person-exclamation text-info fs-2"></i>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Terdapat {{ $unassignedStudents->count() }} Santri Membutuhkan Guru Pengampu</h6>
                            <p class="text-secondary small mb-0">Klik tombol <strong>Alokasikan</strong> untuk mencocokkan jadwal santri ke guru yang membuka jam tersebut.</p>
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    @foreach($unassignedStudents->take(6) as $st)
                        @php
                            $enr = $st->enrollments->first();
                            $reqDays = $enr?->requested_days ? implode(', ', array_map(fn($d) => \App\Models\MentorAvailability::DAYS[$d] ?? $d, $enr->requested_days)) : 'Fleksibel';
                            $reqTime = $enr?->requested_time ? substr($enr->requested_time, 0, 5) . ' WIB' : 'Fleksibel';
                        @endphp
                        <div class="col-md-4 col-sm-6">
                            <div class="card border-0 rounded-4 p-3 bg-white shadow-sm h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-dark">{{ $st->getDisplayName() }}</span>
                                        <span class="badge bg-light text-dark border">{{ $enr?->program->name ?? 'Tahfidz' }}</span>
                                    </div>
                                    <div class="small text-muted mb-2">
                                        <div><i class="bi bi-calendar-event me-1 text-primary"></i>Minta Hari: <strong>{{ $reqDays }}</strong></div>
                                        <div><i class="bi bi-clock me-1 text-warning"></i>Minta Jam: <strong>{{ $reqTime }}</strong></div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill w-100 fw-semibold btn-quick-assign" 
                                    data-student-id="{{ $st->id }}"
                                    data-student-name="{{ $st->getDisplayName() }}"
                                    data-program-id="{{ $enr?->program_id }}"
                                    data-preferred-day="{{ $enr?->requested_days[0] ?? 'monday' }}">
                                    <i class="bi bi-person-check-fill me-1"></i>Alokasikan Santri Ini
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Search & Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('admin.mentors.availability') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control border-start-0" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama pengajar atau spesialisasi...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm" name="day" onchange="this.form.submit()">
                        <option value="">Semua Hari (Senin - Ahad)</option>
                        @foreach($days as $d)
                            <option value="{{ $d }}" {{ ($filterDay ?? '') === $d ? 'selected' : '' }}>Hari {{ $dayLabels[$d] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm" name="slot" onchange="this.form.submit()">
                        <option value="">Semua Slot Waktu (0 - 6)</option>
                        @foreach($slotMap as $num => $s)
                            <option value="{{ $num }}" {{ (string)($filterSlot ?? '') === (string)$num ? 'selected' : '' }}>
                                Slot {{ $num }} ({{ $s['time'] }} - {{ $s['desc'] }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 w-100">Filter</button>
                    @if($search || $filterDay || $filterSlot !== null)
                        <a href="{{ route('admin.mentors.availability') }}" class="btn btn-sm btn-light rounded-pill px-2" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Legend Indicator Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-3 bg-white">
        <div class="card-body py-2 px-4 d-flex align-items-center gap-3 flex-wrap small">
            <span class="fw-bold text-dark me-2"><i class="bi bi-palette-fill me-1 text-primary"></i>Keterangan Warna & Indikator:</span>
            <span class="badge bg-success text-white px-3 py-1 rounded-pill">
                🟢 Buka Mengajar (Ada Kuota)
            </span>
            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill">
                🟡 Hampir Penuh (Sisa 1)
            </span>
            <span class="badge bg-danger text-white px-3 py-1 rounded-pill">
                🔴 Penuh (Kapasitas Maks)
            </span>
            <span class="badge bg-light text-danger border px-3 py-1 rounded-pill">
                ❌ Jam Kosong (Tidak Mengajar)
            </span>
        </div>
    </div>

    <!-- Matriks Ketersediaan Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-grid-3x3 text-success me-2"></i>Matriks 7 Hari Slot Jam Mengajar & Sisa Jam Kosong
            </h6>
            <span class="badge bg-light text-secondary border">Total Pengajar Aktif: {{ count($matrix) }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table class="table table-bordered align-middle text-center mb-0" style="min-width: 1050px;">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="text-start ps-3" style="width: 230px;">NAMA PENGAJAR</th>
                            @foreach($days as $day)
                                <th style="min-width: 155px;">{{ $dayLabels[$day] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($matrix as $mentorId => $data)
                            @php
                                $mentor = $data['mentor'];
                                $schedule = $data['schedule'];
                                $totalSlots = $data['total_active_slots'];
                            @endphp
                            <tr>
                                <td class="text-start ps-3 py-3 bg-light bg-opacity-25 align-top">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $mentor->getDisplayName() }}</div>
                                            <small class="text-muted d-block">{{ $mentor->specialization ?? 'Al-Qur\'an' }}</small>
                                        </div>
                                        @if($totalSlots === 0)
                                            <span class="badge bg-danger text-white rounded-pill" style="font-size: 0.65rem;">
                                                Belum Isi
                                            </span>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mt-2">
                                        <span class="badge bg-light text-secondary border" style="font-size: 0.68rem;">
                                            <i class="bi bi-telephone me-1"></i>{{ $mentor->user?->phone ?? '-' }}
                                        </span>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold" style="font-size: 0.68rem;">
                                            {{ $totalSlots }} Jam Mengajar
                                        </span>
                                    </div>
                                </td>
                                @foreach($days as $day)
                                    @php
                                        $dayInfo = $schedule[$day];
                                        $statusType = $dayInfo['status_type'];
                                        $isAvail = $dayInfo['is_available'];
                                        $slots = $dayInfo['slots'];
                                        $emptySlots = $dayInfo['empty_slots'];
                                    @endphp
                                    <td class="p-2 align-top text-start">
                                        @if($statusType === 'unfilled')
                                            <!-- ⚠️ NOTIFIKASI TULISAN: BELUM DIISI (SEMUA JAM KOSONG) -->
                                            <div class="p-2 rounded-3 bg-warning-subtle border border-warning-subtle text-start">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="badge bg-warning text-dark px-2 py-0" style="font-size: 0.68rem;">
                                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Belum Diisi
                                                    </span>
                                                </div>
                                                <div class="text-danger fw-bold" style="font-size: 0.68rem;">
                                                    <i class="bi bi-x-circle-fill me-1"></i>Semua Jam Kosong:
                                                </div>
                                                <div class="text-muted" style="font-size: 0.65rem; line-height: 1.3;">
                                                    0, 1, 2, 3, 4, 5, 6 (05:00 - 20:00)
                                                </div>
                                            </div>
                                        @elseif($statusType === 'empty')
                                            <!-- ⚪ LIBUR / TIDAK ADA SLOT MENGAJAR (SEMUA JAM KOSONG) -->
                                            <div class="p-2 rounded-3 bg-secondary-subtle border border-secondary-subtle text-start">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    @if($dayInfo['availability']?->is_holiday)
                                                        <span class="badge bg-warning text-dark px-2 py-0" style="font-size: 0.68rem;">
                                                            <i class="bi bi-sun-fill me-1"></i>Hari Bebas (Libur)
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary text-white px-2 py-0" style="font-size: 0.68rem;">
                                                            <i class="bi bi-dash-circle me-1"></i>Libur
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($dayInfo['availability']?->notes)
                                                    <div class="text-secondary small fst-italic mb-1" style="font-size: 0.65rem;">
                                                        "{{ $dayInfo['availability']->notes }}"
                                                    </div>
                                                @endif
                                                <div class="text-danger fw-bold" style="font-size: 0.68rem;">
                                                    <i class="bi bi-x-circle-fill me-1"></i>Semua Jam Kosong:
                                                </div>
                                                <div class="text-muted" style="font-size: 0.65rem; line-height: 1.3;">
                                                    0, 1, 2, 3, 4, 5, 6 (Tidak Mengajar)
                                                </div>
                                            </div>
                                        @else
                                            <!-- 🟢 DAFTAR SLOT BUKA MENGAJAR & SISA JAM KOSONG -->
                                            <div class="d-flex flex-column gap-1">
                                                <!-- Bagian Jam Mengajar -->
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-success fw-bold" style="font-size: 0.68rem;">
                                                        <i class="bi bi-check-circle-fill me-1"></i>Buka ({{ count($slots) }} Jam):
                                                    </span>
                                                </div>

                                                @foreach($slots as $slotNum => $sd)
                                                    @php
                                                        $badgeClass = $sd['is_full'] 
                                                            ? 'bg-danger text-white' 
                                                            : ($sd['is_almost_full'] ? 'bg-warning text-dark' : 'bg-success text-white');
                                                        
                                                        // Buat HTML content untuk popover santri
                                                        $studentsHtml = '';
                                                        if($sd['count'] > 0) {
                                                            $studentsHtml = '<ul class="list-unstyled mb-0 small text-start">';
                                                            foreach($sd['students'] as $st) {
                                                                $studentsHtml .= '<li>• <strong>'.e($st->student_name).'</strong> ('.e($st->program_name ?? 'Tahfidz').')</li>';
                                                            }
                                                            $studentsHtml .= '</ul>';
                                                        } else {
                                                            $studentsHtml = '<span class="small text-muted fst-italic">Belum ada santri teralokasi (Slot Kosong).</span>';
                                                        }
                                                    @endphp
                                                    <div class="badge {{ $badgeClass }} p-1 px-2 rounded-2 text-start d-flex justify-content-between align-items-center shadow-xs cursor-pointer btn-slot-popover"
                                                         data-bs-toggle="popover"
                                                         data-bs-trigger="hover focus"
                                                         data-bs-html="true"
                                                         title="Slot {{ $slotNum }} ({{ $sd['time'] }} WIB)"
                                                         data-bs-content="{{ $studentsHtml }}"
                                                         data-mentor-id="{{ $mentor->id }}"
                                                         data-day="{{ $day }}"
                                                         data-slot="{{ $slotNum }}">
                                                        <span><strong>{{ $slotNum }}️⃣</strong> {{ $sd['time'] }}</span>
                                                        <span class="badge bg-white text-dark rounded-pill px-2 py-0 ms-1" style="font-size: 0.68rem;">
                                                            {{ $sd['count'] }}/{{ $sd['max'] }}
                                                        </span>
                                                    </div>
                                                @endforeach

                                                <!-- Bagian Sisa Jam Kosong di Hari Tersebut -->
                                                @if(!empty($emptySlots))
                                                    <div class="mt-1 pt-1 border-top">
                                                        <span class="text-danger fw-bold d-block mb-1" style="font-size: 0.65rem;">
                                                            <i class="bi bi-x-circle-fill me-1"></i>Sisa Jam Kosong:
                                                        </span>
                                                        <div class="d-flex flex-wrap gap-1">
                                                            @foreach($emptySlots as $eNum => $eInfo)
                                                                <span class="badge bg-light text-danger border" style="font-size: 0.62rem; padding: 2px 4px;" title="Jam Kosong: {{ $eInfo['desc'] }}">
                                                                    {{ $eNum }}️⃣ {{ $eInfo['time'] }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-5 text-muted">Belum ada data pengajar yang cocok dengan kriteria filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form Alokasi Santri Baru -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-success">
                    <i class="bi bi-person-plus-fill me-2"></i>Alokasi Santri ke Guru Pengajar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.mentors.assign-student') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">Tentukan hari dan angka slot jam belajar santri. Dropdown pengajar akan secara cerdas merekomendasikan guru yang membuka slot tersebut.</p>

                    <!-- Pilih Santri -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Pilih Santri <span class="text-danger">*</span></label>
                        <select class="form-select" id="selectStudent" name="student_id" required>
                            <option value="">-- Pilih Santri --</option>
                            @foreach($unassignedStudents as $st)
                                @php
                                    $enr = $st->enrollments->first();
                                    $reqInfo = $enr ? " (Minta: " . ($enr->requested_days ? implode('/', array_map(fn($d)=>\App\Models\MentorAvailability::DAYS[$d]??$d, $enr->requested_days)) : 'Bebas') . ", " . ($enr->requested_time ? substr($enr->requested_time, 0, 5) : 'Bebas') . ")" : '';
                                @endphp
                                <option value="{{ $st->id }}" data-program-id="{{ $enr?->program_id }}" data-day="{{ $enr?->requested_days[0] ?? 'monday' }}">
                                    {{ $st->getDisplayName() }} - {{ $enr?->program->name ?? 'Tahfidz' }}{{ $reqInfo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pilih Hari & Angka Slot -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Hari Belajar <span class="text-danger">*</span></label>
                            <select class="form-select" id="selectDay" name="day" required>
                                <option value="">-- Pilih Hari --</option>
                                @foreach($days as $day)
                                    <option value="{{ $day }}">{{ $dayLabels[$day] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Pilih Slot Jam <span class="text-danger">*</span></label>
                            <select class="form-select" id="selectSlot" name="slot_number" required>
                                <option value="">-- Pilih Slot --</option>
                                @foreach($slotMap as $num => $s)
                                    <option value="{{ $num }}" {{ $num === 5 ? 'selected' : '' }}>
                                        {{ $num }}️⃣ {{ $s['time'] }} ({{ $s['desc'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Pilih Pengajar / Mentor -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Pilih Guru Pengampu <span class="text-danger">*</span></label>
                        <select class="form-select" id="selectMentor" name="mentor_id" required>
                            <option value="">-- Pilih Pengajar --</option>
                            @foreach($matrix as $mentorId => $data)
                                @php $m = $data['mentor']; @endphp
                                <option value="{{ $m->id }}">{{ $m->getDisplayName() }} ({{ $m->specialization ?? 'Al-Qur\'an' }})</option>
                            @endforeach
                        </select>
                        <div id="mentorSlotHint" class="form-text small text-success mt-1 d-none">
                            <i class="bi bi-check-circle me-1"></i>Ditemukan guru yang membuka slot ini dan memiliki sisa kuota.
                        </div>
                    </div>

                    <!-- Pilih Program & Catatan -->
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Program Belajar</label>
                            <select class="form-select" id="selectProgram" name="program_id">
                                <option value="">-- Standar Program --</option>
                                @foreach($programs as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Catatan Sesi</label>
                            <input type="text" class="form-control" name="notes" placeholder="Misal: Bimbingan via Zoom">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success-custom fw-bold px-4 rounded-pill shadow-sm">
                        <i class="bi bi-person-check-fill me-1"></i> Simpan Alokasi Santri
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi Popover Bootstrap
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    const selectStudent = document.getElementById('selectStudent');
    const selectDay = document.getElementById('selectDay');
    const selectSlot = document.getElementById('selectSlot');
    const selectMentor = document.getElementById('selectMentor');
    const selectProgram = document.getElementById('selectProgram');
    const hintEl = document.getElementById('mentorSlotHint');

    // Quick Assign Button Handler
    document.querySelectorAll('.btn-quick-assign').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const studentId = this.getAttribute('data-student-id');
            const programId = this.getAttribute('data-program-id');
            const day = this.getAttribute('data-preferred-day');

            if (selectStudent) selectStudent.value = studentId;
            if (selectProgram && programId) selectProgram.value = programId;
            if (selectDay && day) selectDay.value = day;

            fetchAvailableMentors();

            const modal = new bootstrap.Modal(document.getElementById('assignModal'));
            modal.show();
        });
    });

    // Auto-update mentor dropdown saat hari atau slot berubah
    function fetchAvailableMentors() {
        const day = selectDay ? selectDay.value : '';
        const slot = selectSlot ? selectSlot.value : '';
        const studentId = selectStudent ? selectStudent.value : '';

        if (!day || slot === '') return;

        let url = `{{ route('admin.mentors.available-api') }}?day=${day}&slot=${slot}`;
        if (studentId) {
            url += `&student_id=${encodeURIComponent(studentId)}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.mentors && selectMentor) {
                    const currentVal = selectMentor.value;
                    selectMentor.innerHTML = '<option value="">-- Pilih Pengajar --</option>';

                    data.mentors.forEach(m => {
                        const opt = document.createElement('option');
                        opt.value = m.id;
                        const matchTag = m.match_score ? ` [Kesesuaian: ${m.match_score}%]` : '';
                        opt.textContent = `${m.name} (${m.specialization}) - [Sisa ${m.remaining} dari ${m.max} Kuota]${matchTag}`;
                        if (currentVal == m.id) opt.selected = true;
                        selectMentor.appendChild(opt);
                    });

                    if (hintEl) {
                        hintEl.classList.remove('d-none');
                        const infoStudent = data.student ? ` untuk Santri <strong>${data.student.name}</strong> (${data.student.gender === 'male' ? 'Laki-laki' : 'Perempuan'}, ${data.student.age} th)` : '';
                        hintEl.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i>Ditemukan <strong>${data.mentors.length}</strong> guru yang memenuhi kriteria syariat & membuka Slot ${slot} (${data.time} WIB)${infoStudent}.`;
                    }
                }
            })
            .catch(err => console.error(err));
    }

    if (selectDay) selectDay.addEventListener('change', fetchAvailableMentors);
    if (selectSlot) selectSlot.addEventListener('change', fetchAvailableMentors);
    if (selectStudent) {
        selectStudent.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            const day = opt.getAttribute('data-day');
            const programId = opt.getAttribute('data-program-id');

            if (day && selectDay) selectDay.value = day;
            if (programId && selectProgram) selectProgram.value = programId;

            fetchAvailableMentors();
        });
    }
});
</script>
<style>
.cursor-pointer {
    cursor: pointer;
}
.shadow-xs {
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
</style>
@endpush
