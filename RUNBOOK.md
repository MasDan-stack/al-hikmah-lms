# RUNBOOK: Prosedur Operasional Standar (SOP) & Disaster Response Plan
**Platform:** AL-HIKMAH LMS  
**Target Pembaca:** Solo Operator, Tim DevOps, Administrator & Pimpinan Lembaga  
**Versi:** 1.0 (Basis v11.8)

Dokumen ini adalah pedoman aksi nyata ketika sistem menghadapi anomali operasional, insiden infrastruktur, atau kenyataan pahit dari data lapangan pada **Reality Check Dashboard** (`/admin/reality-check`).

---

## 1. Reality Check Disaster Response Plan

*Dashboard menyajikan fakta jujur tanpa kosmetik. Jika angka lapangan menunjukkan kemunduran, jangan denial atau menyalahkan sistem. Lakukan protokol aksi terukur berikut:*

### Skenario A: Santri Aktif Riil < 50% dari Santri Terdaftar
**Kondisi Pemicu:** Total santri dengan status akun terdaftar banyak, namun santri dengan $\ge 1$ sesi bimbingan berstatus `completed` dalam 30 hari terakhir kurang dari 50%.
* **Hipotesis Masalah:**
  1. *Onboarding Friction:* Wali santri bingung memilih jadwal atau menghubungkan nomor WhatsApp guru.
  2. *Silent Dropout:* Santri bosan, merasa materi terlalu berat, atau orang tua lupa memperpanjang sesi.
  3. *Unmatched Mentors:* Guru berhalangan hadir atau ada permohonan bimbingan yang macet di status `waiting_admin`.
* **Protokol Tindakan (Timeline: 1 Minggu):**
  1. **Audit Permohonan Macet (H+1):** Buka `/admin/enrollments`, filter status `waiting_admin` dan `waiting_parent`. Selesaikan penugasan guru secara manual dalam $< 24$ jam.
  2. **Audit Guru Berhalangan (H+2):** Buka `/admin/mentors/leaves` dan riwayat sesi gagal di `/admin/active-enrollments`. Identifikasi guru dengan tingkat pembatalan tertinggi.
  3. **Personal Outreach 10 Wali Santri (H+3 s.d H+5):** Kirim pesan WhatsApp personal (bukan bot otomatis) ke 10 orang tua santri yang paling lama tidak memiliki sesi aktif. Tanyakan kendala dengan nada empati dakwah, bukan menagih.
  4. **Tawaran Sesi Pemulihan (H+7):** Berikan voucher gratis 1 sesi temu ramah guru pengganti bagi santri yang sempat pasif $> 14$ hari.

---

### Skenario B: Fitur Berstatus 🔴 Menganggur > 50%
**Kondisi Pemicu:** Lebih dari 5 dari 10 fitur utama di Reality Check Dashboard menunjukkan status `Menganggur / Nol Akses` dalam rentang 30 hari.
* **Hipotesis Masalah:**
  1. *Discoverability Rendah:* Pengguna tidak tahu fitur tersebut ada karena tersembunyi di sub-menu atau navigasi buruk.
  2. *Feature Bloat / Premature Building:* Fitur dibangun berdasarkan asumsi pengembang, bukan kebutuhan riil santri/guru di lapangan.
  3. *Bug Halus (Silent Error):* Ada error JavaScript atau broken link yang membuat tombol tidak bisa diklik.
* **Protokol Tindakan (Timeline: 2 Minggu):**
  1. **Smoke Test Manual (H+1):** Coba klik fitur tersebut di HP Android (layar kecil) dan browser desktop untuk memastikan tidak ada error 404/500 atau masalah JavaScript.
  2. **Intervensi Micro-Tutorial (H+3 s.d H+7):**
     - Jika fitur terbukti bermanfaat tapi sepi akses (misal: Rapor Mutaba'ah atau Generator Soal): buat panduan visual ringkas (video pendek 90 detik atau 1 infografis gambar) dan kirim ke grup broadcast WhatsApp wali santri.
     - Tambahkan *highlight badge* / tooltip di navigasi pengguna.
  3. **Keputusan Pemangkasan / Deprecate (H+14):**
     - Jika setelah intervensi 2 pekan akses tetap nol: **Karantina fitur tersebut**.
     - Jangan biarkan kode mati menambah beban pemeliharaan (*maintenance debt*). Hapus route-nya, sembunyikan menunya, dan arsipkan filenya ke `scratch/deprecated/`.

---

### Skenario C: Gross Revenue Riil < Rp 5.000.000 / Bulan
**Kondisi Pemicu:** Total pembayaran santri berstatus `paid` pada tabel `payments` di bulan berjalan berada di bawah ambang batas dasar kelangsungan operasional server dan guru.
* **Hipotesis Masalah:**
  1. *Funnels Leaking:* Trafik landing page ada, tapi calon wali santri rontok sebelum mencapai form pendaftaran atau invoice.
  2. *Payment Friction:* Metode pembayaran ribet atau batas kedaluwarsa invoice terlalu cepat.
  3. *Marketing Melebar Tanpa Fokus:* Energi promosi terpecah ke terlalu banyak kanal tanpa konversi riil.
* **Protokol Tindakan (Timeline: 1 Bulan):**
  1. **Audit Invoice Menggantung (H+2):** Buka `/admin/payments`, filter status `pending` yang mendekati `expired_at`. Hubungi orang tua bersangkutan untuk menawarkan bantuan teknis cara pembayaran.
  2. **Evaluasi CAC & LTV (H+7):** Hitung berapa rupiah biaya promosi yang dikeluarkan dibanding omzet yang masuk.
  3. **Fokus pada 1 Kanal Berkinerja Terbaik (H+14 s.d H+30):**
     - Hentikan eksperimen promosi yang melebar.
     - Fokuskan 100% energi pada kanal paling efektif: misalnya silaturahmi komunitas masjid lokal, referral wali santri berprestasi, atau status WhatsApp guru.

---

## 2. Infrastructure & Health Monitoring Response Plan

### Endpoint `/health` Mengembalikan Status 503 / Degraded
1. **Probe Database Gagal / Latensi Kritis (> 500 ms):**
   - Jalankan `php artisan db:monitor` atau periksa koneksi via Tinker.
   - Cek apakah ada query berat tanpa indeks pada tabel transaksi via slow query log MySQL.
   - Jika koneksi database putus, restart service MySQL di server: `sudo systemctl restart mysql` atau via XAMPP Control Panel.
2. **Probe Storage Ruang Disk Kritis (< 100 MB):**
   - Periksa direktori `storage/logs/laravel.log`: jika ukurannya bergiga-giga, bersihkan dengan `truncate -s 0 storage/logs/laravel.log`.
   - Bersihkan direktori temporary backup lokal usang di `storage/app/backups/`.
3. **Probe Scheduler Mandek (> 25 Jam):**
   - Buka tabel `system_heartbeats` untuk melihat job mana yang terlambat (`stale_jobs`).
   - Pastikan cron daemon pada host Linux berjalan: `crontab -l` harus memuat `* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1`.
   - Di lingkungan lokal Windows, uji coba dengan menjalankan: `php artisan schedule:run`.

---

## 3. Disaster Recovery & Manual Restore Protocol

Jika terjadi bencana kehilangan data atau kerusakan server fisik total:
1. Unduh arsip database terkompresi terbaru dari penyimpanan cadangan (`storage/app/backups/` atau bucket S3).
2. Ekstrak arsip gzip:
   ```bash
   gzip -d -c alhikmah-backup-daily-YYYY-MM-DD-HHMMSS.sql.gz > restore.sql
   ```
3. Impor ke basis data baru:
   ```bash
   mysql -u root -p alhikmah_lms < restore.sql
   ```
4. Jalankan verifikasi integritas:
   ```bash
   php artisan backup:verify-restore
   ```
5. Bersihkan cache aplikasi:
   ```bash
   php artisan optimize:clear
   ```
