@extends('layouts.landing')

@section('title', 'Biaya Pendampingan Belajar | AL-HIKMAH')
@section('description', 'Biaya dan Paket Belajar AL-HIKMAH — Informasi transparan tentang pilihan pendampingan belajar Al-Qur\'an.')

@section('content')
    <!-- ============================================ -->
    <!-- 1. ETRAIN BREADCRUMB HEADER -->
    <!-- ============================================ -->
    <section class="breadcrumb_bg" aria-label="Header Biaya Belajar">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb_iner_item" data-reveal>
                        <div class="section-badge mx-auto mb-2"><i class="bi bi-tag-fill"></i> Investasi Pendidikan</div>
                        <h2>Pilihan <span class="text-gradient">Investasi &amp; Paket Belajar</span></h2>
                        <p>Transparan, terjangkau, dan fleksibel untuk mendukung kelancaran belajar Al-Qur'an keluarga Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Biaya Pendaftaran -->
    <section class="section-padding pb-0" aria-label="Biaya Pendaftaran">
        <div class="container">
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8" data-reveal>
                    <div class="biaya-card">
                        <div class="biaya-card-left">
                            <div class="biaya-icon-wrapper"><i class="bi bi-file-earmark-check"></i></div>
                            <div class="biaya-info"><span class="biaya-label">Biaya Pendaftaran</span>
                                <div class="biaya-harga">Rp <span class="biaya-angka">{{ number_format($registrationFee, 0, ',', '.') }}</span></div>
                                <span class="biaya-catatan">✔ Satu kali pembayaran untuk administrasi & assessment awal</span>
                            </div>
                        </div>
                        <div class="biaya-card-right">
                            <span class="biaya-badge">Sekali Bayar</span>
                            <small class="biaya-subnote">*Belum termasuk paket belajar program</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Daftar Paket Investasi Belajar Per Program -->
    <section class="section-padding" aria-label="Paket Belajar">
        <div class="container">
            <div class="program-section-title text-center mb-4">
                <i class="bi bi-journal-bookmark text-success me-2"></i>Daftar Pilihan Program & Investasi Belajar
            </div>
                     <div class="row g-4 justify-content-center">
                @foreach($programs as $index => $program)
                    @php
                        $parentEnrollment = isset($parentEnrollments) ? $parentEnrollments->firstWhere('program_id', $program->id) : null;
                    @endphp
                    <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="{{ ($index % 3) * 100 }}">
                        <div class="paket-card {{ $program->is_popular ? 'paket-popular' : '' }} {{ $parentEnrollment ? 'border-2 border-success shadow' : '' }} h-100 d-flex flex-column justify-content-between">
                            @if($parentEnrollment)
                                <div class="paket-popular-ribbon" style="background: linear-gradient(135deg, #0d7a3e 0%, #198754 100%);">
                                    <span>✔ Terdaftar</span>
                                </div>
                            @elseif($program->is_popular)
                                <div class="paket-popular-ribbon"><span>⭐ Paling Diminati</span></div>
                            @endif
                            <div>
                                <div class="paket-card-header">
                                    <span class="paket-name">{{ $program->name }}</span>
                                    <span class="paket-badge {{ $program->is_popular ? 'popular' : '' }}">{{ $program->duration_weeks }} Minggu</span>
                                </div>
                                <div class="paket-card-body">
                                    @if($parentEnrollment)
                                        @if($parentEnrollment->isWaitingAdmin())
                                            <div class="alert alert-warning border-0 py-2 px-3 mb-3 rounded-3 text-start small">
                                                <div class="d-flex align-items-center gap-2 fw-bold text-dark mb-1">
                                                    <i class="bi bi-hourglass-split text-warning"></i> Status: Sedang Direview
                                                </div>
                                                <div class="text-secondary" style="font-size: 0.8rem;">
                                                    Santri: <strong>{{ $parentEnrollment->student?->getDisplayName() }}</strong><br>
                                                    Lembaga sedang mereview jadwal &amp; ketersediaan guru.
                                                </div>
                                            </div>
                                        @elseif($parentEnrollment->isWaitingParent())
                                            <div class="alert alert-info border-0 py-2 px-3 mb-3 rounded-3 text-start small">
                                                <div class="d-flex align-items-center gap-2 fw-bold text-info-emphasis mb-1">
                                                    <i class="bi bi-chat-dots-fill text-info"></i> Status: Menunggu Respon Anda
                                                </div>
                                                <div class="text-secondary" style="font-size: 0.8rem;">
                                                    Santri: <strong>{{ $parentEnrollment->student?->getDisplayName() }}</strong><br>
                                                    Ada tawaran alternatif jadwal dari lembaga.
                                                </div>
                                            </div>
                                        @elseif($parentEnrollment->isConfirmed())
                                            <div class="alert alert-primary border-0 py-2 px-3 mb-3 rounded-3 text-start small">
                                                <div class="d-flex align-items-center gap-2 fw-bold text-primary-emphasis mb-1">
                                                    <i class="bi bi-check-circle-fill text-primary"></i> Status: Jadwal Deal (Siap Bayar)
                                                </div>
                                                <div class="text-secondary" style="font-size: 0.8rem;">
                                                    Santri: <strong>{{ $parentEnrollment->student?->getDisplayName() }}</strong><br>
                                                    Guru: <strong>{{ $parentEnrollment->mentor?->getDisplayName() ?? 'Ditentukan Lembaga' }}</strong>
                                                </div>
                                            </div>
                                        @elseif($parentEnrollment->isActive())
                                            <div class="alert alert-success border-0 py-2 px-3 mb-3 rounded-3 text-start small">
                                                <div class="d-flex align-items-center gap-2 fw-bold text-success mb-1">
                                                    <i class="bi bi-award-fill text-success"></i> Status: Bimbingan Aktif Berjalan
                                                </div>
                                                <div class="text-secondary" style="font-size: 0.8rem;">
                                                    Santri: <strong>{{ $parentEnrollment->student?->getDisplayName() }}</strong><br>
                                                    Guru: <strong>{{ $parentEnrollment->mentor?->getDisplayName() ?? 'Guru Aktif' }}</strong>
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    <div class="paket-price">
                                        <span class="price-amount">{{ $program->formatted_price }}</span>
                                        <span class="price-period">/ paket</span>
                                    </div>
                                    <div class="paket-detail">
                                        <span class="detail-label">Tingkat / Target</span>
                                        <span class="detail-value fw-bold text-success">{{ $program->level }}</span>
                                    </div>
                                    <div class="paket-detail">
                                        <span class="detail-label">Durasi Pembelajaran</span>
                                        <span class="detail-value">{{ $program->duration_weeks }} Minggu Terstruktur</span>
                                    </div>
                                    <div class="paket-detail">
                                        <span class="detail-label">Model Bimbingan</span>
                                        <span class="detail-value">Private Intensif (1:1)</span>
                                    </div>
                                    <div class="paket-detail">
                                        <span class="detail-label">Fasilitas</span>
                                        <span class="detail-value">Modul &amp; Rapor Berkala</span>
                                    </div>
                                </div>
                            </div>
                            <div class="px-3 pb-3 mt-3">
                                @auth
                                    @if(auth()->user()->isParent())
                                        @if($parentEnrollment)
                                            @if($parentEnrollment->isWaitingAdmin())
                                                <a href="{{ route('parent.enrollments.show', $parentEnrollment->id) }}" 
                                                   class="btn btn-warning text-dark w-100 py-2 rounded-pill fw-bold shadow-sm mb-2">
                                                    <i class="bi bi-eye me-1"></i> Pantau Status Jadwal
                                                </a>
                                            @elseif($parentEnrollment->isWaitingParent())
                                                <a href="{{ route('parent.enrollments.show', $parentEnrollment->id) }}" 
                                                   class="btn btn-info text-white w-100 py-2 rounded-pill fw-bold shadow-sm mb-2">
                                                    <i class="bi bi-chat-dots me-1"></i> Konfirmasi Tawaran Jadwal
                                                </a>
                                            @elseif($parentEnrollment->isConfirmed())
                                                <a href="{{ route('parent.enrollments.show', $parentEnrollment->id) }}" 
                                                   class="btn btn-primary-custom w-100 py-2 rounded-pill fw-bold shadow-sm mb-2">
                                                    <i class="bi bi-credit-card me-1"></i> Bayar Sekarang
                                                </a>
                                            @elseif($parentEnrollment->isActive())
                                                <a href="{{ route('parent.enrollments.show', $parentEnrollment->id) }}" 
                                                   class="btn btn-success w-100 py-2 rounded-pill fw-bold shadow-sm mb-2">
                                                    <i class="bi bi-calendar-check me-1"></i> Lihat Sesi Bimbingan
                                                </a>
                                            @else
                                                <a href="{{ route('parent.enrollments.create', ['program_id' => $program->id]) }}" 
                                                   class="btn {{ $program->is_popular ? 'btn-primary-custom' : 'btn-outline-custom' }} w-100 py-2 rounded-pill mb-2">
                                                    <i class="bi bi-calendar-plus me-1"></i> Pilih Program & Jadwal
                                                </a>
                                            @endif
                                            <a href="{{ route('parent.enrollments.create', ['program_id' => $program->id]) }}" 
                                               class="btn btn-outline-secondary btn-sm w-100 py-1 rounded-pill" style="font-size: 0.8rem;">
                                                <i class="bi bi-person-plus me-1"></i> + Daftar Santri Lain
                                            </a>
                                        @else
                                            <a href="{{ route('parent.enrollments.create', ['program_id' => $program->id]) }}" 
                                               class="btn {{ $program->is_popular ? 'btn-primary-custom' : 'btn-outline-custom' }} w-100 py-2 rounded-pill">
                                                <i class="bi bi-calendar-plus me-1"></i> Pilih Program & Jadwal
                                            </a>
                                        @endif
                                    @elseif(auth()->user()->isAdmin())
                                        <a href="{{ route('admin.enrollments.index') }}" 
                                           class="btn btn-outline-warning w-100 py-2 rounded-pill">
                                            <i class="bi bi-gear me-1"></i> Kelola Pendaftaran (Admin)
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-5">
                <p class="text-muted fst-italic">"Tidak ada paksaan dalam memilih program. Tim kami siap membantu mencocokkan program sesuai hasil assessment ananda."</p>
            </div>
        </div>
    </section>

    <!-- Modal Khusus Pendaftaran Program -->
    <div class="modal fade" id="modalProgramDaftar" tabindex="-1" aria-labelledby="modalProgramDaftarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-premium border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold text-success" id="modalProgramDaftarLabel">
                            <i class="bi bi-check2-circle me-2"></i>Formulir Pendaftaran Program
                        </h5>
                        <p class="text-muted small mb-0">Program yang dipilih: <span id="labelSelectedProgram" class="badge bg-success-subtle text-success fs-6 fw-bold">Program Al-Qur'an</span></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formProgramRegistration" action="{{ route('program.pre-register') }}" method="POST">
                        @csrf
                        <input type="hidden" name="program_id" id="inputModalProgramId" value="">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small" for="modalNamaWali">Nama Orang Tua / Wali <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modalNamaWali" name="nama" required placeholder="Nama Ayah/Bunda..." value="{{ auth()->check() ? auth()->user()->name : '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small" for="modalNamaAnak">Nama Murid / Calon Santri <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modalNamaAnak" name="nama_anak" required placeholder="Nama lengkap anak/peserta...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small" for="modalWhatsApp">Nomor WhatsApp Aktif <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="modalWhatsApp" name="whatsapp" required placeholder="08123456789" value="{{ auth()->check() ? auth()->user()->phone : '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small" for="modalUsia">Rentang Usia</label>
                                <select class="form-select" id="modalUsia" name="usia">
                                    <option value="10-15 tahun (Anak)" selected>10-15 tahun (Anak)</option>
                                    <option value="Dewasa (16-30 tahun)">Dewasa (16-30 tahun)</option>
                                    <option value="Dewasa (31-50 tahun)">Dewasa (31-50 tahun)</option>
                                    <option value="50+ tahun">50+ tahun</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small" for="modalGender">Jenis Kelamin</label>
                                <select class="form-select" id="modalGender" name="gender">
                                    <option value="L">Laki-laki (Ikhwan)</option>
                                    <option value="P">Perempuan (Akhwat)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small" for="modalLokasi">Kota / Kecamatan Tinggal <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modalLokasi" name="lokasi" required placeholder="Contoh: Semarang Barat / Jakarta Selatan">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-secondary small" for="modalMetode">Preferensi Metode Belajar</label>
                                <select class="form-select" id="modalMetode" name="metode">
                                    <option value="Online (Zoom / Meet)" selected>Online (Tatap Maya Interaktif)</option>
                                    <option value="Offline (Guru Datang ke Rumah)">Offline (Guru Datang ke Rumah)</option>
                                    <option value="Hybrid (Kombinasi)">Hybrid (Kombinasi Online & Offline)</option>
                                </select>
                            </div>
                        </div>

                        <div class="alert alert-success-subtle border-0 d-flex align-items-center mt-4 mb-0 py-2">
                            <i class="bi bi-shield-check text-success fs-4 me-2"></i>
                            <span class="small text-success-emphasis">Data Anda aman. Setelah mengirim form ini, Anda akan diarahkan untuk konfirmasi akun murid di LMS AL-HIKMAH.</span>
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-bold mt-4 rounded-3">
                            <i class="bi bi-arrow-right-circle me-2"></i> Lanjutkan Pendaftaran Akun LMS
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Interaktif Modal (Idiomatic Bootstrap 5 Event Delegation) -->
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('modalProgramDaftar');
            if (!modalEl) return;

            modalEl.addEventListener('show.bs.modal', function (event) {
                // Tombol yang memicu modal (Bootstrap 5 relatedTarget)
                const button = event.relatedTarget;
                if (!button) return;

                const programId    = button.getAttribute('data-program-id');
                const programName  = button.getAttribute('data-program-name');
                const programPrice = button.getAttribute('data-program-price');

                // Set input hidden & label
                const inputId = modalEl.querySelector('#inputModalProgramId');
                const labelEl = modalEl.querySelector('#labelSelectedProgram');

                if (inputId) inputId.value = programId;
                if (labelEl) labelEl.textContent = `${programName} (${programPrice})`;
            });
        });
    </script>
    @endpush
@endsection
