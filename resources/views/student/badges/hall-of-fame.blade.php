@extends('layouts.student')

@section('title', 'Hall of Fame: ' . $badge->name)
@section('header', 'Hall of Fame Lencana: ' . $badge->name)
@section('subheader', 'Daftar santri berprestasi yang telah berhasil meraih lencana ini.')

@section('content')
<div class="mb-4">
    <a href="{{ route('student.badges') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 mb-3">
        &larr; Kembali ke Koleksi Lencana
    </a>

    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-light-subtle">
        <div class="rounded-circle mx-auto my-2 bg-warning bg-opacity-10 text-warning border border-warning d-flex align-items-center justify-content-center"
             style="width: 80px; height: 80px;">
            <i class="bi {{ $badge->icon }} fs-1"></i>
        </div>
        <h4 class="fw-bold mb-1">{{ $badge->name }}</h4>
        <p class="text-muted small mx-auto mb-2" style="max-width: 500px;">{{ $badge->description }}</p>
        <span class="badge bg-success-subtle text-success fw-bold px-3 py-1 rounded-pill mx-auto">
            Hadiah +{{ $badge->points_reward }} Poin &bull; Total Peraih: {{ $recipients->total() }} Santri
        </span>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 p-4">
    <h6 class="fw-bold mb-3"><i class="bi bi-people-fill text-primary me-2"></i>Daftar Santri Peraih Lencana</h6>

    <div class="row g-2">
        @forelse($recipients as $recipient)
            <div class="col-12 col-md-6">
                <div class="p-3 rounded-3 border bg-light-subtle d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                            {{ substr($recipient->student?->getDisplayName() ?? 'S', 0, 1) }}
                        </div>
                        <div>
                            <div class="fw-bold">{{ $recipient->student?->getDisplayName() ?? 'Santri' }}</div>
                            <small class="text-muted">{{ $recipient->earned_at ? \Carbon\Carbon::parse($recipient->earned_at)->translatedFormat('d F Y, H:i') : '-' }}</small>
                        </div>
                    </div>
                    <span class="badge bg-warning-subtle text-dark rounded-pill px-2 py-1">Diraih 🎖️</span>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-4 text-muted">
                Belum ada santri yang meraih lencana ini. Jadilah yang pertama!
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $recipients->links() }}
    </div>
</div>
@endsection
