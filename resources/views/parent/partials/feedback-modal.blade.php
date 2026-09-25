<!-- Modal Rating & Ulasan Pasca-Sesi Wali Santri -->
<div class="modal fade" id="parentFeedbackModal" tabindex="-1" aria-labelledby="parentFeedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('parent.feedbacks.store') }}" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <input type="hidden" name="session_id" id="feedback_session_id" value="">
            <input type="hidden" name="mentor_id" id="feedback_mentor_id" value="">
            <input type="hidden" name="overall_rating" id="feedback_overall_rating" value="5">

            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="parentFeedbackModalLabel">
                        ⭐ Ulasan Sesi Belajar Santri
                    </h5>
                    <p class="text-muted small mb-0">Bantu kami meningkatkan kualitas bimbingan guru <strong id="feedback_mentor_name">Ust. Pembimbing</strong></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body py-3">
                <!-- Star Rating Interactive -->
                <div class="text-center my-2">
                    <div class="d-flex justify-content-center gap-2 fs-2 text-warning mb-1" id="starRatingContainer" style="cursor: pointer;">
                        <i class="bi bi-star-fill star-item" data-value="1"></i>
                        <i class="bi bi-star-fill star-item" data-value="2"></i>
                        <i class="bi bi-star-fill star-item" data-value="3"></i>
                        <i class="bi bi-star-fill star-item" data-value="4"></i>
                        <i class="bi bi-star-fill star-item" data-value="5"></i>
                    </div>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill font-monospace" id="starRatingText">
                        5.0 - Sangat Memuaskan
                    </span>
                </div>

                <!-- Multi-Category Sub Ratings -->
                <div class="bg-light p-3 rounded-4 my-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-2 small">
                        <span>Penyampaian Materi:</span>
                        <select name="categories[teaching_quality]" class="form-select form-select-sm w-auto rounded-pill">
                            <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                            <option value="4">⭐⭐⭐⭐ (4)</option>
                            <option value="3">⭐⭐⭐ (3)</option>
                            <option value="2">⭐⭐ (2)</option>
                            <option value="1">⭐ (1)</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 small">
                        <span>Kesabaran &amp; Ketelatenan:</span>
                        <select name="categories[patience]" class="form-select form-select-sm w-auto rounded-pill">
                            <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                            <option value="4">⭐⭐⭐⭐ (4)</option>
                            <option value="3">⭐⭐⭐ (3)</option>
                            <option value="2">⭐⭐ (2)</option>
                            <option value="1">⭐ (1)</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span>Ketepatan Waktu:</span>
                        <select name="categories[punctuality]" class="form-select form-select-sm w-auto rounded-pill">
                            <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                            <option value="4">⭐⭐⭐⭐ (4)</option>
                            <option value="3">⭐⭐⭐ (3)</option>
                            <option value="2">⭐⭐ (2)</option>
                            <option value="1">⭐ (1)</option>
                        </select>
                    </div>
                </div>

                <!-- Quick Chips Tagging -->
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase mb-2">Tag Cepat (Klik untuk memilih):</label>
                    <div class="d-flex flex-wrap gap-2" id="quickChipsContainer">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill chip-btn" data-tag="#SangatSabar">#SangatSabar</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill chip-btn" data-tag="#TepatWaktu">#TepatWaktu</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill chip-btn" data-tag="#PenyampaianJelas">#PenyampaianJelas</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill chip-btn" data-tag="#SantriSemangat">#SantriSemangat</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill chip-btn" data-tag="#MakhrajDetail">#MakhrajDetail</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill chip-btn" data-tag="#PerluVariasi">#PerluVariasi</button>
                    </div>
                    <div id="hiddenChipsInputs"></div>
                </div>

                <!-- Written Comment -->
                <div class="mb-3">
                    <label class="form-label fw-bold small">Catatan Tambahan untuk Guru / Lembaga (Opsional):</label>
                    <textarea name="comment" rows="2" class="form-control rounded-3" placeholder="Tuliskan apresiasi atau saran membangun untuk guru..."></textarea>
                </div>

                <!-- Anonymous Toggle -->
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_anonymous" value="1" id="anonymousToggle">
                    <label class="form-check-label small text-muted" for="anonymousToggle">
                        Kirim ulasan sebagai Anonim (Nama saya disembunyikan dari guru)
                    </label>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Nanti Saja</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    Kirim Ulasan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Star Rating click listener
    const stars = document.querySelectorAll("#starRatingContainer .star-item");
    const ratingInput = document.getElementById("feedback_overall_rating");
    const ratingText = document.getElementById("starRatingText");

    const ratingLabels = {
        1: "1.0 - Perlu Banyak Perbaikan",
        2: "2.0 - Cukup / Kurang Memuaskan",
        3: "3.0 - Baik",
        4: "4.0 - Sangat Baik",
        5: "5.0 - Sangat Memuaskan"
    };

    stars.forEach(star => {
        star.addEventListener("click", function () {
            const val = parseInt(this.getAttribute("data-value"));
            ratingInput.value = val;
            ratingText.innerText = ratingLabels[val] || (val + ".0");

            stars.forEach((s, idx) => {
                if (idx < val) {
                    s.classList.remove("bi-star");
                    s.classList.add("bi-star-fill");
                } else {
                    s.classList.remove("bi-star-fill");
                    s.classList.add("bi-star");
                }
            });
        });
    });

    // Quick Chips Toggle
    const chipButtons = document.querySelectorAll("#quickChipsContainer .chip-btn");
    const hiddenChipsContainer = document.getElementById("hiddenChipsInputs");
    const selectedChips = new Set();

    chipButtons.forEach(btn => {
        btn.addEventListener("click", function () {
            const tag = this.getAttribute("data-tag");
            if (selectedChips.has(tag)) {
                selectedChips.delete(tag);
                this.classList.remove("btn-success", "text-white");
                this.classList.add("btn-outline-secondary");
            } else {
                selectedChips.add(tag);
                this.classList.remove("btn-outline-secondary");
                this.classList.add("btn-success", "text-white");
            }

            // Sync hidden inputs
            hiddenChipsContainer.innerHTML = "";
            selectedChips.forEach(t => {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "quick_tags[]";
                input.value = t;
                hiddenChipsContainer.appendChild(input);
            });
        });
    });
});

function openFeedbackModal(sessionId, mentorId, mentorName) {
    document.getElementById("feedback_session_id").value = sessionId;
    document.getElementById("feedback_mentor_id").value = mentorId;
    document.getElementById("feedback_mentor_name").innerText = mentorName;
    const modal = new bootstrap.Modal(document.getElementById("parentFeedbackModal"));
    modal.show();
}
</script>
