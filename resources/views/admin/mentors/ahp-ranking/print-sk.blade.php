<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keputusan Guru Teladan AHP - {{ $month }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            background: #f8fafc;
            padding: 20px 0;
        }
        .page-sheet {
            background: #fff;
            max-width: 210mm;
            margin: 0 auto;
            padding: 20mm 20mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-radius: 4px;
        }
        .kop-title {
            font-size: 1.35rem;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #0d5c2e;
        }
        .kop-sub {
            font-size: 0.85rem;
            color: #444;
            line-height: 1.3;
        }
        .double-line {
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            height: 4px;
            margin: 12px 0 18px 0;
        }
        .sk-title {
            font-size: 1.15rem;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
        }
        .sk-number {
            font-size: 0.95rem;
            font-weight: bold;
            margin-bottom: 16px;
        }
        .content-section {
            font-size: 0.95rem;
            line-height: 1.5;
            text-align: justify;
        }
        .table-sk {
            font-size: 0.85rem;
            border-color: #000;
        }
        .table-sk th {
            background-color: #f1f5f9 !important;
            text-align: center;
            vertical-align: middle;
            border-color: #000;
        }
        .table-sk td {
            vertical-align: middle;
            border-color: #000;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .page-sheet {
                box-shadow: none;
                margin: 0;
                padding: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Tombol Cetak (Hanya tampil di layar) -->
    <div class="container text-center mb-4 no-print">
        <button onclick="window.print()" class="btn btn-success rounded-pill px-4 shadow">
            <i class="bi bi-printer-fill me-1"></i> Cetak / Simpan PDF (A4)
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary rounded-pill px-3 ms-2">
            Tutup Tab
        </button>
    </div>

    <div class="page-sheet">
        <!-- KOP SURAT RESMI YAYASAN -->
        <div class="text-center">
            <div class="text-muted small font-monospace mb-1">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
            <div class="kop-title">YAYASAN PENDIDIKAN DAN BIMBINGAN AL-QUR'AN AL-HIKMAH</div>
            <div class="kop-sub">
                Lembaga Sertifikasi Tahsin, Tahfidz, Tajwid & Kaderisasi Pengajar Al-Qur'an Terpadu<br>
                Sekretariat: Kompleks Pesantren Al-Hikmah, Jl. Terusan Al-Hikmah No. 45 | Telp: 0812-3456-7890<br>
                Situs Resmi: <em>lms.alhikmah.sch.id</em> | Email: <em>akademik@alhikmah.sch.id</em>
            </div>
            <div class="double-line"></div>
        </div>

        <!-- JUDUL SURAT KEPUTUSAN -->
        <div class="text-center mb-4">
            <div class="sk-title">SURAT KEPUTUSAN PIMPINAN LEMBAGA</div>
            <div class="sk-number">NOMOR: {{ $skNumber }}</div>
            <div class="fw-bold" style="font-size: 0.95rem;">
                TENTANG:<br>
                PENETAPAN PENGHARGAAN USTADZ/USTAZAH TELADAN DAN ALOKASI BONUS REWARD<br>
                PERIODE {{ strtoupper($targetDate->translatedFormat('F Y')) }}
            </div>
        </div>

        <!-- ISI KONSIDERANS -->
        <div class="content-section mb-3">
            <table class="w-100" style="vertical-align: top;">
                <tr>
                    <td style="width: 110px; vertical-align: top;" class="fw-bold">Menimbang</td>
                    <td style="width: 20px; vertical-align: top;">:</td>
                    <td>
                        <ol type="a" class="ps-3 mb-2">
                            <li>Bahwa dalam rangka memotivasi etos pengajaran dan apresiasi terhadap dedikasi pengajar Al-Qur'an, diperlukan penilaian mutu bimbingan yang transparan, akuntabel, dan objektif.</li>
                            <li>Bahwa berdasarkan hasil perhitungan matematis Sistem Pendukung Keputusan (SPK) metode <em>Analytical Hierarchy Process (AHP)</em> periode {{ $targetDate->translatedFormat('F Y') }}, telah diperoleh perangkingan komposit guru teladan.</li>
                            <li>Bahwa nama-nama yang tercantum dalam surat keputusan ini dipandang memenuhi kriteria dan dedikasi terbaik untuk diberikan piagam penghargaan dan bonus reward.</li>
                        </ol>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold" style="vertical-align: top;">Mengingat</td>
                    <td style="vertical-align: top;">:</td>
                    <td>
                        <ol class="ps-3 mb-2">
                            <li>Anggaran Dasar dan Anggaran Rumah Tangga Yayasan Pendidikan Al-Hikmah.</li>
                            <li>Standar Mutu Bimbingan Al-Qur'an dan Sistem Mutaba'ah Terpadu AL-HIKMAH LMS.</li>
                            <li>Hasil Evaluasi Multi-Kriteria 5 Dimensi Saaty AHP (Consistency Ratio &le; 10%).</li>
                        </ol>
                    </td>
                </tr>
            </table>
        </div>

        <div class="text-center fw-bold my-3" style="letter-spacing: 2px;">MEMUTUSKAN</div>

        <!-- DIKTUM KEPUTUSAN -->
        <div class="content-section mb-4">
            <table class="w-100">
                <tr>
                    <td style="width: 110px; vertical-align: top;" class="fw-bold">Menetapkan</td>
                    <td style="width: 20px; vertical-align: top;">:</td>
                    <td><strong>KEPUTUSAN PIMPINAN LEMBAGA TENTANG PENETAPAN USTADZ/USTAZAH TELADAN DAN ALOKASI BONUS REWARD PERIODE {{ strtoupper($targetDate->translatedFormat('F Y')) }}.</strong></td>
                </tr>
                <tr>
                    <td class="fw-bold" style="vertical-align: top;">PERTAMA</td>
                    <td style="vertical-align: top;">:</td>
                    <td>Menetapkan hasil evaluasi Sistem Pendukung Keputusan AHP dengan 5 Kriteria (Kedisiplinan, Kualitas Pedagogi & Mutaba'ah, Akhlak & Komunikasi, Kepuasan Wali Santri, dan Pengembangan Diri).</td>
                </tr>
                <tr>
                    <td class="fw-bold" style="vertical-align: top;">KEDUA</td>
                    <td style="vertical-align: top;">:</td>
                    <td>Menetapkan Ustadz/Ustazah penerima penghargaan teladan dan alokasi bonus reward sebagaimana tertera pada tabel lampiran di bawah ini:</td>
                </tr>
            </table>
        </div>

        <!-- TABEL LAMPIRAN DAFTAR JUARA & REWARD -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm table-sk w-100">
                <thead>
                    <tr>
                        <th style="width: 40px;">Peringkat</th>
                        <th>Nama Ustadz / Ustazah</th>
                        <th style="width: 60px;">Skor AHP</th>
                        <th>Predikat Kehormatan</th>
                        <th style="width: 110px;">Bonus Reward</th>
                        <th>Catatan Khusus Pimpinan Lembaga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($evaluation['leaderboard']->take(5) as $row)
                    <tr>
                        <td class="text-center fw-bold">#{{ $row['rank_position'] }}</td>
                        <td>
                            <div class="fw-bold">{{ $row['mentor_name'] }}</div>
                            <small class="text-muted">{{ $row['mentor']?->specialization ?? 'Guru Pembimbing' }}</small>
                        </td>
                        <td class="text-center fw-bold font-monospace">{{ $row['final_ahp_score'] }}</td>
                        <td>{{ $row['tier_badge'] ?? 'Peserta Evaluasi' }}</td>
                        <td class="text-end fw-bold font-monospace">
                            {{ $row['reward_amount'] > 0 ? 'Rp ' . number_format($row['reward_amount'], 0, ',', '.') : '-' }}
                        </td>
                        <td class="small">{{ $row['admin_notes'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="content-section mb-4">
            <table class="w-100">
                <tr>
                    <td style="width: 110px; vertical-align: top;" class="fw-bold">KETIGA</td>
                    <td style="width: 20px; vertical-align: top;">:</td>
                    <td>Pemberian bonus reward ini dibebankan pada Anggaran Dana Insentif & Apresiasi Mutu SDM Yayasan Al-Hikmah dan disalurkan langsung ke rekening resmi masing-masing ustadz/ustazah.</td>
                </tr>
                <tr>
                    <td class="fw-bold" style="vertical-align: top;">KEEMPAT</td>
                    <td style="vertical-align: top;">:</td>
                    <td>Keputusan ini berlaku terhitung sejak tanggal ditetapkan, dengan ketentuan apabila di kemudian hari terdapat kekeliruan akan diadakan perbaikan sebagaimana mestinya.</td>
                </tr>
            </table>
        </div>

        <!-- TANDA TANGAN PIMPINAN -->
        <div class="row mt-5">
            <div class="col-6"></div>
            <div class="col-6 text-center">
                <div>Ditetapkan di : Tangerang Selatan</div>
                <div>Pada tanggal : {{ now()->translatedFormat('d F Y') }}</div>
                <div class="fw-bold mt-2 mb-5">
                    Pimpinan Lembaga Al-Hikmah LMS,<br>
                    Yayasan Bimbingan Al-Qur'an Al-Hikmah
                </div>
                <div class="fw-bold text-decoration-underline" style="font-size: 1.05rem;">
                    K.H. Dr. Abdullah Gymnastiar, M.Ag.
                </div>
                <div class="text-muted small">NIP. 19780512 200501 1 002</div>
            </div>
        </div>
    </div>

</body>
</html>
