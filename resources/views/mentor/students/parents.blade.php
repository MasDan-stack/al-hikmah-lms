@extends('layouts.mentor')

@section('title', 'Daftar Orang Tua / Wali Santri Binaan')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="bi bi-people text-success me-2"></i>Daftar Orang Tua / Wali Santri</h3>
            <p class="text-muted small mb-0">Informasi kontak dan alamat orang tua dari santri binaan Al-Qur'an Anda.</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-journal-text text-success me-2"></i>Kontak Wali Santri Binaan</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-4">Nama Santri</th>
                            <th>Nama Orang Tua / Wali</th>
                            <th>No. WhatsApp / Telepon</th>
                            <th>Alamat Rumah</th>
                            <th class="text-end pe-4">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($parents as $item)
                            @php
                                $student = $item['student'];
                                $parent = $item['parent'];
                                $parentUser = $item['parent_user'];
                                $phone = $parentUser?->phone ?? $parent?->emergency_phone;
                                $cleanPhone = preg_replace('/[^0-9]/', '', $phone ?? '');
                                if (str_starts_with($cleanPhone, '0')) {
                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                }
                            @endphp
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-primary">{{ $student->full_name }}</div>
                                    <small class="text-muted">Usia: {{ $student->age }} Thn ({{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }})</small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $parentUser?->name ?? 'Orang Tua' }}</div>
                                    <small class="text-muted"><i class="bi bi-envelope me-1"></i>{{ $parentUser?->email ?? '-' }}</small>
                                </td>
                                <td>
                                    @if($phone)
                                        <span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-telephone text-success me-1"></i>{{ $phone }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="small text-secondary">{{ $parent?->address ?? $student->location ?? 'Online' }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    @if($phone)
                                        <a href="https://wa.me/{{ $cleanPhone }}?text=Assalamu'alaikum%20Bapak/Ibu%20wali%20dari%20{{ urlencode($student->full_name) }}" target="_blank" class="btn btn-sm btn-success fw-semibold">
                                            <i class="bi bi-whatsapp me-1"></i> Hubungi WA
                                        </a>
                                    @else
                                        <span class="btn btn-sm btn-light disabled">No Kontak</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-5 text-center text-muted">Belum ada data orang tua dari santri binaan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
