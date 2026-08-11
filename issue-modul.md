---
title: "[FEATURE & SEEDER] Modul Pengaturan Website (CMS Settings) di Dashboard Admin & Update Data Seeders Terkini"
labels: ["enhancement", "admin-dashboard", "database-seeder", "good-first-issue"]
assignees: ["junior-developer"]
---

# 🚀 [FEATURE & SEEDER] Modul Pengaturan Website (CMS Settings) & Update Database Seeders

Dokumen perencanaan ini dibuat sebagai panduan teknis (*issue task guide*) untuk **Junior Developer / Programmer** dalam mengimplementasikan **Modul Pengaturan Website (CMS Settings)** di Dashboard Admin dan memperbarui **seluruh data Seeders** agar sinkron dengan data operasional AL-HIKMAH terkini.

---

## 📌 Context & Motivation

1. **Modul Setting Website (CMS Setting)**:
   - **Masalah**: Saat ini beberapa informasi kontak (seperti nomor WhatsApp, username Instagram, email) tersimpan di file `.env` atau config. Jika pengelola ingin mengubah nomor CS/WA atau alamat email, mereka harus meminta developer mengubah file `.env` di server.
   - **Solusi**: Dibuatkan menu **Pengaturan Website** di Dashboard Admin (`/admin/settings`). Admin dapat mengubah nomor WhatsApp, username Instagram, email kontak, alamat lembaga, dan info header/footer secara dinamis langsung dari antarmuka Web Admin Panel.

2. **Pembaruan Data Seeders (`database/seeders/*`)**:
   - **Masalah**: Data dummy awal pada seeders lama belum sepenuhnya mencerminkan daftar program dan data operasional yang ada di template UI terbaru.
   - **Solusi**: Seluruh file seeder disesuaikan agar menyajikan data contoh yang presisi, lengkap, dan realistis untuk pengujian maupun inisialisasi aplikasi pertama kali (*fresh installation*).

---

## 📋 Summary Checklist Fitur

- [x] **Task 1**: Skema Database & Model `Setting`
- [x] **Task 2**: `SettingSeeder` untuk Data Awal Pengaturan
- [x] **Task 3**: Update Helper `site_setting()` & `wa_url()`
- [x] **Task 4**: Controller `Admin\SettingController` & Route `/admin/settings`
- [x] **Task 5**: Blade View Pengaturan Admin (`resources/views/admin/settings/index.blade.php`)
- [x] **Task 6**: Tambahkan Menu "Pengaturan Website" pada Sidebar / Navigasi Admin Layout
- [x] **Task 7**: Pembaharuan Seluruh File Seeder (`ProgramSeeder`, `UserSeeder`, `GallerySeeder`, `PaymentSeeder`, `DatabaseSeeder`, dll.)
- [x] **Task 8**: Testing & Validation Checklist

---

## 🛠️ Task 1: Skema Database & Model `Setting`

### 1.1 Buat Migration Tabel `settings`
Jalankan perintah berikut:
```bash
php artisan make:model Setting -m
```

Edit file migration `database/migrations/xxxx_xx_xx_xxxxxx_create_settings_table.php`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label')->nullable();
            $table->string('group')->default('general'); // general, contact, social, landing
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
```

### 1.2 Model `App\Models\Setting`
Edit `app/Models/Setting.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'label',
        'group',
    ];

    /**
     * Helper static untuk mengambil nilai setting berdasarkan key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Helper static untuk menyimpan/mengupdate setting.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
```

---

## ⚙️ Task 2: Membuat `SettingSeeder`

Buat seeder baru untuk inisialisasi kunci setting awal:
```bash
php artisan make:seeder SettingSeeder
```

Edit `database/seeders/SettingSeeder.php`:
```php
<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Group: contact
            [
                'key' => 'whatsapp_number',
                'value' => '6285786689008',
                'label' => 'Nomor WhatsApp CS',
                'group' => 'contact',
            ],
            [
                'key' => 'instagram_handle',
                'value' => 'houseofalhikmah',
                'label' => 'Username Instagram',
                'group' => 'social',
            ],
            [
                'key' => 'email_contact',
                'value' => 'belajarquranalhikmah@gmail.com',
                'label' => 'Email Kontak',
                'group' => 'contact',
            ],
            [
                'key' => 'office_address',
                'value' => 'Jabodetabek & Sekitarnya (Home Visit & Online)',
                'label' => 'Alamat / Area Layanan',
                'group' => 'contact',
            ],
            // Group: general
            [
                'key' => 'site_name',
                'value' => 'AL-HIKMAH',
                'label' => 'Nama Lembaga',
                'group' => 'general',
            ],
            [
                'key' => 'site_tagline',
                'value' => 'Menemani Generasi Qur\'ani Indonesia',
                'label' => 'Tagline Website',
                'group' => 'general',
            ],
            [
                'key' => 'registration_fee',
                'value' => '150000',
                'label' => 'Biaya Pendaftaran (Rp)',
                'group' => 'general',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
```

---

## 🌐 Task 3: Integration Helper `site_setting()` & `wa_url()`

Buka file `app/Helpers/settings.php` dan perbarui fungsinya agar memeriksa tabel `Setting` terlebih dahulu sebelum fallback ke `.env`/config:

```php
<?php

use App\Models\Setting;

if (!function_exists('site_setting')) {
    /**
     * Ambil nilai pengaturan website dari database (tabel settings), fallback ke config/env.
     */
    function site_setting(string $key, mixed $default = null): mixed
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $val = Setting::get($key);
                if ($val !== null && $val !== '') {
                    return $val;
                }
            }
        } catch (\Throwable $e) {
            // Fallback jika database belum di-migrate
        }

        return config("settings.{$key}", $default);
    }
}

if (!function_exists('wa_url')) {
    /**
     * Generate WhatsApp URL secara dinamis dari setting database.
     */
    function wa_url(?string $message = null): string
    {
        $phone = site_setting('whatsapp_number', '6285786689008');

        // Clean non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        $url = "https://wa.me/{$phone}";

        if (!empty($message)) {
            $url .= '?text=' . rawurlencode($message);
        }

        return $url;
    }
}
```

---

## 🎛️ Task 4: Controller & Routes

### 4.1 Buat Controller `SettingController`
```bash
php artisan make:controller Admin/SettingController
```

Edit `app/Http/Controllers/Admin/SettingController.php`:
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::all()->groupBy('group');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string|max:1000',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Pengaturan website berhasil diperbarui!');
    }
}
```

### 4.2 Daftarkan Route di `routes/web.php`
Di dalam grup `Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(...)`:
```php
use App\Http\Controllers\Admin\SettingController;

Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
```

---

## 🎨 Task 5: View Form Pengaturan Admin

Buat file view baru `resources/views/admin/settings/index.blade.php`:

```blade
@extends('layouts.admin')

@section('title', 'Pengaturan Website')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold"><i class="bi bi-gear-fill text-success me-2"></i>Pengaturan Website</h1>
            <p class="text-muted small mb-0">Kelola kontak, nomor WhatsApp, sosial media, dan informasi umum website AL-HIKMAH.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        
        <div class="row g-4">
            <!-- Informasi Kontak & WhatsApp -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4 fw-bold text-success fs-5">
                        <i class="bi bi-telephone-fill me-2"></i>Kontak & Customer Service
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Nomor WhatsApp CS <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-whatsapp text-success"></i></span>
                                <input type="text" class="form-control border-start-0" name="settings[whatsapp_number]" 
                                       value="{{ site_setting('whatsapp_number', '6285786689008') }}" required placeholder="Contoh: 6285786689008">
                            </div>
                            <small class="text-muted">Gunakan format internasional tanpa spasi (misal: 6285786689008).</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Email Resmi Kontak</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-primary"></i></span>
                                <input type="email" class="form-control border-start-0" name="settings[email_contact]" 
                                       value="{{ site_setting('email_contact', 'belajarquranalhikmah@gmail.com') }}" placeholder="email@domain.com">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Username Instagram</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-instagram text-danger"></i></span>
                                <input type="text" class="form-control border-start-0" name="settings[instagram_handle]" 
                                       value="{{ site_setting('instagram_handle', 'houseofalhikmah') }}" placeholder="houseofalhikmah">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Alamat / Area Layanan</label>
                            <textarea class="form-control" name="settings[office_address]" rows="2" 
                                      placeholder="Jabodetabek & Sekitarnya">{{ site_setting('office_address', 'Jabodetabek & Sekitarnya (Home Visit & Online)') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Umum Lembaga -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4 fw-bold text-success fs-5">
                        <i class="bi bi-building me-2"></i>Informasi Lembaga & Pendaftaran
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Nama Lembaga / Brand</label>
                            <input type="text" class="form-control" name="settings[site_name]" 
                                   value="{{ site_setting('site_name', 'AL-HIKMAH') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Tagline Website</label>
                            <input type="text" class="form-control" name="settings[site_tagline]" 
                                   value="{{ site_setting('site_tagline', 'Menemani Generasi Qur\'ani Indonesia') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Biaya Pendaftaran Standar (Rp)</label>
                            <input type="number" class="form-control" name="settings[registration_fee]" 
                                   value="{{ site_setting('registration_fee', '150000') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary-custom px-5 py-3 rounded-3 fw-bold fs-6">
                <i class="bi bi-save me-2"></i> Simpan Perubahan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection
```

---

## 🔄 Task 7: Pembaharuan Seluruh Data Seeders (`database/seeders/*`)

Junior Developer wajib memperbarui seluruh file seeder berikut agar data dummy yang dihasilkan saat `php artisan migrate:fresh --seed` terisi dengan data yang lengkap dan presisi:

### 7.1 Update `ProgramSeeder.php`
Sinkronkan seluruh modul dari UI template:
```php
<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            [
                'name' => 'Iqra & Dasar Al-Qur\'an',
                'description' => 'Memulai perjalanan mengenal huruf hijaiyah dan membaca Al-Qur\'an secara bertahap.',
                'duration_weeks' => 8,
                'price' => 400000,
                'level' => 'Anak (10-15 th)',
            ],
            [
                'name' => 'Tahsin Dasar',
                'description' => 'Membantu memperbaiki bacaan agar lebih baik dan sesuai dengan kaidah tajwid.',
                'duration_weeks' => 12,
                'price' => 450000,
                'level' => 'Anak (10-15 th)',
            ],
            [
                'name' => 'Adab & Doa Harian',
                'description' => 'Mengenalkan nilai-nilai adab Islami dan doa yang dapat diamalkan dalam kehidupan sehari-hari.',
                'duration_weeks' => 8,
                'price' => 350000,
                'level' => 'Anak (10-15 th)',
            ],
            [
                'name' => 'Tahfidz Al-Qur\'an',
                'description' => 'Mendampingi anak dalam menghafal Al-Qur\'an secara bertahap dengan murajaah dan pembiasaan.',
                'duration_weeks' => 16,
                'price' => 500000,
                'level' => 'Anak (10-15 th)',
            ],
            [
                'name' => 'Belajar dari Nol (Dewasa)',
                'description' => 'Tidak pernah terlambat untuk memulai. Program untuk siapa saja yang ingin belajar dari dasar.',
                'duration_weeks' => 12,
                'price' => 400000,
                'level' => 'Dewasa',
            ],
            [
                'name' => 'Tahsin Dewasa',
                'description' => 'Pendampingan untuk memperbaiki makhraj, tajwid, dan kualitas bacaan.',
                'duration_weeks' => 12,
                'price' => 450000,
                'level' => 'Dewasa',
            ],
            [
                'name' => 'Kelas Muslimah',
                'description' => 'Ruang belajar yang nyaman bagi muslimah bersama pendamping wanita.',
                'duration_weeks' => 12,
                'price' => 450000,
                'level' => 'Muslimah',
            ],
            [
                'name' => 'Tahfidz Dewasa',
                'description' => 'Mendampingi perjalanan menghafal dengan target yang disesuaikan kemampuan.',
                'duration_weeks' => 16,
                'price' => 550000,
                'level' => 'Dewasa',
            ],
            [
                'name' => 'Bahasa Arab Dasar',
                'description' => 'Mengenal kosakata dan percakapan dasar untuk membangun fondasi bahasa Arab.',
                'duration_weeks' => 12,
                'price' => 500000,
                'level' => 'Bahasa Arab',
            ],
            [
                'name' => 'Nahwu & Sharaf',
                'description' => 'Mempelajari dasar-dasar tata bahasa Arab sebagai bekal memahami teks keislaman.',
                'duration_weeks' => 16,
                'price' => 600000,
                'level' => 'Bahasa Arab',
            ],
        ];

        foreach ($programs as $program) {
            Program::updateOrCreate(
                ['name' => $program['name']],
                $program
            );
        }
    }
}
```

### 7.2 Update `DatabaseSeeder.php`
Pastikan `SettingSeeder::class` dipanggil paling awal:
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            RoleSeeder::class,
            ProgramSeeder::class,
            UserSeeder::class,
            LearningSessionSeeder::class,
            ProgressSeeder::class,
            PaymentSeeder::class,
            GallerySeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
```

### 7.3 Verifikasi Seeder Lain
Pastikan `UserSeeder`, `GallerySeeder`, `LearningSessionSeeder`, `ProgressSeeder`, `PaymentSeeder`, dan `NotificationSeeder` menggunakan ID role dan data program yang valid tanpa error constraint foreign key.

---

## 🧪 Acceptance Criteria & Verifikasi

- [x] Jalankan perintah:
  ```bash
  php artisan migrate:fresh --seed
  ```
  Harus berjalan **sukses tanpa error** 100%.
- [x] Buka browser dan login sebagai Admin (`admin@alhikmah.id`).
- [x] Akses halaman `/admin/settings`. Form pengaturan harus tampil dengan rapi.
- [x] Coba ubah nomor WhatsApp menjadi `6281299998888` dan simpan form.
- [x] Buka halaman utama (Home / Tahfidz / Program), periksa link WhatsApp di floating button, navbar, dan footer. Nomor WA baru harus otomatis ter-update di seluruh website.
- [x] Jalankan `vendor/bin/pint --dirty --format agent` dan `php artisan test --compact` untuk memastikan seluruh tes hijau.

---
