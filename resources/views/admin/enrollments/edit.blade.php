@extends('layouts.admin')

@section('title', 'Proses Pendaftaran & Negosiasi Jadwal | Admin AL-HIKMAH')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Summary Header -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom pb-3 mb-3">
                        <div>
                            <span class="badge bg-{{ $enrollment->status->badgeClass() }} px-3 py-2 rounded-pill">
                                {{ $enrollment->status->label() }}
                            </span>
                            <h5 class="fw-bold mt-2 mb-0">Permohonan Pendaftaran #ENR-{{ str_pad($enrollment->id, 5, '0', STR_PAD_LEFT) }}</h5>
                        </div>
                        <div class="text-end">
                            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-arrow-left me-1"></i> Kembali ke Antrean
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between p-3" role="alert">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                                <div class="fw-semibold">{{ session('success') }}</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between p-3" role="alert">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                                <div class="fw-semibold">{{ session('error') }}</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 p-3" role="alert">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
                                <span class="fw-bold">Terdapat kesalahan input:</span>
                            </div>
                            <ul class="mb-0 ps-4 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block">Santri / Anak</small>
                                <span class="fw-bold">{{ $enrollment->student->getDisplayName() }}</span>
                                <small class="d-block text-muted">{{ $enrollment->student->age }} Tahun</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block">Orang Tua / Wali</small>
                                <span class="fw-bold">{{ $enrollment->student->getParentNameAttribute() }}</span>
                                <small class="d-block text-muted"><i class="bi bi-telephone me-1"></i>{{ $enrollment->student->getParentPhoneAttribute() }}</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block">Program & Harga Terkunci</small>
                                <span class="fw-bold text-success">{{ $enrollment->program->name }}</span>
                                <small class="d-block text-muted">{{ $enrollment->formatted_price }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SMART MATCHMAKING AI v2.0 INTERACTIVE CARDS -->
            @if(isset($recommendations) && $recommendations->isNotEmpty())
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">
                            <i class="bi bi-robot text-primary me-2"></i>Rekomendasi Guru Terbaik (Smart Matchmaking AI v2.0)
                        </h5>
                        <small class="text-muted">Kalkulasi 5 Faktor: Gender (25%), Jarak (20%), Kuota (25%), Spesialisasi (20%), Beban (10%) + Islamic Context Rules</small>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-semibold small">
                            <i class="bi bi-lightning-charge-fill me-1"></i>High-Performance Cached Engine
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        @foreach($recommendations as $index => $rec)
                            @php
                                $m = $rec['mentor'];
                                $score = (float) $rec['score'];
                                $b = $rec['breakdown'] ?? [];
                                $rankBadges = [
                                    0 => ['title' => '🥇 Pilihan Terbaik', 'badge' => 'bg-warning-subtle text-dark border-warning-subtle', 'theme' => 'border-primary border-2 shadow-sm'],
                                    1 => ['title' => '🥈 Rekomendasi Ke-2 (Shadow Mentor)', 'badge' => 'bg-secondary-subtle text-dark border-secondary-subtle', 'theme' => 'border-0 shadow-sm'],
                                    2 => ['title' => '🥉 Rekomendasi Ke-3', 'badge' => 'bg-light text-muted border', 'theme' => 'border-0 shadow-sm']
                                ];
                                $currentRank = $rankBadges[$index] ?? ['title' => "Rekomendasi #".($index+1), 'badge' => 'bg-light text-dark', 'theme' => 'border-0 shadow-sm'];
                                $shadowMentorId = isset($recommendations[1]) ? $recommendations[1]['mentor']->id : null;
                            @endphp
                            <div class="col-lg-4 col-md-6">
                                <div class="card h-100 rounded-4 {{ $currentRank['theme'] }} bg-white p-3 d-flex flex-column position-relative">
                                    @if($score >= 95.0)
                                        <div class="position-absolute top-0 end-0 m-3">
                                            <span class="badge bg-success text-white rounded-pill px-2.5 py-1 small shadow-sm">
                                                <i class="bi bi-shield-check me-1"></i>Auto-Assign Match
                                            </span>
                                        </div>
                                    @endif

                                    <div class="text-center mb-3">
                                        <span class="badge {{ $currentRank['badge'] }} rounded-pill border px-3 py-1 fw-bold mb-2 small">{{ $currentRank['title'] }}</span>
                                        <div class="avatar-circle bg-primary-subtle text-primary mx-auto d-flex align-items-center justify-content-center rounded-circle fw-bold fs-4 shadow-sm" style="width: 58px; height: 58px;">
                                            {{ strtoupper(substr($m->getDisplayName(), 0, 2)) }}
                                        </div>
                                        <h6 class="fw-bold mt-2 mb-0 text-dark">{{ $m->getDisplayName() }}</h6>
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            {{ is_array($m->specializations) ? implode(', ', $m->specializations) : ($m->specialization ?? 'Tahsin & Tahfidz') }}
                                        </small>
                                    </div>

                                    <!-- Compatibility Score Header -->
                                    <div class="p-2.5 rounded-3 bg-light text-center mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small text-muted fw-semibold">Kecocokan Total</span>
                                            <strong class="text-{{ $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger') }} fs-5">{{ $score }}%</strong>
                                        </div>
                                        <div class="progress" style="height: 7px;">
                                            <div class="progress-bar bg-{{ $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger') }} rounded-pill" style="width: {{ $score }}%;"></div>
                                        </div>
                                    </div>

                                    <!-- Score Breakdown -->
                                    <div class="small text-muted mb-4 flex-grow-1" style="font-size: 0.78rem;">
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span>Gender Match:</span>
                                            <strong class="text-dark">{{ $b['gender'] ?? 0 }}%</strong>
                                        </div>
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span>Jarak Lokasi:</span>
                                            <strong class="text-dark">{{ $b['location'] ?? 0 }}% @if($rec['distance_km'] < 900) ({{ $rec['distance_km'] }} km) @endif</strong>
                                        </div>
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span>Sisa Kuota Hari:</span>
                                            <strong class="text-dark">{{ $b['slot'] ?? 0 }}%</strong>
                                        </div>
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span>Spesialisasi:</span>
                                            <strong class="text-dark">{{ $b['specialization'] ?? 0 }}%</strong>
                                        </div>
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span>Beban Mengajar:</span>
                                            <strong class="text-dark">{{ $b['load'] ?? 0 }}% ({{ $rec['active_load'] ?? $m->students_count }} santri)</strong>
                                        </div>
                                        @if(!empty($b['gamification_boost']))
                                            <div class="d-flex justify-content-between py-1 text-success fw-semibold">
                                                <span>Lencana Teladan:</span>
                                                <span>+{{ $b['gamification_boost'] }}% 🌟</span>
                                            </div>
                                        @endif
                                        @if(!empty($b['prayer_penalty']))
                                            <div class="d-flex justify-content-between py-1 text-danger fw-semibold">
                                                <span>Buffer Sholat:</span>
                                                <span>-{{ $b['prayer_penalty'] }}% ⚠️</span>
                                            </div>
                                        @endif
                                    </div>

                                    <form action="{{ route('admin.enrollments.assign-recommended', $enrollment->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="mentor_id" value="{{ $m->id }}">
                                        <input type="hidden" name="shadow_mentor_id" value="{{ $shadowMentorId }}">
                                        <input type="hidden" name="score" value="{{ $score }}">
                                        <input type="hidden" name="start_date" value="{{ $enrollment->start_date?->format('Y-m-d') ?? date('Y-m-d', strtotime('+3 days')) }}">
                                        <input type="hidden" name="score_breakdown" value="{{ json_encode($b) }}">
                                        <button type="submit" class="btn {{ $index === 0 ? 'btn-primary' : 'btn-outline-primary' }} w-100 rounded-pill fw-bold py-2 shadow-sm">
                                            <i class="bi bi-check-circle me-1"></i> Pilih {{ $m->getDisplayName() }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Explainable AI Inspection ("Why Not...?") -->
                    @php
                        $topMentorIds = $recommendations->pluck('mentor.id')->toArray();
                        $otherMentors = $mentors->whereNotIn('id', $topMentorIds);
                    @endphp
                    @if($otherMentors->isNotEmpty())
                    <div class="mt-4 pt-3 border-top">
                        <a class="text-decoration-none small text-muted d-flex align-items-center justify-content-between p-2 rounded-3 bg-light" data-bs-toggle="collapse" href="#explainableAiCollapse" role="button" aria-expanded="false">
                            <span class="fw-semibold text-dark"><i class="bi bi-question-diamond-fill me-1 text-primary"></i> Explainable AI: Mengapa guru lain tidak masuk Top 3?</span>
                            <span class="badge bg-white text-muted border rounded-pill">Inspeksi {{ $otherMentors->count() }} Guru Lain <i class="bi bi-chevron-down ms-1"></i></span>
                        </a>
                        <div class="collapse mt-3" id="explainableAiCollapse">
                            <div class="row g-2">
                                @foreach($otherMentors->take(6) as $otherMentor)
                                    @php
                                        $explanations = app(\App\Services\MentorMatchingService::class)->explainMentorExclusion($enrollment, $otherMentor->id);
                                    @endphp
                                    <div class="col-md-6">
                                        <div class="p-2.5 bg-light rounded-3 border">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <strong class="text-dark small">{{ $otherMentor->getDisplayName() }}</strong>
                                                <small class="text-muted" style="font-size: 0.7rem;">{{ $otherMentor->specialization ?? 'Umum' }}</small>
                                            </div>
                                            <ul class="mb-0 ps-3 text-muted" style="font-size: 0.75rem;">
                                                @foreach($explanations as $reason)
                                                    <li>{{ $reason }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Two Action Panels: Accept vs Counter-Offer -->
            <div class="row g-4">
                <!-- Panel Opsi A: Setujui Jadwal Request -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-success-subtle border-0 pt-3 px-4">
                            <h6 class="fw-bold text-success-emphasis mb-0"><i class="bi bi-check-circle me-2"></i>OPSI A: Setujui Jadwal Orang Tua</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="p-3 bg-light rounded-3 mb-3 small">
                                <p class="mb-1"><strong>Hari Diminta:</strong> {{ $enrollment->requested_days_label }}</p>
                                <p class="mb-1"><strong>Jam Diminta:</strong> {{ $enrollment->requested_time_label }}</p>
                                <p class="mb-0"><strong>Catatan Wali:</strong> {{ $enrollment->parent_notes ?? '-' }}</p>
                            </div>

                            <form action="{{ route('admin.enrollments.accept', $enrollment->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Pilih Mentor Pembimbing <span class="text-danger">*</span></label>
                                    <select name="mentor_id" class="form-select" required>
                                        <option value="">-- Pilih Mentor --</option>
                                        @foreach($mentors as $mentor)
                                            <option value="{{ $mentor->id }}" {{ old('mentor_id', $enrollment->mentor_id) == $mentor->id ? 'selected' : '' }}>
                                                {{ $mentor->getDisplayName() }} (Keahlian: {{ $mentor->specialization ?? '-' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Tanggal Mulai Belajar <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $enrollment->start_date?->format('Y-m-d') ?? date('Y-m-d', strtotime('+3 days'))) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Catatan Admin (Opsional)</label>
                                    <textarea name="admin_notes" class="form-control" rows="2" placeholder="Catatan untuk orang tua...">{{ old('admin_notes') }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-success w-100 rounded-pill py-2">
                                    <i class="bi bi-check-lg me-1"></i> Setujui Jadwal & Terbitkan Invoice
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Panel Opsi B: Tawarkan Alternatif Jadwal (Counter-Offer) -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-warning-subtle border-0 pt-3 px-4">
                            <h6 class="fw-bold text-warning-emphasis mb-0"><i class="bi bi-chat-dots me-2"></i>OPSI B: Tawarkan Alternatif Jadwal</h6>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('admin.enrollments.offer', $enrollment->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Saran Mentor (Opsional)</label>
                                    <select name="mentor_id" class="form-select">
                                        <option value="">-- Pilih Mentor Jika Ada --</option>
                                        @foreach($mentors as $mentor)
                                            <option value="{{ $mentor->id }}" {{ old('mentor_id', $enrollment->mentor_id) == $mentor->id ? 'selected' : '' }}>
                                                {{ $mentor->getDisplayName() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small d-block">Tawarkan Hari Alternatif <span class="text-danger">*</span></label>
                                    <div class="row g-2">
                                        @foreach($days as $val => $dayLabel)
                                            <div class="col-6">
                                                <div class="form-check p-1 border rounded text-center">
                                                    <input class="form-check-input ms-0 me-1" type="checkbox" name="offered_days[]" value="{{ $val }}" id="offered_{{ $val }}" {{ is_array(old('offered_days', $enrollment->offered_days)) && in_array($val, old('offered_days', $enrollment->offered_days ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="offered_{{ $val }}">{{ $dayLabel }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Jam Alternatif (Opsional)</label>
                                    <input type="time" name="offered_time" class="form-control" value="{{ old('offered_time', $enrollment->offered_time) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Penjelasan Alasan / Catatan Admin <span class="text-danger">*</span></label>
                                    <textarea name="admin_notes" class="form-control" rows="2" placeholder="Contoh: Jadwal Senin jam 15:00 penuh, mentor bersedia di hari Selasa jam 16:00." required>{{ old('admin_notes', $enrollment->admin_notes) }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-warning w-100 rounded-pill py-2">
                                    <i class="bi bi-send me-1"></i> Kirim Tawaran Alternatif Ke Orang Tua
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
