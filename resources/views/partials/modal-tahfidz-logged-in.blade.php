<!-- Modal Pendaftaran Tahfidz untuk Orang Tua Terdaftar (Parent) -->
@auth
    @if(auth()->user()->isParent())
        <div class="modal fade" id="tahfidzLoggedInModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content modal-premium border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-success">
                            <i class="bi bi-journal-check me-2"></i>Daftarkan Anak ke Program Tahfidz Al-Qur'an
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted mb-4 small">Selamat datang, <strong>{{ auth()->user()->name }}</strong>. Pilih anak yang ingin didaftarkan atau tambahkan anak baru ke Program Tahfidz.</p>

                        <form action="{{ route('parent.enroll-tahfidz') }}" method="POST">
                            @csrf
                            @php
                                $parentProfile = auth()->user()->parentProfile;
                                $existingStudents = $parentProfile?->students ?? collect();
                            @endphp

                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary small">Pilih Santri / Anak <span class="text-danger">*</span></label>
                                <select class="form-select" name="student_id" id="tahfidzStudentSelect">
                                    @if($existingStudents->isNotEmpty())
                                        @foreach($existingStudents as $child)
                                            <option value="{{ $child->id }}">{{ $child->getDisplayName() }} (Usia: {{ $child->age ?? '-' }} thn)</option>
                                        @endforeach
                                    @endif
                                    <option value="new">+ Tambah Anak Baru ke Program Tahfidz</option>
                                </select>
                            </div>

                            <!-- Block Form Anak Baru jika memilih new -->
                            <div id="newChildFields" class="p-3 bg-light rounded-3 mb-3 d-none border">
                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-plus me-1 text-primary"></i>Data Santri Baru</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary small">Nama Lengkap Anak <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="new_nama_anak" placeholder="Nama anak...">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-secondary small">Usia</label>
                                        <input type="number" class="form-control" name="new_usia" placeholder="Tahun">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-secondary small">Gender</label>
                                        <select class="form-select" name="new_gender">
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Target & Level Tahfidz -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">Target Hafalan <span class="text-danger">*</span></label>
                                    <select class="form-select" name="target_tahfidz" required>
                                        <option value="Juz 30 (Juz Amma)">Juz 30 (Juz Amma)</option>
                                        <option value="Juz 29">Juz 29</option>
                                        <option value="Surah Al-Baqarah">Surah Al-Baqarah</option>
                                        <option value="Target 30 Juz">Target 30 Juz</option>
                                        <option value="Bebas / Bertahap">Bebas / Bertahap</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">Level Hafalan Saat Ini</label>
                                    <select class="form-select" name="level_tahfidz">
                                        <option value="Pemula (Belum ada hafalan)">Pemula (Belum ada hafalan)</option>
                                        <option value="Juz 30 Sebagian">Juz 30 Sebagian</option>
                                        <option value="Sudah Hafal 1-3 Juz">Sudah Hafal 1-3 Juz</option>
                                        <option value="Lanjutan > 3 Juz">Lanjutan > 3 Juz</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold text-secondary small">Metode Belajar</label>
                                    <select class="form-select" name="metode">
                                        <option value="Online">Online (Zoom/Meet)</option>
                                        <option value="Offline (Home Visit)">Offline (Home Visit)</option>
                                        <option value="Hybrid (Kombinasi)">Hybrid (Kombinasi)</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success-custom w-100 py-3 fw-bold rounded-3 shadow-sm">
                                <i class="bi bi-check-circle me-2"></i> Konfirmasi Pendaftaran Program Tahfidz
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const selectEl = document.getElementById('tahfidzStudentSelect');
                const newFields = document.getElementById('newChildFields');
                if (selectEl && newFields) {
                    selectEl.addEventListener('change', function () {
                        if (this.value === 'new') {
                            newFields.classList.remove('d-none');
                        } else {
                            newFields.classList.add('d-none');
                        }
                    });
                }
            });
        </script>
    @endif
@endauth
