@extends('layouts.parent')

@section('title', 'Daftar Anak Saya')
@section('header', 'Daftar Anak Binaan')
@section('subheader', 'Daftar ananda yang terdaftar di AL-HIKMAH LMS')

@section('content')
    <!-- Flash Alert Notification Messages -->
    @if (session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between p-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between p-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
                <div class="fw-semibold">{{ session('warning') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between p-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-people-fill text-primary me-2"></i>Anak Binaan Saya</h4>
            <p class="text-muted small mb-0">Kelola data anak, capaian lencana, akun login santri, dan pantau progres pembelajaran.</p>
        </div>
        <a href="{{ route('parent.profile.children') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Tambah Anak Baru
        </a>
    </div>

    @if($children->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 bg-white p-5 text-center">
            <i class="bi bi-person-x fs-1 text-secondary mb-3"></i>
            <h5 class="fw-bold text-dark">Belum Ada Anak Terdaftar</h5>
            <p class="text-muted small">Anda belum memiliki data anak terhubung di akun ini.</p>
            <div>
                <a href="{{ route('parent.profile.children') }}" class="btn btn-primary rounded-pill px-4">
                    Tambahkan Data Anak
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($children as $child)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-white h-100 p-3 position-relative">
                        <div class="d-flex align-items-center gap-3 mb-3 border-bottom pb-3">
                            <div class="rounded-circle p-3 bg-primary-subtle text-primary fs-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">{{ $child->user?->name ?? $child->full_name }}</h5>
                                <small class="text-muted d-block mb-1">{{ $child->user?->email }}</small>
                                <span class="badge bg-success-subtle text-success rounded-pill px-3">Santri Aktif</span>
                            </div>
                        </div>

                        <!-- Gamifikasi Mini Badges -->
                        <div class="p-2 rounded-3 bg-light mb-3 d-flex justify-content-around text-center small">
                            <div>
                                <div class="fw-bold text-warning">{{ number_format($child->total_points ?: 0) }}</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Poin</small>
                            </div>
                            <div class="border-start"></div>
                            <div>
                                <div class="fw-bold text-danger">{{ $child->current_streak ?: 0 }} Hari</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Streak</small>
                            </div>
                            <div class="border-start"></div>
                            <div>
                                <div class="fw-bold text-primary">{{ $child->earnedBadges->count() }}</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Lencana</small>
                            </div>
                        </div>

                        <div class="small text-secondary mb-3">
                            <div class="mb-1"><i class="bi bi-calendar-event me-2"></i>Usia: {{ $child->age }} Tahun</div>
                            <div class="mb-1"><i class="bi bi-gender-ambiguous me-2"></i>Jenis Kelamin: {{ $child->gender === 'L' ? 'Laki-Laki' : 'Perempuan' }}</div>
                            <div class="mb-1"><i class="bi bi-geo-alt me-2"></i>Lokasi: {{ $child->location ?? 'Online / Home' }}</div>
                            <div>
                                <i class="bi bi-person-workspace me-2"></i>Mentor: 
                                @if($child->getActiveMentor())
                                    <strong class="text-dark">Ustadz/ah {{ $child->getActiveMentor()->getDisplayName() }}</strong>
                                @else
                                    <span class="text-muted">Belum ditentukan</span>
                                @endif
                            </div>
                        </div>

                        <!-- Kredensial Login Santri & Password Default -->
                        <div class="p-2.5 rounded-3 bg-light border mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small" style="font-size: 0.76rem;"><i class="bi bi-envelope me-1"></i>Email Login:</span>
                                <code class="text-primary fw-bold" style="font-size: 0.78rem;">{{ $child->user?->email }}</code>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small" style="font-size: 0.76rem;"><i class="bi bi-key me-1"></i>Password Awal:</span>
                                <span class="badge bg-secondary-subtle text-dark border px-2 py-0.5 font-monospace" style="font-size: 0.75rem;">santri123</span>
                            </div>
                            <small class="text-muted d-block" style="font-size: 0.7rem; line-height: 1.25;">
                                <i class="bi bi-info-circle text-warning me-1"></i>Disarankan ananda segera mengubah password default setelah login.
                            </small>
                        </div>

                        <div class="mt-auto d-grid gap-2">
                            <a href="{{ route('parent.children.show', $child->id) }}" class="btn btn-outline-primary rounded-pill fw-bold">
                                <i class="bi bi-graph-up-arrow me-1"></i> Lihat Progres & Grafik
                            </a>
                            <button type="button" class="btn btn-outline-success rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#resetPasswordModal_{{ $child->id }}">
                                <i class="bi bi-whatsapp me-1"></i> Reset & Kirim Password
                            </button>
                            <a href="{{ route('parent.children.report', $child->id) }}" class="btn btn-outline-dark rounded-pill fw-bold" target="_blank">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Cetak Laporan PDF
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Modal Konfirmasi Reset Password -->
                <div class="modal fade" id="resetPasswordModal_{{ $child->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 rounded-4 shadow">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold text-dark">Reset Password Akun Santri</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center py-4">
                                <div class="rounded-circle bg-success bg-opacity-10 text-success mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                                    <i class="bi bi-shield-lock-fill fs-2"></i>
                                </div>
                                <h6 class="fw-bold mb-2">Konfirmasi Buat & Kirim Password Baru?</h6>
                                <p class="text-muted small px-3">
                                    Sistem akan membuat password acak baru yang aman untuk akun santri ananda <strong>{{ $child->getDisplayName() }}</strong> (<code>{{ $child->user?->email }}</code>), dan mengirimkannya langsung ke nomor WhatsApp / Email Anda.
                                </p>
                                <div class="alert alert-info border-0 rounded-3 py-2 small mb-0">
                                    <i class="bi bi-info-circle me-1"></i> Sesuai standar keamanan, password lama akan diganti seketika.
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0 justify-content-center">
                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                <form action="{{ route('parent.children.reset-password', $child->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                                        <i class="bi bi-send-check me-1"></i> Ya, Reset & Kirim Sekarang
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
