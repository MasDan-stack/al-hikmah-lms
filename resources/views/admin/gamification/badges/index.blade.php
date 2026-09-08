@extends('layouts.admin')

@section('title', 'Katalog Lencana Gamifikasi | AL-HIKMAH')
@section('header', 'Katalog Lencana Gamifikasi')
@section('subheader', 'Kelola daftar lencana Islami, icon, reward poin, dan kriteria penghargaan santri')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Daftar Lencana Islami (Badges)</h5>
            <small class="text-muted">Total {{ $badges->count() }} Lencana aktif terdaftar dalam sistem</small>
        </div>
        <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createBadgeModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Lencana Baru
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Lencana</th>
                        <th>Kode</th>
                        <th>Kategori</th>
                        <th>Poin Reward</th>
                        <th>Total Peraih</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($badges as $badge)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi {{ $badge->icon }} fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $badge->name }}</div>
                                        <small class="text-muted">{{ $badge->description }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><code>{{ $badge->code }}</code></td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                    {{ strtoupper($badge->category) }}
                                </span>
                            </td>
                            <td><strong class="text-warning">+{{ $badge->points_reward }} Pts</strong></td>
                            <td><strong>{{ $badge->students_count }}</strong> Santri</td>
                            <td>
                                @if($badge->is_active)
                                    <span class="badge bg-success-subtle text-success rounded-pill">Aktif</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-pill">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <form action="{{ route('admin.badges.destroy', $badge->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus lencana ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Belum ada lencana terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Lencana -->
<div class="modal fade" id="createBadgeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('admin.badges.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Lencana Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Kode Lencana (Unik)</label>
                        <input type="text" name="code" class="form-control" placeholder="Contoh: B16" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Lencana</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Bintang Tahajjud" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Diberikan kepada santri yang..." required></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Icon (Bootstrap Icon)</label>
                            <input type="text" name="icon" class="form-control" value="bi-award" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Kategori</label>
                            <select name="category" class="form-select" required>
                                <option value="milestone">Milestone</option>
                                <option value="streak">Streak</option>
                                <option value="achievement">Achievement</option>
                                <option value="leaderboard">Leaderboard</option>
                                <option value="adab">Adab</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Hadiah Poin (Points Reward)</label>
                        <input type="number" name="points_reward" class="form-control" value="100" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Simpan Lencana</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
