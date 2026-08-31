<!-- Modal Perayaan Pencapaian Badge Baru -->
<div class="modal fade" id="celebrationBadgeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg text-center p-4">
            <div class="modal-body py-4">
                <div class="mb-3">
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold fs-6">
                        🎉 Masya Allah! Pencapaian Baru
                    </span>
                </div>

                <div class="rounded-circle mx-auto my-3 bg-warning bg-opacity-10 text-warning border border-warning d-flex align-items-center justify-content-center"
                     style="width: 100px; height: 100px;">
                    <i class="bi bi-trophy-fill" style="font-size: 3.5rem;"></i>
                </div>

                <h4 class="fw-bold text-success mb-2" id="celebrationBadgeTitle">Lencana Baru Diraih!</h4>
                <p class="text-muted mb-4" id="celebrationBadgeDesc">Selamat atas keistiqomahanmu dalam menghafal Al-Qur'an.</p>

                <div class="p-3 bg-warning bg-opacity-10 rounded-3 border border-warning border-opacity-25 mb-4">
                    <div class="text-warning fw-bold fs-5" id="celebrationBadgePoints">+100 Poin Gamifikasi</div>
                    <small class="text-muted">Poin telah ditambahkan ke profil Anda!</small>
                </div>

                <button type="button" class="btn btn-success rounded-pill px-5 py-2 fw-bold" data-bs-dismiss="modal">
                    Alhamdulillah, Lanjutkan!
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function triggerCelebration(title, desc, points) {
        if (typeof confetti === 'function') {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 }
            });
        }

        const modalEl = document.getElementById('celebrationBadgeModal');
        if (modalEl) {
            if (title) document.getElementById('celebrationBadgeTitle').innerText = title;
            if (desc) document.getElementById('celebrationBadgeDesc').innerText = desc;
            if (points) document.getElementById('celebrationBadgePoints').innerText = '+' + points + ' Poin Gamifikasi';

            const bsModal = new bootstrap.Modal(modalEl);
            bsModal.show();
        }
    }
</script>
