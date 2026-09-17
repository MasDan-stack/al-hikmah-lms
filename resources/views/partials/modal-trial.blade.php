<!-- Modal Pendaftaran Gratis & Penempatan Level Belajar (Placement Test) -->
<div class="modal fade modal-trial-custom" id="trialModal" tabindex="-1" aria-labelledby="trialModalLabel" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content trial-modal-card border-0 shadow-xl rounded-4">
            <!-- Modal Header -->
            <div class="modal-header border-0 pb-0 pt-4 px-4 px-md-5 d-flex align-items-start justify-content-between">
                <div>
                    <span class="badge-trial-pill mb-2">
                        <i class="bi bi-gift-fill me-1"></i> 100% Bebas Biaya • Penempatan Level Belajar
                    </span>
                    <h4 class="modal-title font-display fw-bold text-heading" id="trialModalLabel">
                        Sesi Perkenalan &amp; Penempatan Belajar Ananda
                    </h4>
                    <p class="text-secondary small mb-0 mt-1">
                        Kami mengajak ananda mengobrol dan membaca bersama Ustadz atau Ustadzah selama 15 menit. Tujuannya mengenali tingkat bacaan ananda dalam suasana yang santai, bersahabat, dan tanpa rasa tertekan.
                    </p>
                </div>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Tutup jendela pendaftaran"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4 px-md-5">
                <!-- State Alert Container (AJAX feedback) -->
                <div id="trialFormAlert" class="d-none mb-4" role="alert"></div>

                <form id="trialBookingForm" action="{{ route('trial.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="row g-3">
                        <!-- Data Orang Tua -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="trialParentName">
                                Nama Orang Tua / Wali <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control trial-input" id="trialParentName" name="parent_name"
                                   required placeholder="Nama Anda (Bunda / Ayah)" autocomplete="name">
                            <div class="invalid-feedback small">Mohon masukkan nama orang tua atau wali.</div>
                        </div>

                        <!-- Data WhatsApp -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="trialWhatsapp">
                                Nomor WhatsApp Aktif <span class="text-danger">*</span>
                            </label>
                            <input type="tel" class="form-control trial-input" id="trialWhatsapp" name="whatsapp"
                                   required placeholder="Contoh: 081234567890" autocomplete="tel">
                            <div class="invalid-feedback small">Mohon masukkan nomor WhatsApp untuk konfirmasi jadwal.</div>
                        </div>

                        <!-- Data Calon Santri -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="trialChildName">
                                Nama Calon Santri (Anak) <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control trial-input" id="trialChildName" name="child_name"
                                   required placeholder="Nama lengkap ananda">
                            <div class="invalid-feedback small">Mohon masukkan nama ananda.</div>
                        </div>

                        <!-- Usia & Kelamin -->
                        <div class="col-md-3 col-6">
                            <label class="form-label fw-semibold text-secondary small" for="trialChildAge">
                                Usia Santri <span class="text-danger">*</span>
                            </label>
                            <select class="form-select trial-input" id="trialChildAge" name="child_age" required>
                                <option value="">Pilih usia...</option>
                                <option value="4-6 Tahun">4 - 6 Tahun (TK)</option>
                                <option value="7-9 Tahun">7 - 9 Tahun (SD Awal)</option>
                                <option value="10-12 Tahun">10 - 12 Tahun (SD Akhir)</option>
                                <option value="13-17 Tahun">13 - 17 Tahun (Remaja)</option>
                                <option value="18+ Tahun">Dewasa (18+ Tahun)</option>
                            </select>
                            <div class="invalid-feedback small">Pilih rentang usia.</div>
                        </div>

                        <div class="col-md-3 col-6">
                            <label class="form-label fw-semibold text-secondary small" for="trialGender">
                                Jenis Kelamin <span class="text-danger">*</span>
                            </label>
                            <select class="form-select trial-input" id="trialGender" name="gender" required>
                                <option value="L" selected>Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <!-- Fokus Sesi Uji Coba -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="trialFocus">
                                Fokus Uji Coba (Placement Test) <span class="text-danger">*</span>
                            </label>
                            <select class="form-select trial-input" id="trialFocus" name="trial_focus" required>
                                <option value="iqra_placement" selected>Penempatan Iqra &amp; Pengenalan Huruf Hijaiyah</option>
                                <option value="tahsin_tajwid">Tes Tahsin &amp; Makharijul Huruf Tartil</option>
                                <option value="tahfidz_hafalan">Evaluasi Kelancaran Hafalan &amp; Murajaah</option>
                                <option value="bahasa_arab">Dasar Bahasa Arab &amp; Pembiasaan Adab</option>
                            </select>
                        </div>

                        <!-- Metode Sesi -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="trialMethod">
                                Metode Pelaksanaan <span class="text-danger">*</span>
                            </label>
                            <select class="form-select trial-input" id="trialMethod" name="learning_method" required>
                                <option value="online" selected>Online (Google Meet / Zoom Interaktif)</option>
                                <option value="offline">Offline / Guru Datang ke Rumah (Khusus Jabodetabek)</option>
                            </select>
                        </div>

                        <!-- Preferensi Waktu Belajar -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="trialTimeSlot">
                                Pilihan Slot Waktu <span class="text-danger">*</span>
                            </label>
                            <select class="form-select trial-input" id="trialTimeSlot" name="preferred_time_slot" required>
                                <option value="sore" selected>Sore (16:00 - 17:30 WIB)</option>
                                <option value="pagi">Pagi (08:30 - 11:30 WIB)</option>
                                <option value="siang">Siang (13:30 - 15:30 WIB)</option>
                                <option value="malam">Malam (19:30 - 21:00 WIB)</option>
                            </select>
                        </div>

                        <!-- Kota / Domisili -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small" for="trialCity">
                                Kota / Wilayah Domisili
                            </label>
                            <input type="text" class="form-control trial-input" id="trialCity" name="city"
                                   placeholder="Contoh: Jakarta Selatan, Depok, Tangerang" autocomplete="address-level2">
                        </div>

                        <!-- Catatan Khusus -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small" for="trialNotes">
                                Catatan Khusus Belajar Anak <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <textarea class="form-control trial-input" id="trialNotes" name="notes" rows="2"
                                      placeholder="Contoh: Ananda sedikit pemalu, sebelumnya pernah belajar Iqra jilid 2..."></textarea>
                        </div>
                    </div>

                    <!-- Syarat & Privasi Singkat -->
                    <div class="trial-benefit-callout mt-4 p-3 rounded-3 bg-light border">
                        <div class="d-flex align-items-center gap-2 text-success fw-semibold small mb-1">
                            <i class="bi bi-shield-check fs-6"></i> Komitmen Transparansi Al-Hikmah
                        </div>
                        <p class="text-muted small mb-0">
                            Sesi uji coba ini 100% bebas biaya dan tidak mewajibkan pembelian paket. Nomor WhatsApp Anda hanya digunakan untuk konfirmasi jadwal sesi.
                        </p>
                    </div>

                    <!-- Tombol Aksi Submit -->
                    <div class="mt-4 pt-2 d-flex flex-column flex-sm-row gap-2 justify-content-end">
                        <button type="button" class="btn btn-light px-4 py-2 fw-semibold rounded-3 text-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" id="btnSubmitTrial" class="btn btn-editorial-primary px-4 py-3 fw-bold rounded-3 d-inline-flex align-items-center justify-content-center gap-2">
                            <span class="spinner-border spinner-border-sm d-none" id="trialSubmitSpinner" aria-hidden="true"></span>
                            <i class="bi bi-calendar2-check-fill" id="trialSubmitIcon"></i>
                            <span>Daftar Sekarang Tanpa Biaya</span>
                        </button>
                    </div>
                </form>

                <!-- Success Box Template (Shown after successful submission) -->
                <div id="trialSuccessView" class="d-none text-center py-4">
                    <div class="trial-success-icon-wrap mx-auto mb-3">
                        <i class="bi bi-check-lg text-success fs-1"></i>
                    </div>
                    <h4 class="fw-bold text-heading mb-2">Pendaftaran Berhasil Dikirim!</h4>
                    <p class="text-secondary small mx-auto mb-4" style="max-width: 480px;" id="trialSuccessMessage">
                        Alhamdulillah, formulir pendaftaran penempatan belajar gratis telah kami terima. Koordinator akademik kami akan segera menghubungi Anda melalui WhatsApp untuk konfirmasi jadwal guru.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="#" id="trialDirectWaBtn" target="_blank" rel="noopener" class="btn btn-success px-4 py-2 fw-semibold rounded-3 d-inline-flex align-items-center gap-2">
                            <i class="bi bi-whatsapp"></i> Hubungi Admin via WhatsApp
                        </a>
                        <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-3" data-bs-dismiss="modal">
                            Selesai &amp; Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
