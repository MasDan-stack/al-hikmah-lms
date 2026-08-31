@props(['milestone' => null])

@if($milestone)
<div class="card border-0 shadow-sm rounded-4 p-4 text-white position-relative overflow-hidden" 
     style="background: linear-gradient(135deg, #0d7a3e 0%, #1e3a8a 100%);">
    <!-- Islamic Pattern Background Overlay -->
    <div class="position-absolute end-0 top-0 p-3 opacity-25">
        <i class="bi bi-clock-history" style="font-size: 8rem; line-height: 0.8;"></i>
    </div>

    <div class="position-relative z-1">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-flag-fill me-1"></i> Target Milestone
            </span>
            <small class="text-white-50">Tenggat: {{ $milestone->target_date->translatedFormat('d F Y, H:i') }} WIB</small>
        </div>

        <h4 class="fw-bold mb-1">{{ $milestone->name }}</h4>
        <p class="text-white-50 small mb-3">Tingkatkan setoran untuk mencapai target sebelum hitungan mundur berakhir!</p>

        <!-- Countdown Digits Grid -->
        <div class="row g-2 text-center my-2" id="flipCountdown" data-target="{{ $milestone->target_date->toIso8601String() }}">
            <div class="col-3">
                <div class="bg-black bg-opacity-25 rounded-3 py-2 px-1 border border-white border-opacity-10">
                    <div class="fs-3 fw-bold text-warning lh-1" id="cdDays">00</div>
                    <small class="text-white-50 text-uppercase" style="font-size: 0.65rem;">Hari</small>
                </div>
            </div>
            <div class="col-3">
                <div class="bg-black bg-opacity-25 rounded-3 py-2 px-1 border border-white border-opacity-10">
                    <div class="fs-3 fw-bold text-white lh-1" id="cdHours">00</div>
                    <small class="text-white-50 text-uppercase" style="font-size: 0.65rem;">Jam</small>
                </div>
            </div>
            <div class="col-3">
                <div class="bg-black bg-opacity-25 rounded-3 py-2 px-1 border border-white border-opacity-10">
                    <div class="fs-3 fw-bold text-white lh-1" id="cdMinutes">00</div>
                    <small class="text-white-50 text-uppercase" style="font-size: 0.65rem;">Menit</small>
                </div>
            </div>
            <div class="col-3">
                <div class="bg-black bg-opacity-25 rounded-3 py-2 px-1 border border-white border-opacity-10">
                    <div class="fs-3 fw-bold text-info lh-1" id="cdSeconds">00</div>
                    <small class="text-white-50 text-uppercase" style="font-size: 0.65rem;">Detik</small>
                </div>
            </div>
        </div>

        <!-- Progress bar towards goal -->
        @php
            $percent = $milestone->progress_goal > 0 ? min(100, round(($milestone->progress_current / $milestone->progress_goal) * 100)) : 0;
        @endphp
        <div class="mt-3">
            <div class="d-flex justify-content-between text-white-50 small mb-1">
                <span>Capaian: {{ $milestone->progress_current }} / {{ $milestone->progress_goal }}</span>
                <span class="text-white fw-bold">{{ $percent }}%</span>
            </div>
            <div class="progress rounded-pill bg-black bg-opacity-25" style="height: 8px;">
                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percent }}%;"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const countdownEl = document.getElementById('flipCountdown');
        if (!countdownEl) return;

        const targetDate = new Date(countdownEl.dataset.target).getTime();

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance < 0) {
                document.getElementById('cdDays').innerText = "00";
                document.getElementById('cdHours').innerText = "00";
                document.getElementById('cdMinutes').innerText = "00";
                document.getElementById('cdSeconds').innerText = "00";
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById('cdDays').innerText = String(days).padStart(2, '0');
            document.getElementById('cdHours').innerText = String(hours).padStart(2, '0');
            document.getElementById('cdMinutes').innerText = String(minutes).padStart(2, '0');
            document.getElementById('cdSeconds').innerText = String(seconds).padStart(2, '0');
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    });
</script>
@else
<div class="card border-0 shadow-sm rounded-4 p-4 bg-light-subtle text-center">
    <div class="py-3">
        <i class="bi bi-flag text-muted" style="font-size: 2.5rem;"></i>
        <h6 class="fw-bold mt-2 mb-1">Belum Ada Target Milestone Aktif</h6>
        <p class="text-muted small mb-3">Tetapkan target hafalan jangka panjang untuk memicu hitung mundur motivasi!</p>
        <a href="{{ route('student.milestones') }}" class="btn btn-sm btn-success rounded-pill px-3">
            <i class="bi bi-plus-circle me-1"></i> Buat Target Baru
        </a>
    </div>
</div>
@endif
