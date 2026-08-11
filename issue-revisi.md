# 📋 Perencanaan Implementation & Revisi Feature (Guide untuk Junior Developer)

Dokumen ini berisi panduan dan instruksi langkah-demi-langkah pengerjaan revisi antarmuka serta implementasi fitur dinamis pada project **AL-HIKMAH LMS**. Dokumen ini dibuat secara detail dan terstruktur agar dapat langsung dipahami dan dieksekusi oleh **Junior Developer / Programmer**.

---

## 📌 Ringkasan Revisi dari Atasan

1. **Penyesuaian Halaman `program.blade.php` & `biaya.blade.php` dengan Master HTML Template**
   - Halaman **Program** (`resources/views/program.blade.php`) dan **Biaya** (`resources/views/biaya.blade.php`) yang ada di project memiliki struktur tampilan yang berbeda signifikan dengan master HTML di `template/program.html` dan `template/biaya.html`.
   - Diperlukan penyesuaian (refactoring) layout Blade View agar presisi 100% mengikuti desain master HTML template tanpa merusak data dinamis dari database.

2. **Dinamisasi Link WhatsApp & Kontak (Termasuk `resources/views/tahfidz.blade.php`)**
   - Link WhatsApp di `resources/views/tahfidz.blade.php` (baris 46-48 & baris 64) serta beberapa view lainnya masih di-hardcode (`https://wa.me/6285786689008`).
   - Perlu dibuatkan fitur dinamisasi nomor WhatsApp terpusat (konfigurasi `.env` / config / database) agar nomor kontak dapat diubah dari satu tempat.

---

## 🛠️ Task 1: Refactoring Tampilan Halaman Program & Biaya

---

### A. Refactoring Halaman Program (`resources/views/program.blade.php`)

#### 🧐 Analisis Permasalahan:
* Dalam file master `template/program.html`, program belajar dibagi secara rapi dalam **3 Section Utama**:
  1. **Program Anak (10–15 tahun) — Utama**: *Iqra & Dasar Al-Qur'an, Tahsin Dasar, Adab & Doa Harian, Tahfidz Al-Qur'an*.
  2. **Program Tambahan (Dewasa & Muslimah)**: *Belajar dari Nol (Dewasa), Tahsin Dewasa, Kelas Muslimah, Tahfidz Dewasa*.
  3. **Program Bahasa Arab**: *Bahasa Arab Dasar, Nahwu & Sharaf* (menggunakan aksen kartu khusus `.arabic-featured`).
* Dalam file `resources/views/program.blade.php`, saat ada data `$programs` dari DB, tampilan berubah menjadi 1 section grid generik yang menghapus pembagian 3 section ini serta style `.arabic-featured`.

#### 📝 Tahapan Pengerjaan (Langkah demi Langkah):
1. **Buka File Reference & Target**:
   - Master Template: `template/program.html`
   - Target View: `resources/views/program.blade.php`

2. **Strukturkan Ulang Grid Layout pada Blade**:
   - Kembalikan layout Blade agar selalu menampilkan **3 Section** sesuai template:
     - Section `Program Anak`
     - Section `Program Tambahan`
     - Section `Program Bahasa Arab`

3. **Opsi Penanganan Data Dinamis**:
   * **Pendekatan A (Filtering By Category di Controller/Blade)**:
     - Tambahkan kolom `category` (misal: `anak`, `dewasa`, `bahasa_arab`) pada database/migration `programs`.
     - Filter data program pada Blade View:
       ```blade
       {{-- Section Program Anak --}}
       @foreach($programs->where('category', 'anak') as $program)
           ...
       @endforeach

       {{-- Section Bahasa Arab --}}
       @foreach($programs->where('category', 'bahasa_arab') as $program)
           <div class="program-card arabic-featured">
               ...
           </div>
       @endforeach
       ```
   * **Pendekatan B (Preserve Template Layout dengan Fallback Dynamic Action)**:
     - Apabila tabel `programs` belum memiliki kategori, pertahankan struktur statis dari `template/program.html` untuk elemen visual, dan cukup buatkan link WhatsApp/Modal pendaftaran dinamis di masing-masing kartu.

4. **Verifikasi Elemen Visual**:
   - Pastikan class `program-section-title`, `program-card`, dan `arabic-featured` terpasang persis sesuai `template/program.html`.

---

### B. Refactoring Halaman Biaya (`resources/views/biaya.blade.php`)

#### 🧐 Analisis Permasalahan:
* Dalam file master `template/biaya.html`, terdapat 2 komponen penting:
  1. **Section Biaya Pendaftaran**: Banner Rp 150.000 ("Sekali Bayar" - untuk administrasi & assessment awal).
  2. **Section Paket Belajar Bulanan**:
     - **Basic**: Rp 400.000 / bulan (4x / bulan, 90 menit, Private 1:1).
     - **Standard**: Rp 800.000 / bulan (8x / bulan, 90 menit, Private 1:1) — Memiliki ribbon **`⭐ Banyak Dipilih`** (`.paket-popular` & `.paket-popular-ribbon`).
     - **Premium**: Rp 1.200.000 / bulan (12x / bulan, 90 menit, Private 1:1).
* Di `resources/views/biaya.blade.php` saat ini, perulangan `@foreach($programs)` dari DB mengganti format paket bulanan di atas sehingga komponen ribbon popularitas dan detail pertemuan 4x/8x/12x menjadi berantakan/hilang.

#### 📝 Tahapan Pengerjaan (Langkah demi Langkah):
1. **Buka File Reference & Target**:
   - Master Template: `template/biaya.html`
   - Target View: `resources/views/biaya.blade.php`

2. **Pertahankan Section Biaya Pendaftaran**:
   - Pastikan elemen kartu `biaya-card` (Rp 150.000 - Sekali Bayar) tetap dirender di bagian atas halaman.

3. **Restrukturisasi Kartu Paket Belajar**:
   - Pastikan 3 kartu paket (Basic, Standard, Premium) dirender dengan struktur HTML persis seperti `template/biaya.html`.
   - Pada kartu paket **Standard** (indeks ke-2 atau paket paling populer), wajib sertakan markup ribbon berikut:
     ```html
     <div class="paket-card paket-popular">
         <div class="paket-popular-ribbon"><span>⭐ Banyak Dipilih</span></div>
         ...
     </div>
     ```

4. **Integrasi Data Paket/Program Dinamis**:
   - Jika menggunakan data dari database, mapping atribut DB dengan slot paket berikut:
     - `name`: Nama Paket (Basic / Standard / Premium)
     - `price`: Rp 400.000 / Rp 800.000 / Rp 1.200.000
     - `meetings`: 4x / 8x / 12x per bulan
     - `is_popular`: boolean (untuk mentrigger class `.paket-popular` dan ribbon `⭐ Banyak Dipilih`).

---

## 📱 Task 2: Dinamisasi Fitur Link WhatsApp & Kontak Website

---

### 🧐 Analisis Permasalahan:
Di file `resources/views/tahfidz.blade.php` pada baris 46–48:
```blade
<a href="https://wa.me/6285786689008?text={{ urlencode('Assalamualaikum, saya ingin mendaftar Program Tahfidz Al-Qur\'an AL-HIKMAH') }}"
   class="btn btn-primary-custom" target="_blank">
   Daftar Program Tahfidz <i class="bi bi-arrow-right ms-2"></i>
</a>
```
Nomor WhatsApp `6285786689008` di-hardcode langsung di view. Hal ini juga terjadi pada file `home.blade.php`, `metode.blade.php`, `biaya.blade.php`, `program.blade.php`, `landing.blade.php` (floating WA), dan `footer.blade.php`. Jika kelak nomor WA pengelola berubah, kita harus mengubah puluhan file Blade secara manual.

---

### 📝 Tahapan Pengerjaan (Langkah demi Langkah untuk Junior Dev):

#### Langkah 1: Tambahkan Variable Konfigurasi WhatsApp

1. Buka file `.env` dan tambahkan variabel berikut:
   ```env
   WHATSAPP_NUMBER=6285786689008
   ```

2. Buka atau buat file konfigurasi `config/settings.php` (atau tambahkan di `config/app.php`):
   ```php
   <?php

   return [
       'whatsapp_number' => env('WHATSAPP_NUMBER', '6285786689008'),
       'instagram_handle' => env('INSTAGRAM_HANDLE', 'houseofalhikmah'),
       'email_contact' => env('EMAIL_CONTACT', 'belajarquranalhikmah@gmail.com'),
   ];
   ```

---

#### Langkah 2: Buat Global Helper Function `wa_url()`

1. Buat file helper baru di `app/Helpers/settings.php`:
   ```php
   <?php

   if (!function_exists('wa_url')) {
       /**
        * Generate WhatsApp link secara dinamis.
        *
        * @param string|null $message Pesan default yang akan dikirim via WA
        * @return string URL WhatsApp lengkap
        */
       function wa_url(?string $message = null): string
       {
           $phone = config('settings.whatsapp_number', '6285786689008');
           
           // Bersihkan karakter non-digit jika ada input angka dengan format +62/dash
           $phone = preg_replace('/[^0-9]/', '', $phone);
           
           $url = "https://wa.me/{$phone}";

           if (!empty($message)) {
               $url .= '?text=' . rawurlencode($message);
           }

           return $url;
       }
   }
   ```

2. Daftarkan file helper tersebut di `composer.json` pada bagian `autoload`:
   ```json
   "autoload": {
       "psr-4": {
           "App\\": "app/",
           "Database\\Factories\\": "database/factories/",
           "Database\\Seeders\\": "database/seeders/"
       },
       "files": [
           "app/Helpers/settings.php"
       ]
   },
   ```

3. Jalankan perintah terminal berikut agar helper terdeteksi oleh Laravel:
   ```bash
   composer dump-autoload
   ```

---

#### Langkah 3: Update View `resources/views/tahfidz.blade.php`

1. Buka file `resources/views/tahfidz.blade.php`.
2. Ubah kode di baris 46–49 menjadi dinamis menggunakan helper `wa_url()`:
   ```blade
   <a href="{{ wa_url('Assalamualaikum, saya ingin mendaftar Program Tahfidz Al-Qur\'an AL-HIKMAH') }}"
      class="btn btn-primary-custom" target="_blank">
      Daftar Program Tahfidz <i class="bi bi-arrow-right ms-2"></i>
   </a>
   ```

3. Ubah juga tombol Konsultasi pada bagian CTA (baris 64):
   ```blade
   <a href="{{ wa_url('Assalamualaikum, saya ingin berkonsultasi mengenai Program Tahfidz Al-Qur\'an AL-HIKMAH') }}"
      class="btn btn-outline-light-custom btn-lg" target="_blank">
      <i class="bi bi-whatsapp me-2"></i>Konsultasi Program Tahfidz
   </a>
   ```

---

#### Langkah 4: Refactor Seluruh Link WA di Component Lain (Global Consistency)

Ganti semua link WA di file berikut menggunakan helper `wa_url()`:
* `resources/views/layouts/landing.blade.php` (Floating WhatsApp Widget):
  ```blade
  <a href="{{ wa_url('Assalamualaikum, saya ingin bertanya tentang program belajar AL-HIKMAH') }}"
     class="floating-whatsapp" target="_blank" rel="noopener" aria-label="Hubungi via WhatsApp">
     <i class="bi bi-whatsapp"></i>
     <span class="wa-tooltip">Berbincang dengan Kami</span>
  </a>
  ```
* `resources/views/partials/footer.blade.php` (Link WA di Footer & Socials)
* `resources/views/metode.blade.php`
* `resources/views/program.blade.php`
* `resources/views/biaya.blade.php`

---

## 🧪 Pengujian & Verifikasi (Quality Assurance Checklist)

Sebelum melaporkan pekerjaan selesai, Junior Developer wajib melakukan verifikasi berikut:

- [ ] **Cache Clearing**:
  Jalankan perintah berikut di terminal:
  ```bash
  php artisan config:clear
  php artisan view:clear
  ```

- [ ] **Uji Tampilan Halaman Program (`/program`)**:
  - Buka browser ke halaman `/program`.
  - Pastikan 3 section (Program Anak, Program Tambahan, Program Bahasa Arab) tampil rapi sesuai `template/program.html`.
  - Check kartu Bahasa Arab memiliki aksen khusus `.arabic-featured`.

- [ ] **Uji Tampilan Halaman Biaya (`/biaya`)**:
  - Buka browser ke halaman `/biaya`.
  - Pastikan kartu **Biaya Pendaftaran** (Rp 150.000) dan 3 kartu paket (Basic, Standard, Premium) tampil presisi sesuai `template/biaya.html`.
  - Pastikan ribbon **`⭐ Banyak Dipilih`** di paket Standard terpasang dengan benar.

- [ ] **Uji Dinamisasi WhatsApp (`/tahfidz` & Halaman Lain)**:
  - Coba ubah nilai `WHATSAPP_NUMBER` di file `.env` (misal menjadi `6281234567890`).
  - Refresh browser dan hover / klik tombol *Daftar Program Tahfidz*, *Konsultasi*, serta *Floating WA*.
  - Pastikan URL tujuan WhatsApp otomatis mengarahkan ke nomor baru yang diatur di `.env`.

---

## 📌 Catatan Tambahan untuk Developer
* **CSS Assets**: Seluruh style CSS sudah tersedia di `public/assets/css/style.css`, jangan membuat class CSS baru jika class di template HTML sudah ada.
* **Code Standard**: Ikuti aturan pengodean Laravel & Pint (`vendor/bin/pint`).
