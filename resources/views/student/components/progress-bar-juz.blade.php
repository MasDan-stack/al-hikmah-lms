@props(['juz' => null])

@if($juz)
<div class="p-3 mb-2 rounded-3 border bg-light-subtle transition-hover">
    <div class="d-flex align-items-center justify-content-between mb-1">
        <div class="d-flex align-items-center gap-2">
            <span class="badge {{ $juz->status === 'mutqin' ? 'bg-warning text-dark' : ($juz->status === 'completed' ? 'bg-success text-white' : 'bg-primary-subtle text-primary') }} rounded-pill px-3 fw-bold">
                Juz {{ $juz->juz_number }}
            </span>
            <span class="fw-bold" style="font-size: 0.9rem;">
                @if($juz->status === 'mutqin')
                    <span class="text-warning"><i class="bi bi-patch-check-fill me-1"></i> Mutqin (Lulus Ujian)</span>
                @elseif($juz->status === 'completed')
                    <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Khatam</span>
                @elseif($juz->status === 'in_progress')
                    <span class="text-primary"><i class="bi bi-arrow-repeat me-1"></i> Sedang Dihafal</span>
                @else
                    <span class="text-muted">Belum Dimulai</span>
                @endif
            </span>
        </div>
        <div class="text-end">
            <span class="fw-bold text-success" style="font-size: 0.9rem;">{{ $juz->percentage }}%</span>
            <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $juz->ayat_hafal }} / {{ $juz->total_ayat }} Ayat</small>
        </div>
    </div>

    <div class="progress rounded-pill mt-2" style="height: 10px;">
        <div class="progress-bar {{ $juz->status === 'mutqin' ? 'bg-warning' : ($juz->percentage >= 100 ? 'bg-success' : 'bg-primary') }}" 
             role="progressbar" 
             style="width: {{ $juz->percentage }}%; transition: width 0.8s ease;" 
             aria-valuenow="{{ $juz->percentage }}" 
             aria-valuemin="0" 
             aria-valuemax="100">
        </div>
    </div>
</div>
@endif
