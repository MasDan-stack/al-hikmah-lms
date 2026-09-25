@extends('layouts.mentor')

@section('title', 'Presensi & Upload Bukti Hadir | Sesi #' . $session->id)
@section('header', 'Presensi Mandiri Guru')
@section('subheader', 'Konfirmasi kehadiran bimbingan santri dan unggah bukti foto saat orang tua lupa absen di website')

@section('content')
<div class="container-fluid p-0">
    <!-- Breadcrumb & Back Navigation -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('mentor.sessions.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-xs">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Sesi
            </a>
            <span class="text-muted small">|</span>
            <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 font-monospace">
                Sesi #{{ $session->id }}
            </span>
        </div>
        <div>
            @if($session->status === 'completed')
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-semibold">
                    <i class="bi bi-check2-all me-1"></i> Sesi Telah Selesai
                </span>
            @elseif($session->status === 'cancelled')
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1.5 fw-semibold">
                    <i class="bi bi-x-circle me-1"></i> Sesi Dibatalkan
                </span>
            @else
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1.5 fw-semibold">
                    <i class="bi bi-clock-history me-1"></i> Sesi Terjadwal
                </span>
            @endif
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                <div>
                    <h6 class="fw-bold mb-0 text-success">Berhasil Disimpan!</h6>
                    <span class="small">{{ session('success') }}</span>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-start">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i>
                <div>
                    <h6 class="fw-bold mb-1 text-danger">Terdapat Kesalahan Pengisian:</h6>
                    <ul class="mb-0 small ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Info Banner Alur Koordinasi WhatsApp & Presensi Mandiri -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0d7a3e 0%, #064e3b 100%);">
        <div class="card-body p-4 text-white">
            <div class="d-flex align-items-start gap-3 flex-column flex-md-row justify-content-between">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle bg-white text-success p-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bi bi-camera-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-white">Presensi Mandiri Guru di Rumah Santri</h5>
                        <p class="mb-0 text-white-50 small" style="max-width: 780px; line-height: 1.5;">
                            Ketika orang tua berhalangan atau lupa mengonfirmasi kehadiran via web dan hanya berkoordinasi via WhatsApp, ustadz/ustazah dapat langsung menginput status kehadiran santri dan mengunggah foto dokumentasi di lokasi. Data kehadiran akan otomatis terhubung ke <strong>Slip Gaji Admin</strong> serta tersinkronisasi di <strong>Portal Wali Santri</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $student = $session->student;
        $parent = $student?->parent;
        $parentUser = $parent?->user;
        $activeEnrollment = $student?->enrollments?->first();
        $programName = $session->program?->name ?? $activeEnrollment?->program?->name ?? $student?->programs?->first()?->name ?? 'Bimbingan Privat Al-Hikmah';
        $parentPhone = $student?->getParentPhone() ?? $parentUser?->phone;
        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $parentPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }
    @endphp

    <div class="row g-4">
        <!-- Kolom Kiri: Detail Santri, Wali, & Lokasi -->
        <div class="col-lg-5">
            <!-- Kartu Santri & Bimbingan -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <h6 class="fw-bold text-dark border-bottom pb-3 mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-person-badge-fill text-success"></i> Informasi Santri Binaan
                </h6>

                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="user-avatar-badge rounded-circle text-white fw-bold d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" 
                         style="width: 52px; height: 52px; font-size: 1.1rem; background: linear-gradient(135deg, var(--primary) 0%, #064e3b 100%);">
                        {{ strtoupper(substr($student?->getDisplayName() ?? 'Santri', 0, 2)) }}
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">{{ $student?->getDisplayName() }}</h5>
                        <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.72rem;">
                                {{ $programName }}
                            </span>
                            @if($student?->age)
                                <span class="badge bg-light text-secondary border rounded-pill" style="font-size: 0.72rem;">
                                    {{ $student->age }} Tahun ({{ $student->gender === 'L' ? 'Ikhwan' : 'Akhwat' }})
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-light rounded-3 p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small">Wali Santri:</span>
                        <span class="fw-bold text-dark small">{{ $student?->parent_name ?? $parentUser?->name ?? 'Orang Tua / Wali' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Kontak WhatsApp:</span>
                        @if($cleanPhone)
                            <a href="https://wa.me/{{ $cleanPhone }}?text=Assalamu'alaikum%20Bapak/Ibu%20wali%20santri%20{{ urlencode($student?->getDisplayName() ?? '') }},%20saya%20konfirmasi%20sesi%20bimbingan%20Al-Qur'an%20ananda." 
                               target="_blank" 
                               class="badge bg-success text-white text-decoration-none rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-xs"
                               style="font-size: 0.72rem;">
                                <i class="bi bi-whatsapp"></i> {{ $parentPhone }}
                            </a>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </div>
                </div>

                <!-- Alamat & Navigasi Lokasi -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary mb-1">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>Alamat Rumah Santri:
                    </label>
                    <div class="p-3 bg-light rounded-3 border small text-dark">
                        {{ $student?->effective_address ?: ($student?->location ?: 'Alamat belum dilengkapi oleh wali santri.') }}
                    </div>
                    @if($student?->maps_link)
                        <div class="mt-2">
                            <a href="{{ $student->maps_link }}" target="_blank" rel="noopener noreferrer" 
                               class="btn btn-sm btn-outline-success rounded-pill w-100 fw-semibold d-inline-flex align-items-center justify-content-center gap-1 shadow-xs">
                                <i class="bi bi-pin-map-fill text-danger"></i> Buka Rute Peta (Google Maps / Waze)
                                <i class="bi bi-box-arrow-up-right ms-1" style="font-size: 0.7rem;"></i>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Metode Pembelajaran -->
                <div>
                    <label class="form-label small fw-bold text-secondary mb-1">Metode Pembelajaran:</label>
                    <div>
                        @if($session->method === 'offline')
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fs-6">
                                <i class="bi bi-house-door-fill me-1"></i> Offline (Home Visit)
                            </span>
                        @elseif($session->method === 'online')
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fs-6">
                                <i class="bi bi-camera-video-fill me-1"></i> Online Virtual
                            </span>
                        @else
                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-1.5 fs-6">
                                <i class="bi bi-arrow-repeat me-1"></i> Hybrid
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Kartu Status Presensi Saat Ini (Jika Ada) -->
            @if($confirmation)
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <h6 class="fw-bold text-dark border-bottom pb-3 mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-primary"></i> Data Presensi Tersimpan
                    </h6>

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted small">Status Kehadiran:</span>
                        @if($confirmation->status === 'hadir')
                            <span class="badge bg-success text-white rounded-pill px-3 py-1">
                                <i class="bi bi-check-circle-fill me-1"></i> Hadir
                            </span>
                        @elseif($confirmation->status === 'izin')
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1">
                                <i class="bi bi-info-circle-fill me-1"></i> Izin
                            </span>
                        @elseif($confirmation->status === 'sakit')
                            <span class="badge bg-danger text-white rounded-pill px-3 py-1">
                                <i class="bi bi-heart-pulse-fill me-1"></i> Sakit
                            </span>
                        @endif
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted small">Dikonfirmasi Oleh:</span>
                        <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1 font-monospace">
                            {{ $confirmation->confirmed_by === 'mentor' ? 'Ustadz / Guru Pembimbing' : 'Wali Santri' }}
                        </span>
                    </div>

                    @if($confirmation->verified_at)
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted small">Waktu Presensi:</span>
                            <small class="text-dark fw-semibold">
                                {{ $confirmation->verified_at->locale('id')->isoFormat('D MMMM Y, HH:mm') }} WIB
                            </small>
                        </div>
                    @endif

                    @if($confirmation->proof_image_url)
                        <div class="mt-3 pt-3 border-top">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-image text-success me-1"></i>Foto Bukti yang Telah Diunggah:
                            </label>
                            <div class="rounded-3 overflow-hidden border shadow-2xs text-center p-1 bg-light">
                                <a href="{{ $confirmation->proof_image_url }}" target="_blank" title="Klik untuk membuka ukuran asli">
                                    <img src="{{ $confirmation->proof_image_url }}" alt="Bukti Foto" class="img-fluid rounded-2 object-fit-contain" style="max-height: 220px;">
                                </a>
                            </div>
                            <small class="text-muted d-block text-center mt-1" style="font-size: 0.72rem;">
                                Klik foto di atas untuk melihat resolusi penuh
                            </small>
                        </div>
                    @endif

                    @if($confirmation->notes)
                        <div class="mt-3 pt-3 border-top">
                            <label class="form-label small fw-bold text-secondary mb-1">Catatan Tersimpan:</label>
                            <div class="p-2.5 bg-light rounded-3 small text-secondary fst-italic">
                                "{{ $confirmation->notes }}"
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Kolom Kanan: Form Presensi & Upload Foto -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="border-bottom pb-3 mb-4">
                    <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <i class="bi bi-clipboard2-check-fill text-success"></i> Formulir Presensi &amp; Bukti Foto
                    </h5>
                    <p class="text-muted small mb-0">
                        Isi form di bawah ini dan unggah foto dokumentasi saat berada di rumah santri untuk melengkapi presensi sesi.
                    </p>
                </div>

                <div class="alert alert-danger border-0 rounded-4 p-3 mb-4 d-flex align-items-start gap-3 shadow-xs">
                    <div class="rounded-circle bg-danger text-white p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                        <i class="bi bi-camera-fill fs-6"></i>
                    </div>
                    <div>
                        <strong class="d-block text-danger fw-bold fs-6">Wajib Unggah Bukti Foto Pengajaran</strong>
                        <p class="mb-0 text-danger-emphasis small" style="line-height: 1.45;">
                            Agar kehadiran santri diverifikasi valid dan hak honorarium mengajar Rp 100.000 masuk ke <strong>Bagian B (Rincian Kehadiran & Honor Persantri) Slip Gaji Admin</strong>, Ustadz/Ustadzah wajib mengunggah foto dokumentasi bimbingan di rumah santri.
                        </p>
                    </div>
                </div>

                <form action="{{ route('mentor.sessions.confirm-attendance.submit', $session->id) }}" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      id="formMentorAttendance">
                    @csrf

                    <!-- Field 1: Nama Santri (Readonly) -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary mb-1">
                            <i class="bi bi-person-check text-primary me-1"></i>Nama Santri
                        </label>
                        <input type="text" class="form-control rounded-3 bg-light fw-bold text-dark" value="{{ $student?->getDisplayName() }}" readonly>
                    </div>

                    <!-- Field 2: Tanggal & Waktu Sesi -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary mb-1">
                                <i class="bi bi-calendar-event text-success me-1"></i>Tanggal Sesi <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   name="date" 
                                   id="inputDate"
                                   class="form-control rounded-3" 
                                   value="{{ old('date', $session->date ? $session->date->format('Y-m-d') : now()->format('Y-m-d')) }}" 
                                   required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary mb-1">
                                <i class="bi bi-clock text-warning me-1"></i>Waktu Sesi (WIB) <span class="text-danger">*</span>
                            </label>
                            <input type="time" 
                                   name="time" 
                                   id="inputTime"
                                   class="form-control rounded-3" 
                                   value="{{ old('time', $session->time ? date('H:i', strtotime($session->time)) : '16:00') }}" 
                                   required>
                        </div>
                    </div>

                    <!-- Field 3: Status Kehadiran -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary mb-2">
                            <i class="bi bi-check2-circle text-success me-1"></i>Status Kehadiran Santri <span class="text-danger">*</span>
                        </label>
                        
                        @php
                            $currentStatus = old('status', $confirmation?->status ?? 'hadir');
                        @endphp

                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="status-radio-card p-3 rounded-3 border d-flex flex-column align-items-center text-center cursor-pointer h-100 transition-all" 
                                       id="cardStatusHadir"
                                       style="cursor: pointer; {{ $currentStatus === 'hadir' ? 'background: #ecfdf5; border-color: #059669 !important;' : 'background: #fafaf9;' }}">
                                    <input type="radio" name="status" value="hadir" class="d-none" {{ $currentStatus === 'hadir' ? 'checked' : '' }} onchange="updateStatusCardUI('hadir')">
                                    <i class="bi bi-check-circle-fill fs-3 text-success mb-1"></i>
                                    <span class="fw-bold text-dark small">Hadir</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">Selesai bimbingan & terhitung honor</span>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="status-radio-card p-3 rounded-3 border d-flex flex-column align-items-center text-center cursor-pointer h-100 transition-all" 
                                       id="cardStatusIzin"
                                       style="cursor: pointer; {{ $currentStatus === 'izin' ? 'background: #fffbeb; border-color: #d97706 !important;' : 'background: #fafaf9;' }}">
                                    <input type="radio" name="status" value="izin" class="d-none" {{ $currentStatus === 'izin' ? 'checked' : '' }} onchange="updateStatusCardUI('izin')">
                                    <i class="bi bi-info-circle-fill fs-3 text-warning mb-1"></i>
                                    <span class="fw-bold text-dark small">Izin</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">Santri berhalangan hadir</span>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="status-radio-card p-3 rounded-3 border d-flex flex-column align-items-center text-center cursor-pointer h-100 transition-all" 
                                       id="cardStatusSakit"
                                       style="cursor: pointer; {{ $currentStatus === 'sakit' ? 'background: #fef2f2; border-color: #dc2626 !important;' : 'background: #fafaf9;' }}">
                                    <input type="radio" name="status" value="sakit" class="d-none" {{ $currentStatus === 'sakit' ? 'checked' : '' }} onchange="updateStatusCardUI('sakit')">
                                    <i class="bi bi-heart-pulse-fill fs-3 text-danger mb-1"></i>
                                    <span class="fw-bold text-dark small">Sakit</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">Santri sakit / kurang sehat</span>
                                </label>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-1.5" style="font-size: 0.75rem;">
                            *Pilihan <strong>Hadir</strong> akan otomatis menandai sesi sebagai <code>Selesai (Completed)</code> dan menambahkan hak honor mengajar Rp 100.000 ke Slip Gaji Anda.
                        </small>
                    </div>

                    <!-- Field 4: Upload Bukti Foto di Lokasi -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary mb-1 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-camera-fill text-success me-1"></i>Upload Bukti Foto di Rumah Santri
                            </span>
                            <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.7rem;">JPG, PNG, WEBP (Maks 5MB)</span>
                        </label>

                        <div class="border border-2 border-dashed rounded-4 p-4 text-center bg-light position-relative" id="dropArea" style="transition: all 0.2s;">
                            <input type="file" 
                                   name="proof_image" 
                                   id="proofImageInput" 
                                   class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" 
                                   style="cursor: pointer;"
                                   accept="image/*"
                                   onchange="previewSelectedImage(this)">
                            
                            <div id="uploadPlaceholder">
                                <div class="rounded-circle bg-white shadow-xs p-3 d-inline-flex align-items-center justify-content-center mb-2" style="width: 54px; height: 54px;">
                                    <i class="bi bi-cloud-arrow-up-fill fs-3 text-success"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Pilih Berkas Foto atau Buka Kamera</h6>
                                <p class="text-muted small mb-0">Klik area ini untuk mengambil foto langsung dari kamera HP atau memilih dari galeri.</p>
                            </div>

                            <!-- Live Image Preview Container -->
                            <div id="previewContainer" class="d-none mt-2">
                                <img id="imagePreviewElement" src="#" alt="Pratinjau Foto Bukti" class="img-fluid rounded-3 shadow-sm border" style="max-height: 250px;">
                                <div class="mt-2">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1" id="previewFileName">Foto Terpilih</span>
                                    <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none ms-2" onclick="clearSelectedImage()">
                                        <i class="bi bi-trash me-1"></i>Ganti Foto
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Field 5: Catatan Bimbingan / Evaluasi -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary mb-1">
                            <i class="bi bi-chat-left-text-fill text-primary me-1"></i>Catatan Pengajaran / Evaluasi
                        </label>
                        <textarea name="notes" 
                                  class="form-control rounded-3" 
                                  rows="3" 
                                  placeholder="Contoh: Sesi belajar berjalan tertib di rumah ananda. Ananda membaca jilid 2 halaman 14-16 dengan pengucapan makhraj yang semakin jelas.">{{ old('notes', $confirmation?->notes ?? '') }}</textarea>
                        <small class="text-muted" style="font-size: 0.75rem;">
                            Catatan ini akan langsung dapat dibaca oleh Orang Tua di portal mereka dan dicatat ke rekam mutaba'ah.
                        </small>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top flex-wrap gap-2">
                        <a href="{{ route('mentor.sessions.index') }}" class="btn btn-light border rounded-pill px-4">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-success text-white rounded-pill px-5 fw-bold shadow-sm d-inline-flex align-items-center gap-2" id="btnSubmitAttendance">
                            <i class="bi bi-check2-circle fs-5"></i>
                            <span>Simpan &amp; Verifikasi Presensi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function updateStatusCardUI(status) {
        const cards = {
            'hadir': { id: 'cardStatusHadir', bg: '#ecfdf5', border: '#059669' },
            'izin': { id: 'cardStatusIzin', bg: '#fffbeb', border: '#d97706' },
            'sakit': { id: 'cardStatusSakit', bg: '#fef2f2', border: '#dc2626' }
        };

        Object.keys(cards).forEach(key => {
            const el = document.getElementById(cards[key].id);
            if (key === status) {
                el.style.background = cards[key].bg;
                el.style.borderColor = cards[key].border;
            } else {
                el.style.background = '#fafaf9';
                el.style.borderColor = '#e5e7eb';
            }
        });
    }

    function previewSelectedImage(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                document.getElementById('imagePreviewElement').src = e.target.result;
                document.getElementById('previewFileName').innerText = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                document.getElementById('uploadPlaceholder').classList.add('d-none');
                document.getElementById('previewContainer').classList.remove('d-none');
            };

            reader.readAsDataURL(file);
        }
    }

    function clearSelectedImage() {
        const input = document.getElementById('proofImageInput');
        input.value = '';
        document.getElementById('uploadPlaceholder').classList.remove('d-none');
        document.getElementById('previewContainer').classList.add('d-none');
    }
</script>
@endsection
