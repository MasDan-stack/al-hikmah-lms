@if (session('success') || session('error') || session('warning') || session('info') || session('status'))
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090;">
        <div id="liveGlobalToast" class="toast align-items-center border-0 shadow-lg rounded-4 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-3 py-3 px-3">
                    @if (session('success'))
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center p-2 flex-shrink-0" style="width: 36px; height: 36px;">
                            <i class="bi bi-check2-circle fs-5"></i>
                        </div>
                        <div>
                            <strong class="d-block text-success fw-bold">Berhasil!</strong>
                            <span class="text-dark small">{{ session('success') }}</span>
                        </div>
                    @elseif (session('error'))
                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center p-2 flex-shrink-0" style="width: 36px; height: 36px;">
                            <i class="bi bi-exclamation-octagon fs-5"></i>
                        </div>
                        <div>
                            <strong class="d-block text-danger fw-bold">Perhatian!</strong>
                            <span class="text-dark small">{{ session('error') }}</span>
                        </div>
                    @elseif (session('warning'))
                        <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center p-2 flex-shrink-0" style="width: 36px; height: 36px;">
                            <i class="bi bi-exclamation-triangle fs-5"></i>
                        </div>
                        <div>
                            <strong class="d-block text-dark fw-bold">Peringatan!</strong>
                            <span class="text-dark small">{{ session('warning') }}</span>
                        </div>
                    @elseif (session('info') || session('status'))
                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center p-2 flex-shrink-0" style="width: 36px; height: 36px;">
                            <i class="bi bi-info-circle fs-5"></i>
                        </div>
                        <div>
                            <strong class="d-block text-info-emphasis fw-bold">Informasi</strong>
                            <span class="text-dark small">{{ session('info') ?? session('status') }}</span>
                        </div>
                    @endif
                </div>
                <button type="button" class="btn-close me-3 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toastEl = document.getElementById('liveGlobalToast');
            if (toastEl && typeof bootstrap !== 'undefined') {
                const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
                toast.show();
            }
        });
    </script>
@endif
