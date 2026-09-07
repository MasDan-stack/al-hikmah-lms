@extends('layouts.mentor')

@section('title', 'Pengaturan Ketersediaan Mengajar')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-calendar-week-fill text-success me-2"></i>Pengaturan Ketersediaan Mengajar
            </h3>
            <p class="text-muted small mb-0">Nama Pengajar: <strong>{{ $mentor?->getDisplayName() ?? auth()->user()->name }}</strong>. Tentukan angka slot jam kosong Anda dari Senin s/d Ahad.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-success rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#importWaModal">
                <i class="bi bi-whatsapp me-1"></i> Import Format WhatsApp
            </button>
            <button type="button" class="btn btn-light border rounded-pill px-3 shadow-sm" id="btnCopyWa">
                <i class="bi bi-clipboard-check me-1"></i> Salin Format WhatsApp
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
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

    <!-- Keterangan Angka Slot Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                <span class="fw-bold text-dark small"><i class="bi bi-clock-history text-primary me-1"></i>Keterangan Angka Slot Jam Bimbingan:</span>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btnCopyMondayToAll">
                    <i class="bi bi-copy me-1"></i> Salin Jadwal Senin ke Seluruh Hari Kerja (Selasa–Jum'at)
                </button>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @foreach($slotMap as $num => $s)
                    <div class="badge bg-light text-dark border p-2 rounded-3 text-start">
                        <span class="badge bg-dark text-white me-1">{{ $num }}</span>
                        <strong>{{ $s['time'] }} WIB</strong>
                        <span class="text-muted d-block" style="font-size: 0.68rem;">{{ $s['desc'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Form Pengaturan Slot Harian -->
    <form action="{{ route('mentor.availability.store') }}" method="POST">
        @csrf
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-check2-square text-success me-2"></i>Pilih Angka Slot Jam Kosong per Hari
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary small text-uppercase">
                            <tr>
                                <th class="ps-4" style="width: 160px;">Hari</th>
                                <th>Pilihan Slot Jam Buka Mengajar (0 s/d 6)</th>
                                <th class="text-center" style="width: 220px;">Status & Santri Aktif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalOpenSlots = 0;
                                $totalFilledStudents = 0;
                            @endphp
                            @foreach($days as $day)
                                @php
                                    $avail = $availabilities->get($day);
                                    $checkedSlots = $avail?->slot_numbers ?? [];
                                    $totalOpenSlots += count($checkedSlots);
                                    $dayStudents = $assignedRows->get($day) ?? collect();
                                    $dayStudentCount = $dayStudents->count();
                                    $totalFilledStudents += $dayStudentCount;
                                    $maxQuota = $avail?->max_students ?? $mentor?->default_max_students_per_day ?? 5;
                                @endphp
                                <tr id="row_{{ $day }}">
                                    <td class="ps-4 fw-bold text-dark align-top pt-3">
                                        <div class="fs-6">{{ $dayLabels[$day] }}</div>
                                        <span class="text-muted small font-monospace" style="font-size: 0.72rem;">{{ strtoupper($day) }}</span>
                                        
                                        <!-- Toggle Hari Bebas / Libur -->
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input holiday-toggle" 
                                                   type="checkbox" 
                                                   name="days[{{ $day }}][is_holiday]" 
                                                   id="holiday_{{ $day }}" 
                                                   value="1" 
                                                   {{ ($avail?->is_holiday ?? false) ? 'checked' : '' }} 
                                                   data-day="{{ $day }}">
                                            <label class="form-check-label small fw-semibold text-danger" for="holiday_{{ $day }}" style="font-size: 0.75rem;">
                                                <i class="bi bi-calendar-x me-1"></i>Hari Libur
                                            </label>
                                        </div>
                                        <input type="text" 
                                               name="days[{{ $day }}][notes]" 
                                               class="form-control form-control-sm mt-1 holiday-notes-{{ $day }} {{ ($avail?->is_holiday ?? false) ? '' : 'd-none' }}" 
                                               placeholder="Alasan libur / hari bebas..." 
                                               value="{{ $avail?->notes ?? '' }}" 
                                               style="font-size: 0.72rem;">
                                    </td>
                                    <td class="pt-3">
                                        <!-- Slot Checkboxes (0 to 6) -->
                                        <div class="d-flex flex-wrap gap-2 align-items-center mb-2 slot-container-{{ $day }}">
                                            @foreach($slotMap as $num => $slot)
                                                @php
                                                    $isChecked = in_array($num, $checkedSlots, true);
                                                    $studentsInThisSlot = $dayStudents->where('slot_number', $num);
                                                @endphp
                                                <div class="form-check form-check-inline bg-light border rounded-3 px-2 py-1 m-0 d-flex align-items-center gap-1 slot-wrapper-{{ $day }}">
                                                    <input class="form-check-input slot-cb slot-cb-{{ $day }}" 
                                                           type="checkbox" 
                                                           name="days[{{ $day }}][slots][]" 
                                                           id="slot_{{ $day }}_{{ $num }}" 
                                                           value="{{ $num }}" 
                                                           {{ $isChecked ? 'checked' : '' }}>
                                                    <label class="form-check-label small fw-semibold cursor-pointer" for="slot_{{ $day }}_{{ $num }}">
                                                        <span class="badge bg-white text-dark border me-1">{{ $num }}</span>{{ $slot['time'] }}
                                                    </label>
                                                    @if($studentsInThisSlot->count() > 0)
                                                        <span class="badge bg-primary text-white rounded-pill px-1 ms-1" style="font-size: 0.65rem;" title="{{ $studentsInThisSlot->count() }} Santri Aktif">
                                                            {{ $studentsInThisSlot->count() }} 👤
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Quick Batch Actions per Row -->
                                        <div class="d-flex flex-wrap gap-1 quick-actions-{{ $day }}">
                                            <button type="button" class="btn btn-light btn-xs border text-secondary btn-quick-slot" data-day="{{ $day }}" data-slots="1,2">🌅 Pagi (1-2)</button>
                                            <button type="button" class="btn btn-light btn-xs border text-secondary btn-quick-slot" data-day="{{ $day }}" data-slots="3,4">🌤️ Siang (3-4)</button>
                                            <button type="button" class="btn btn-light btn-xs border text-secondary btn-quick-slot" data-day="{{ $day }}" data-slots="5,6">🌙 Malam (5-6)</button>
                                            <button type="button" class="btn btn-light btn-xs border text-secondary btn-quick-slot" data-day="{{ $day }}" data-slots="1,2,3,4,5,6">⚡ Semua (1-6)</button>
                                            <button type="button" class="btn btn-light btn-xs border text-danger btn-clear-slot" data-day="{{ $day }}">❌ Kosongkan</button>
                                        </div>

                                        <!-- Detail Santri Terdaftar di Hari Ini -->
                                        @if($dayStudents->isNotEmpty())
                                            <div class="mt-2 p-2 rounded-3 bg-light border">
                                                <span class="text-primary fw-bold small d-block mb-1" style="font-size: 0.72rem;">
                                                    <i class="bi bi-people-fill me-1"></i>Santri Aktif Terdaftar ({{ $dayStudents->count() }} Murid):
                                                </span>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($dayStudents as $stRow)
                                                        <span class="badge bg-white text-dark border px-2 py-1 small">
                                                            👤 <strong>{{ $stRow->student_name }}</strong> 
                                                            <span class="text-muted">({{ $stRow->program_name ?? 'Tahfidz' }} - Slot {{ $stRow->slot_number }} / {{ $slotMap[$stRow->slot_number]['time'] ?? '16:00' }} WIB)</span>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center align-top pt-3 status-cell-{{ $day }}">
                                        @if($avail?->is_holiday)
                                            <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-bold">
                                                🔴 Hari Bebas (Libur)
                                            </span>
                                        @elseif(empty($checkedSlots))
                                            <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">⚪ Kosong / Tidak Aktif</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold">
                                                👥 {{ $dayStudentCount }} Santri Terisi
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2">
                    <label class="fw-semibold small text-secondary mb-0">Kuota Maks Santri per Slot:</label>
                    <input type="number" name="max_students" class="form-control form-control-sm text-center" style="width: 80px;" value="{{ $mentor?->default_max_students_per_day ?? 5 }}" min="1" max="20">
                    <span class="text-muted small">Santri / Slot</span>
                </div>
                <button type="submit" class="btn btn-success-custom fw-bold px-4 rounded-pill shadow-sm">
                    <i class="bi bi-save me-1"></i> Simpan Ketersediaan Mengajar
                </button>
            </div>
        </div>
    </form>

    <!-- Ringkasan Statistik Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-3">
                <i class="bi bi-info-circle text-primary me-2"></i>Ringkasan Ketersediaan & Beban Mengajar Anda
            </h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-4 border d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small d-block">Total Slot Terbuka / Pekan</span>
                            <h4 class="fw-bold text-success mb-0">{{ $totalOpenSlots }} Slot Jam</h4>
                        </div>
                        <i class="bi bi-calendar-check text-success fs-1"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-4 border d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small d-block">Total Santri Binaan Terisi</span>
                            <h4 class="fw-bold text-primary mb-0">{{ $totalFilledStudents }} Santri Aktif</h4>
                        </div>
                        <i class="bi bi-people-fill text-primary fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import Format WhatsApp -->
<div class="modal fade" id="importWaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-success">
                    <i class="bi bi-whatsapp me-2"></i>Import Jadwal dari Pesan WhatsApp
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('mentor.availability.import-wa') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-2">Paste format chat WhatsApp jadwal ketersediaan Anda di bawah ini:</p>
                    <textarea class="form-control font-monospace small" name="raw_text" rows="10" placeholder="Nama : {{ $mentor?->getDisplayName() ?? 'MENTOR' }}

senin : 1 2 3 4
selasa : 5
Rabu : 1 2 3 4
Kamis : 1 2 3 4 5 6
Jumat : 2 3 5 6
Sabtu : 3 4 5
Ahad : 1 2 3 4 5 6" required></textarea>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success-custom fw-bold px-4 rounded-pill shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Proses & Terapkan Jadwal
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
    // Fungsi untuk memperbarui tampilan baris saat status libur berubah
    function updateHolidayState(toggle) {
        const day = toggle.getAttribute('data-day');
        const isHoliday = toggle.checked;
        const notesInput = document.querySelector(`.holiday-notes-${day}`);
        const slotCbs = document.querySelectorAll(`.slot-cb-${day}`);
        const quickButtons = document.querySelectorAll(`.quick-actions-${day} button`);
        const row = document.getElementById(`row_${day}`);

        if (notesInput) {
            notesInput.classList.toggle('d-none', !isHoliday);
        }

        slotCbs.forEach(cb => {
            cb.disabled = isHoliday;
            if (isHoliday) {
                cb.checked = false;
            }
        });

        quickButtons.forEach(btn => {
            btn.disabled = isHoliday;
        });

        if (row) {
            if (isHoliday) {
                row.classList.add('table-light');
            } else {
                row.classList.remove('table-light');
            }
        }
    }

    // Pasang listener pada seluruh toggle hari libur
    document.querySelectorAll('.holiday-toggle').forEach(function (toggle) {
        // Inisialisasi awal
        updateHolidayState(toggle);

        toggle.addEventListener('change', function () {
            updateHolidayState(this);
        });
    });

    // Tombol batch slot per baris (Pagi, Siang, Malam, Semua)
    document.querySelectorAll('.btn-quick-slot').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const day = this.getAttribute('data-day');
            const holidayToggle = document.getElementById(`holiday_${day}`);
            if (holidayToggle && holidayToggle.checked) return;

            const slots = this.getAttribute('data-slots').split(',').map(s => parseInt(s.trim()));
            document.querySelectorAll(`.slot-cb-${day}`).forEach(function (cb) {
                cb.checked = slots.includes(parseInt(cb.value));
            });
        });
    });

    // Tombol kosongkan slot per baris
    document.querySelectorAll('.btn-clear-slot').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const day = this.getAttribute('data-day');
            document.querySelectorAll(`.slot-cb-${day}`).forEach(function (cb) {
                cb.checked = false;
            });
        });
    });

    // Salin Jadwal Senin ke Seluruh Hari Kerja
    const btnCopyMonday = document.getElementById('btnCopyMondayToAll');
    if (btnCopyMonday) {
        btnCopyMonday.addEventListener('click', function () {
            const mondayHoliday = document.getElementById('holiday_monday')?.checked;
            const mondaySlots = [];
            document.querySelectorAll('.slot-cb-monday:checked').forEach(function (cb) {
                mondaySlots.push(parseInt(cb.value));
            });

            ['tuesday', 'wednesday', 'thursday', 'friday'].forEach(function (day) {
                const dayHolidayToggle = document.getElementById(`holiday_${day}`);
                if (dayHolidayToggle) {
                    dayHolidayToggle.checked = !!mondayHoliday;
                    updateHolidayState(dayHolidayToggle);
                }

                if (!mondayHoliday) {
                    document.querySelectorAll(`.slot-cb-${day}`).forEach(function (cb) {
                        cb.checked = mondaySlots.includes(parseInt(cb.value));
                    });
                }
            });

            alert('✅ Jadwal hari Senin berhasil disalin ke hari Selasa s/d Jum\'at!');
        });
    }

    // Salin Teks Format WhatsApp ke Clipboard
    const btnCopyWa = document.getElementById('btnCopyWa');
    if (btnCopyWa) {
        btnCopyWa.addEventListener('click', function () {
            const textToCopy = @json($whatsappText);
            if (navigator.clipboard) {
                navigator.clipboard.writeText(textToCopy).then(() => {
                    alert('📋 Format WhatsApp berhasil disalin ke clipboard! Siap dipaste ke grup WhatsApp.');
                });
            } else {
                const tempInput = document.createElement('textarea');
                tempInput.value = textToCopy;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                alert('📋 Format WhatsApp berhasil disalin ke clipboard!');
            }
        });
    }
});
</script>
<style>
.btn-xs {
    padding: 2px 8px;
    font-size: 0.72rem;
    border-radius: 6px;
}
.cursor-pointer {
    cursor: pointer;
}
</style>
@endpush
