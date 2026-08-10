<div class="row g-4 mb-4">
    <!-- Card: Total Santri -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-7 fw-semibold text-uppercase tracking-wider">Total Santri</span>
                        <h2 class="fw-bold mt-2 mb-0" style="color: var(--text-primary);">{{ $totalStudents }}</h2>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 56px; height: 56px; background: var(--primary-lighter); color: var(--primary);">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-top d-flex align-items-center text-success small">
                    <i class="bi bi-arrow-up-right me-1"></i>
                    <span>Aktif dalam pendampingan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Total Pendamping -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-7 fw-semibold text-uppercase tracking-wider">Pendamping</span>
                        <h2 class="fw-bold mt-2 mb-0" style="color: var(--text-primary);">{{ $totalMentors }}</h2>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 56px; height: 56px; background: rgba(255, 193, 7, 0.15); color: #d97706;">
                        <i class="bi bi-person-badge-fill fs-3"></i>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-top d-flex align-items-center text-muted small">
                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                    <span>Pengajar Tajwid & Tahfidz</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Sesi Hari Ini -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-7 fw-semibold text-uppercase tracking-wider">Sesi Hari Ini</span>
                        <h2 class="fw-bold mt-2 mb-0" style="color: var(--text-primary);">{{ $todaySessions }}</h2>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 56px; height: 56px; background: rgba(14, 165, 233, 0.15); color: #0284c7;">
                        <i class="bi bi-calendar-check-fill fs-3"></i>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-top d-flex align-items-center text-info small">
                    <i class="bi bi-clock-history me-1"></i>
                    <span>Jadwal aktif berjalan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Program Belajar -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-7 fw-semibold text-uppercase tracking-wider">Program Aktif</span>
                        <h2 class="fw-bold mt-2 mb-0" style="color: var(--text-primary);">{{ $totalPrograms }}</h2>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 56px; height: 56px; background: rgba(168, 85, 247, 0.15); color: #9333ea;">
                        <i class="bi bi-journal-bookmark-fill fs-3"></i>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-top d-flex align-items-center text-purple small">
                    <i class="bi bi-book me-1"></i>
                    <span>Tahsin, Tahfidz, Iqra</span>
                </div>
            </div>
        </div>
    </div>
</div>
