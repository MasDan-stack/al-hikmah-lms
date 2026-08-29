<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Soal & Evaluasi - {{ $program->name ?? 'AL-HIKMAH' }} - {{ $topic }}</title>
    <!-- Google Fonts Inter & Amiri (Arabic) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #15803d;
            --text-dark: #0f172a;
            --border-color: #cbd5e1;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: var(--text-dark);
            background: #f8fafc;
            margin: 0;
            padding: 0;
            font-size: 13pt;
            line-height: 1.5;
        }

        .arabic-text {
            font-family: 'Amiri', serif;
            font-size: 1.4rem;
            direction: rtl;
            line-height: 2;
        }

        /* Screen Control Bar */
        .print-toolbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn-print {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 22px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }

        .btn-print:hover {
            background: #166534;
        }

        .btn-back {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
            padding: 9px 18px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-back:hover {
            background: #e2e8f0;
        }

        /* Paper Page Container */
        .page-container {
            max-width: 210mm;
            margin: 24px auto;
            background: #ffffff;
            padding: 20mm;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
        }

        /* Kop Surat Lembaga */
        .kop-header {
            border-bottom: 3px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 18px;
            text-align: center;
        }

        .kop-header h2 {
            margin: 0;
            font-size: 16pt;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .kop-header h3 {
            margin: 4px 0;
            font-size: 12pt;
            font-weight: 600;
            color: #334155;
        }

        .kop-header p {
            margin: 0;
            font-size: 9.5pt;
            color: #64748b;
        }

        /* Student Identity Table */
        .identity-box {
            border: 1px solid #94a3b8;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 20px;
            font-size: 10.5pt;
        }

        .identity-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 120px;
            gap: 8px 16px;
        }

        .identity-field {
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .identity-field strong {
            min-width: 90px;
            color: #1e293b;
        }

        .identity-field .dots {
            border-bottom: 1px dotted #64748b;
            flex-grow: 1;
            height: 14px;
        }

        .score-box {
            border: 1px solid #94a3b8;
            border-radius: 6px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4px;
            background: #f8fafc;
        }

        .score-box .score-title {
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #475569;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 2px;
        }

        .score-box .score-value {
            font-size: 16pt;
            font-weight: 800;
            height: 30px;
        }

        /* Exam Instructions */
        .instruction-box {
            background: #f1f5f9;
            border-left: 4px solid var(--primary);
            padding: 8px 14px;
            margin-bottom: 22px;
            font-size: 9.5pt;
            border-radius: 0 6px 6px 0;
        }

        .instruction-box ol {
            margin: 4px 0 0 0;
            padding-left: 18px;
        }

        /* Section Titles */
        .section-heading {
            background: #e2e8f0;
            padding: 6px 12px;
            font-size: 11pt;
            font-weight: 700;
            margin: 20px 0 14px 0;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Question Item */
        .question-item {
            margin-bottom: 18px;
            page-break-inside: avoid;
        }

        .question-header {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 8px;
        }

        .q-number {
            font-weight: 700;
            min-width: 24px;
        }

        .q-text {
            flex-grow: 1;
            font-weight: 500;
        }

        .options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 16px;
            padding-left: 28px;
        }

        .option-choice {
            display: flex;
            align-items: baseline;
            gap: 8px;
            font-size: 10pt;
        }

        .opt-circle {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 1.2px solid #475569;
            border-radius: 50%;
            text-align: center;
            font-size: 8pt;
            line-height: 14px;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* Essay Writing Space */
        .essay-space {
            margin-left: 28px;
            margin-top: 8px;
            border: 1px dashed #cbd5e1;
            border-radius: 4px;
            background: #fafafa;
            padding: 8px;
            min-height: 80px;
        }

        .essay-lines {
            background-image: repeating-linear-gradient(transparent, transparent 23px, #e2e8f0 24px);
            height: 72px;
            width: 100%;
        }

        /* Answer Key Sheet */
        .key-section {
            page-break-before: always;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px dashed #94a3b8;
        }

        .key-badge {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9pt;
        }

        .explanation-text {
            font-size: 9.5pt;
            color: #334155;
            background: #f8fafc;
            border-left: 3px solid #64748b;
            padding: 6px 10px;
            margin-top: 6px;
            margin-left: 28px;
            border-radius: 0 4px 4px 0;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff;
                font-size: 11pt;
            }

            .print-toolbar {
                display: none !important;
            }

            .page-container {
                max-width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A4 portrait;
                margin: 15mm 15mm 15mm 15mm;
            }
        }
    </style>
</head>

<body>

    <!-- Top Action Toolbar (Hidden during print) -->
    <div class="print-toolbar no-print">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('mentor.questions.index') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Bank Soal
            </a>
            <span style="font-weight: 600; color: #1e293b;">
                Lembar Soal: {{ $program->name ?? 'Semua Program' }} ({{ count($questions) }} Butir Soal)
            </span>
        </div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <label
                style="font-size: 13px; color: #475569; display: flex; align-items: center; gap: 6px; cursor: pointer;">
                <input type="checkbox" id="toggleAnswerKey" checked
                    onchange="document.getElementById('teacherKeySheet').style.display = this.checked ? 'block' : 'none';">
                Sertakan Lembar Kunci & Pembahasan Guru
            </label>
            <button type="button" class="btn-print" onclick="window.print();">
                <i class="bi bi-printer-fill"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Paper Worksheet Content -->
    <div class="page-container">
        <!-- 1. KOP SURAT AL-HIKMAH -->
        <div class="kop-header">
            <h2>Lembaga Pendidikan Al-Qur'an AL-HIKMAH</h2>
            <h3>LEMBAR EVALUASI & UJIAN KOMPETENSI SANTRI</h3>
            <p>Sistem Pembelajaran Al-Qur'an & Bimbingan Tahsin, Tahfidz, Bahasa Arab Terpadu</p>
        </div>

        <!-- 2. IDENTITAS UJIAN & SANTRI -->
        <div class="identity-box">
            <div class="identity-grid">
                <div>
                    <div class="identity-field">
                        <strong>Program:</strong>
                        <span>{{ $program->name ?? 'Semua Program' }}</span>
                    </div>
                    <div class="identity-field" style="margin-top: 4px;">
                        <strong>Materi / Topik:</strong>
                        <span>{{ $topic }}</span>
                    </div>
                </div>
                <div>
                    <div class="identity-field">
                        <strong>Nama Santri:</strong>
                        <div class="dots"></div>
                    </div>
                    <div class="identity-field" style="margin-top: 4px;">
                        <strong>Kelas / Kelompok:</strong>
                        <div class="dots"></div>
                    </div>
                    <div class="identity-field" style="margin-top: 4px;">
                        <strong>Hari / Tanggal:</strong>
                        <div class="dots"></div>
                    </div>
                </div>
                <div class="score-box">
                    <span class="score-title">Nilai / Skor</span>
                    <span class="score-value"></span>
                </div>
            </div>
        </div>

        <!-- 3. PETUNJUK PENGERJAAN -->
        <div class="instruction-box">
            <strong>Petunjuk Pengerjaan:</strong>
            <ol>
                <li>Awali dengan membaca <em>Basmalah</em> dan doa sebelum belajar.</li>
                <li>Tuliskan identitas nama lengkap dan kelas pada kolom yang telah disediakan.</li>
                <li>Untuk soal <strong>Pilihan Ganda</strong>, berilah tanda silang (X) atau bulatkan huruf (A, B, C,
                    atau D) pada jawaban yang paling tepat.</li>
                <li>Untuk soal <strong>Essay / Uraian</strong>, tuliskan jawaban secara jelas, runtut, dan sertakan
                    dalil/kaidah jika diminta.</li>
                <li>Periksa kembali seluruh lembar jawaban sebelum diserahkan kepada Ustadz / Ustadzah.</li>
            </ol>
        </div>

        @php
            $multipleChoiceQuestions = $questions
                ->filter(fn($q) => ($q->type ?? 'multiple_choice') === 'multiple_choice' || empty($q->type))
                ->values();
            $essayQuestions = $questions->filter(fn($q) => ($q->type ?? '') === 'essay')->values();
            $questionCounter = 1;
        @endphp

        <!-- 4. BAGIAN A: SOAL PILIHAN GANDA -->
        @if ($multipleChoiceQuestions->isNotEmpty())
            <div class="section-heading">
                Bagian I: Pilihan Ganda (Berilah tanda silang pada pilihan yang benar)
            </div>

            @foreach ($multipleChoiceQuestions as $q)
                <div class="question-item">
                    <div class="question-header">
                        <span class="q-number">{{ $questionCounter }}.</span>
                        <div class="q-text">{!! nl2br(e($q->question)) !!}</div>
                    </div>
                    <div class="options-grid">
                        @foreach ($q->options ?? ['-', '-', '-', '-'] as $optIdx => $optText)
                            <div class="option-choice">
                                <span class="opt-circle">{{ chr(65 + $optIdx) }}</span>
                                <span>{{ $optText }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @php $questionCounter++; @endphp
            @endforeach
        @endif

        <!-- 5. BAGIAN B: SOAL ESSAY / URAIAN -->
        @if ($essayQuestions->isNotEmpty())
            <div class="section-heading" style="margin-top: 24px;">
                Bagian II: Soal Uraian / Essay (Jawablah pertanyaan berikut dengan lengkap dan tepat)
            </div>

            @foreach ($essayQuestions as $q)
                <div class="question-item">
                    <div class="question-header">
                        <span class="q-number">{{ $questionCounter }}.</span>
                        <div class="q-text">{!! nl2br(e($q->question)) !!}</div>
                    </div>
                    <div class="essay-space">
                        <div class="essay-lines"></div>
                    </div>
                </div>
                @php $questionCounter++; @endphp
            @endforeach
        @endif

        <!-- 6. LEMBAR KUNCI JAWABAN & RUBRIK PEMBAHASAN GURU -->
        <div id="teacherKeySheet" class="key-section">
            <div class="kop-header" style="border-bottom: 2px solid #0f172a; margin-bottom: 14px;">
                <h3 style="margin: 0; text-transform: uppercase; color: #166534; font-size: 13pt;">
                    <i class="bi bi-shield-lock-fill"></i> PEGANGAN GURU / MENTOR: KUNCI JAWABAN & PEMBAHASAN
                </h3>
                <p style="font-size: 9pt;">Materi: {{ $topic }} | Program: {{ $program->name ?? '-' }} |
                    Tingkat: {{ $difficulty }}</p>
            </div>

            @php $keyCounter = 1; @endphp
            @foreach ($questions as $q)
                <div class="question-item" style="margin-bottom: 14px;">
                    <div class="question-header">
                        <span class="q-number">{{ $keyCounter }}.</span>
                        <div class="q-text">
                            <strong>{{ $q->question }}</strong>
                            <div style="margin-top: 4px;">
                                @if (($q->type ?? '') === 'essay')
                                    <span class="key-badge"
                                        style="background:#eff6ff; color:#1d4ed8; border-color:#93c5fd;">Kunci Jawaban
                                        Uraian:</span>
                                    <div style="margin-top: 4px; font-size: 10pt; color: #1e293b; padding-left: 4px;">
                                        {!! nl2br(e($q->essay_answer ?: 'Jawaban ideal terlampir.')) !!}
                                    </div>
                                    @if ($q->rubric)
                                        <div
                                            style="margin-top: 4px; font-size: 9pt; color: #475569; font-style: italic;">
                                            <strong>Rubrik Penilaian:</strong> {{ $q->rubric }}
                                        </div>
                                    @endif
                                @else
                                    <span class="key-badge">Kunci: {{ $q->correct_option_label }}
                                        ({{ $q->correct_option_text }})</span>
                                @endif
                            </div>
                            @if ($q->explanation)
                                <div class="explanation-text">
                                    <strong>Penjelasan / Kaidah Dalil:</strong> {{ $q->explanation }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @php $keyCounter++; @endphp
            @endforeach

            <div
                style="margin-top: 30px; display: grid; grid-template-columns: 1fr 1fr; text-align: center; font-size: 10pt;">
                <div>
                    Mengetahui,<br>
                    <strong>Kepala Pembimbing AL-HIKMAH</strong>
                    <div style="height: 50px;"></div>
                    ( .................................................. )
                </div>
                <div>
                    Guru / Pendamping,<br>
                    <strong>{{ auth()->user()->name ?? 'Ustadz Pembimbing' }}</strong>
                    <div style="height: 50px;"></div>
                    ( .................................................. )
                </div>
            </div>
        </div>
    </div>

</body>

</html>
