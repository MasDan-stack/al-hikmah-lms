<!-- Modal Pendaftaran / Konsultasi -->
<div class="modal fade" id="daftarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-premium border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-success"><i class="bi bi-pencil-square me-2"></i>Formulir Konsultasi & Pendaftaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-4 small">Isi data berikut untuk memulai perjalanan belajar Al-Qur'an Anda bersama AL-HIKMAH.</p>
                <form id="registrationForm" action="{{ route('register') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="namaLengkap">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="namaLengkap" name="nama" required autocomplete="name" placeholder="Nama Anda...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="noWhatsApp">Nomor WhatsApp <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="noWhatsApp" name="whatsapp" required autocomplete="tel" placeholder="08123456789">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="usiaPeserta">Usia Peserta</label>
                            <select class="form-select" id="usiaPeserta" name="usia">
                                <option value="">Pilih usia...</option>
                                <option>10-15 tahun (Anak)</option>
                                <option>Dewasa (16-30 tahun)</option>
                                <option>Dewasa (31-50 tahun)</option>
                                <option>50+ tahun</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="lokasi">Lokasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="lokasi" name="lokasi" placeholder="Kota/Kecamatan" required autocomplete="address-level2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="programPilihan">Program Pilihan</label>
                            <select class="form-select" id="programPilihan" name="program">
                                <option value="">Pilih program...</option>
                                <option>Tahsin</option>
                                <option>Tahfidz</option>
                                <option>Belajar dari Nol</option>
                                <option>Program Anak</option>
                                <option>Program Dewasa</option>
                                <option>Bahasa Arab</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="metodeBelajar">Metode Belajar</label>
                            <select class="form-select" id="metodeBelajar" name="metode">
                                <option value="">Pilih metode...</option>
                                <option>Online</option>
                                <option>Offline (Home Visit)</option>
                                <option>Hybrid (Kombinasi)</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-bold mt-4 rounded-3">
                        <i class="bi bi-person-plus me-2"></i> Lanjutkan Pendaftaran Akun
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>