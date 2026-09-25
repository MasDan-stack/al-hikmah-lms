@extends('layouts.admin')

@section('title', 'WhatsApp Mass Broadcast System')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--text-primary);">
                <i class="bi bi-whatsapp text-success me-2"></i>WhatsApp Mass Broadcast System
            </h3>
            <p class="text-muted small mb-0">Kirim pengumuman resmi, info libur, jadwal evaluasi, atau pengingat SPP massal dengan variabel personalisasi cerdas.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Broadcast Form Console -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
                <h5 class="fw-bold mb-3" style="color: var(--text-primary);">
                    <i class="bi bi-send-fill text-primary me-2"></i>Formulir Broadcast Pesan
                </h5>

                <form method="POST" action="{{ route('admin.broadcast.send') }}" id="broadcastForm">
                    @csrf

                    <!-- Judul Broadcast / Internal Reference -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Judul Pengumuman (Referensi Internal)</label>
                        <input type="text" name="title" id="inputTitle" class="form-control rounded-3" placeholder="Contoh: Pengumuman Libur Awal Ramadhan 1448 H" required value="{{ old('title') }}">
                    </div>

                    <!-- Target Penerima -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Kelompok Sasaran</label>
                            <select name="target_type" id="selectTargetType" class="form-select rounded-3" required onchange="handleTargetTypeChange()">
                                <option value="all">Semua Wali Santri Terdaftar</option>
                                <option value="program">Wali Santri Per Program</option>
                                <option value="mentor">Wali Santri Binaan Guru Tertentu</option>
                            </select>
                        </div>

                        <!-- Dropdown Dinamis Target ID -->
                        <div class="col-md-6" id="targetIdContainer" style="display: none;">
                            <label class="form-label small fw-semibold text-muted" id="targetIdLabel">Pilih Spesifik</label>
                            
                            <!-- Program Selector -->
                            <select name="target_id" id="selectProgram" class="form-select rounded-3 target-select" style="display: none;">
                                @foreach ($programs as $prog)
                                    <option value="{{ $prog->id }}">{{ $prog->name }}</option>
                                @endforeach
                            </select>

                            <!-- Mentor Selector -->
                            <select name="target_id" id="selectMentor" class="form-select rounded-3 target-select" style="display: none;" disabled>
                                @foreach ($mentors as $mentor)
                                    <option value="{{ $mentor->id }}">{{ $mentor->getDisplayName() }} ({{ $mentor->specialization }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Template Preset Loader -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                        <label class="form-label small fw-semibold text-muted mb-0">Isi Pesan WhatsApp</label>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle rounded-pill" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-magic me-1"></i>Pilih Template Siap Pakai
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                @foreach ($presets as $preset)
                                    <li>
                                        <button class="dropdown-item small" type="button" onclick="loadPreset('{{ $preset['id'] }}')">
                                            {{ $preset['name'] }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Variabel Dinamis Helper Chips -->
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <span class="small text-muted me-1 align-self-center" style="font-size: 0.75rem;">Variabel:</span>
                        <button type="button" class="btn btn-sm btn-light border rounded-pill py-0 px-2 small" onclick="insertVariable('{nama_ortu}')">{nama_ortu}</button>
                        <button type="button" class="btn btn-sm btn-light border rounded-pill py-0 px-2 small" onclick="insertVariable('{nama_anak}')">{nama_anak}</button>
                        <button type="button" class="btn btn-sm btn-light border rounded-pill py-0 px-2 small" onclick="insertVariable('{program}')">{program}</button>
                        <button type="button" class="btn btn-sm btn-light border rounded-pill py-0 px-2 small" onclick="insertVariable('{tanggal}')">{tanggal}</button>
                        <button type="button" class="btn btn-sm btn-light border rounded-pill py-0 px-2 small" onclick="insertVariable('{lembaga}')">{lembaga}</button>
                    </div>

                    <!-- Textarea Pesan -->
                    <div class="mb-3">
                        <textarea name="message_template" id="messageTemplate" class="form-control rounded-3 font-monospace" rows="8" placeholder="Tuliskan pesan WhatsApp di sini..." required oninput="updateLivePreview()">{{ old('message_template', "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nYth. Bapak/Ibu {nama_ortu},\n\nKami menginformasikan bahwa...\n\nWassalamu'alaikum Wr. Wb.\n*{lembaga}*") }}</textarea>
                    </div>

                    <!-- Submit Button Strip -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top" style="border-color: var(--border-color) !important;">
                        <span class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-pill">
                            <i class="bi bi-people-fill me-1"></i>Estimasi: <span id="recipientCountBadge">{{ $defaultRecipientsCount }}</span> Wali Santri
                        </span>

                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm" onclick="return confirm('Apakah Anda yakin ingin mengirimkan pesan broadcast WhatsApp ini ke seluruh wali santri yang dipilih?')">
                            <i class="bi bi-whatsapp me-1"></i>Kirim Broadcast Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Phone Mockup Live Preview -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
                <h5 class="fw-bold mb-3" style="color: var(--text-primary);">
                    <i class="bi bi-phone-fill text-success me-2"></i>Simulasi Tampilan WhatsApp
                </h5>

                <!-- Phone Frame -->
                <div class="mx-auto shadow-lg rounded-4 overflow-hidden border border-3 border-dark" style="max-width: 320px; background: #efeae2; min-height: 460px; display: flex; flex-direction: column;">
                    <!-- Phone Top Bar -->
                    <div class="bg-dark text-white p-2 d-flex align-items-center justify-content-between small" style="font-size: 0.75rem;">
                        <span>09:41</span>
                        <div class="d-flex gap-1">
                            <i class="bi bi-wifi"></i>
                            <i class="bi bi-battery-full"></i>
                        </div>
                    </div>

                    <!-- WhatsApp Header -->
                    <div class="p-2 d-flex align-items-center gap-2 text-white" style="background: #075e54;">
                        <i class="bi bi-arrow-left"></i>
                        <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                            AH
                        </div>
                        <div class="flex-grow-1" style="line-height: 1.2;">
                            <div class="fw-bold small">AL-HIKMAH Official</div>
                            <div class="text-white text-opacity-75" style="font-size: 0.65rem;">online</div>
                        </div>
                        <i class="bi bi-three-dots-vertical"></i>
                    </div>

                    <!-- WhatsApp Chat Area -->
                    <div class="p-3 flex-grow-1 d-flex flex-column justify-content-start">
                        <!-- Chat Bubble -->
                        <div class="p-3 rounded-3 shadow-sm" style="background: #ffffff; border-radius: 8px 8px 8px 0 !important; max-width: 90%; font-size: 0.82rem; line-height: 1.4; color: #111b21;">
                            <div id="phonePreviewText" style="white-space: pre-wrap; word-break: break-word;">
                                Memuat simulasi pesan...
                            </div>
                            <div class="text-end text-muted mt-1" style="font-size: 0.65rem;">
                                09:41 <i class="bi bi-check2-all text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center text-muted small mt-3">
                    <i class="bi bi-info-circle me-1"></i>Preview disimulasikan menggunakan data salah satu wali santri aktif.
                </div>
            </div>
        </div>
    </div>

    <!-- Broadcast History Table -->
    <div class="card border-0 shadow-sm rounded-4 p-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
                <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Pengiriman Broadcast Terakhir
            </h5>
            <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $broadcastLogs->count() }} Riwayat</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase text-muted">
                    <tr>
                        <th>Tanggal & Waktu</th>
                        <th>Petugas Admin</th>
                        <th>Judul Pengumuman</th>
                        <th>Target Sasaran</th>
                        <th class="text-center">Total Penerima</th>
                        <th class="text-center">Terkirim</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($broadcastLogs as $log)
                        @php
                            $details = $log->new_values ?? [];
                        @endphp
                        <tr>
                            <td class="small text-muted">
                                {{ $log->created_at?->translatedFormat('d/m/Y H:i') }} WIB
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $log->user?->name ?? 'Administrator' }}</span>
                            </td>
                            <td class="fw-bold text-primary">
                                {{ $details['title'] ?? 'Pengumuman Massal' }}
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">{{ ucfirst($details['target_type'] ?? 'all') }}</span>
                            </td>
                            <td class="text-center fw-bold">{{ $details['total_recipients'] ?? 0 }}</td>
                            <td class="text-center text-success fw-bold">{{ $details['success_count'] ?? 0 }}</td>
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-10 text-success">BERHASIL</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada riwayat pengiriman broadcast WhatsApp.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const presetsData = {!! json_encode($presets) !!};

    function handleTargetTypeChange() {
        const type = document.getElementById('selectTargetType').value;
        const container = document.getElementById('targetIdContainer');
        const progSelect = document.getElementById('selectProgram');
        const mentorSelect = document.getElementById('selectMentor');
        const label = document.getElementById('targetIdLabel');

        if (type === 'program') {
            container.style.display = 'block';
            progSelect.style.display = 'block';
            progSelect.disabled = false;
            mentorSelect.style.display = 'none';
            mentorSelect.disabled = true;
            label.innerText = 'Pilih Program:';
        } else if (type === 'mentor') {
            container.style.display = 'block';
            progSelect.style.display = 'none';
            progSelect.disabled = true;
            mentorSelect.style.display = 'block';
            mentorSelect.disabled = false;
            label.innerText = 'Pilih Guru Pembimbing:';
        } else {
            container.style.display = 'none';
            progSelect.disabled = true;
            mentorSelect.disabled = true;
        }

        updateLivePreview();
    }

    function insertVariable(varName) {
        const textarea = document.getElementById('messageTemplate');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;

        textarea.value = text.substring(0, start) + varName + text.substring(end);
        textarea.focus();
        textarea.selectionStart = textarea.selectionEnd = start + varName.length;

        updateLivePreview();
    }

    function loadPreset(presetId) {
        const found = presetsData.find(p => p.id === presetId);
        if (found) {
            document.getElementById('messageTemplate').value = found.template;
            document.getElementById('inputTitle').value = found.name;
            updateLivePreview();
        }
    }

    let previewDebounceTimer = null;
    function updateLivePreview() {
        clearTimeout(previewDebounceTimer);
        previewDebounceTimer = setTimeout(() => {
            const template = document.getElementById('messageTemplate').value;
            const targetType = document.getElementById('selectTargetType').value;
            let targetId = null;

            if (targetType === 'program') {
                targetId = document.getElementById('selectProgram').value;
            } else if (targetType === 'mentor') {
                targetId = document.getElementById('selectMentor').value;
            }

            fetch("{{ route('admin.broadcast.preview') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    template: template,
                    target_type: targetType,
                    target_id: targetId
                })
            })
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    document.getElementById('phonePreviewText').innerText = response.parsed_message;
                    document.getElementById('recipientCountBadge').innerText = response.total_recipients;
                }
            })
            .catch(err => {
                console.error('Preview error:', err);
            });
        }, 300);
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateLivePreview();
    });
</script>
@endpush
