@extends('layouts.admin')

@section('title', 'Pengaturan Akun & Audit Keamanan | AL-HIKMAH')
@section('header', 'Pengaturan Akun & Audit Keamanan')
@section('subheader', 'Konfigurasi format email santri otomatis dan pemantauan log audit reset password')

@section('content')
<div class="container-fluid px-0">
    <div class="row g-4 mb-4">
        <!-- Pengaturan Domain Santri -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-3"><i class="bi bi-gear-fill text-success me-2"></i>Konfigurasi Domain Santri</h5>
                <p class="text-muted small">Domain ini digunakan untuk men-generate email akun santri secara otomatis dengan format <code>{3hurufdepan}.{namabelakang}@{domain}</code>.</p>
                
                <form action="{{ route('admin.gamification.settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Domain Institusi</label>
                        <div class="input-group">
                            <span class="input-group-text">@</span>
                            <input type="text" name="institution_domain" class="form-control" value="{{ $domainSetting }}" placeholder="alhikmah.com" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <i class="bi bi-check2 me-1"></i> Simpan Pengaturan
                    </button>
                </form>
            </div>
        </div>

        <!-- Kebijakan Keamanan -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-light-subtle">
                <h5 class="fw-bold mb-3"><i class="bi bi-shield-check text-primary me-2"></i>Kebijakan Keamanan Sistem (Security Policy)</h5>
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Zero Plain-text Storage:</strong> Password pengguna selalu di-hash menggunakan algoritma Bcrypt standar Laravel.</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Zero Plain-text Display:</strong> Password saat ini tidak pernah ditampilkan di antarmuka orang tua maupun santri.</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Audit Trail Lengkap:</strong> Setiap reset password tercatat lengkap dengan IP address, User-Agent, inisiator, dan saluran notifikasi.</li>
                    <li class="mb-0"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Strong Complexity:</strong> Password baru wajib memenuhi kombinasi huruf besar, kecil, angka, dan karakter simbol khusus.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Audit Log Table -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-clock-history text-secondary me-2"></i>Log Audit Reset Password (Security Audit Trail)</h5>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Waktu</th>
                        <th>User Santri</th>
                        <th>Inisiator (Changed By)</th>
                        <th>Metode</th>
                        <th>IP Address</th>
                        <th>Saluran Notifikasi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($auditLogs as $log)
                        <tr>
                            <td class="small text-muted">{{ $log->created_at->translatedFormat('d M Y, H:i:s') }}</td>
                            <td>
                                <span class="fw-semibold">{{ $log->user?->name ?? 'User #' . $log->user_id }}</span>
                                <small class="text-muted d-block">{{ $log->user?->email }}</small>
                            </td>
                            <td>
                                @if($log->changed_by)
                                    <span class="badge bg-primary-subtle text-primary rounded-pill">
                                        {{ $log->changer?->name ?? 'User #' . $log->changed_by }} (Orang Tua/Admin)
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">Mandiri (Santri)</span>
                                @endif
                            </td>
                            <td><code>{{ strtoupper($log->reset_method) }}</code></td>
                            <td class="small text-muted">{{ $log->ip_address }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ strtoupper($log->notification_channel) }}
                                </span>
                            </td>
                            <td>
                                @if($log->notification_status === 'sent')
                                    <span class="badge bg-success-subtle text-success rounded-pill">Terkirim</span>
                                @elseif($log->notification_status === 'fallback')
                                    <span class="badge bg-warning-subtle text-warning rounded-pill">Fallback Email</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-pill">Gagal</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Belum ada riwayat aktivitas reset password.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $auditLogs->links() }}
        </div>
    </div>
</div>
@endsection
