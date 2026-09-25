<!-- Modal Pendaftaran Khusus Program Tahfidz Al-Qur'an (Guest/Publik) -->
<div class="modal fade" id="tahfidzDaftarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-premium border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-success">
                    <i class="bi bi-book-half me-2"></i>Formulir Pendaftaran Program Tahfidz Al-Qur'an
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-4 small">Lengkapi data santri dan orang tua untuk mendaftar Program Tahfidz Al-Qur'an bersama AL-HIKMAH.</p>
                <form action="{{ route('tahfidz.pre-register') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="tahfidzNamaOrangTua">Nama Orang Tua / Wali <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tahfidzNamaOrangTua" name="nama" required placeholder="Nama Anda (Orang Tua)...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="tahfidzNamaAnak">Nama Santri / Anak <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tahfidzNamaAnak" name="nama_anak" required placeholder="Nama lengkap santri...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="tahfidzWhatsApp">Nomor WhatsApp <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="tahfidzWhatsApp" name="whatsapp" required placeholder="08123456789">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-secondary small" for="tahfidzUsia">Usia (Tahun)</label>
                            <input type="number" class="form-control" id="tahfidzUsia" name="usia" min="5" max="80" placeholder="Contoh: 10">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-secondary small" for="tahfidzGender">Jenis Kelamin</label>
                            <select class="form-select" id="tahfidzGender" name="gender">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="tahfidzLokasi">Lokasi / Kota <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tahfidzLokasi" name="lokasi" required placeholder="Kota / Kecamatan tempat tinggal">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="tahfidzTarget">Target Hafalan <span class="text-danger">*</span></label>
                            <select class="form-select" id="tahfidzTarget" name="target_tahfidz" required>
                                <option value="Juz 30 (Juz Amma)">Juz 30 (Juz Amma)</option>
                                <option value="Juz 29">Juz 29</option>
                                <option value="Surah Al-Baqarah">Surah Al-Baqarah</option>
                                <option value="Target 30 Juz">Target 30 Juz</option>
                                <option value="Bebas / Bertahap">Bebas / Bertahap</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="tahfidzLevel">Level Hafalan Saat Ini</label>
                            <select class="form-select" id="tahfidzLevel" name="level_tahfidz">
                                <option value="Pemula (Belum ada hafalan)">Pemula (Belum ada hafalan)</option>
                                <option value="Juz 30 Sebagian">Juz 30 Sebagian</option>
                                <option value="Sudah Hafal 1-3 Juz">Sudah Hafal 1-3 Juz</option>
                                <option value="Lanjutan > 3 Juz">Lanjutan > 3 Juz</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="tahfidzMetode">Metode Belajar</label>
                            <select class="form-select" id="tahfidzMetode" name="metode">
                                <option value="Online">Online (Zoom/Meet)</option>
                                <option value="Offline (Home Visit)">Offline (Home Visit)</option>
                                <option value="Hybrid (Kombinasi)">Hybrid (Kombinasi)</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-bold mt-4 rounded-3 shadow-sm">
                        <i class="bi bi-person-plus me-2"></i> Lanjutkan Pendaftaran Program Tahfidz
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
