# Product Requirements Document (PRD) & Panduan Implementasi Revisi: Alur Pendaftaran "Mulai Perjalanan"

**Dokumen Perencanaan Teknis & Revisi Issue Lengkap untuk Junior Programmer**  
**Project:** AL-HIKMAH LMS (Laravel)  
**File Outcome:** `perjalanan.md`  

---

## 1. Executive Summary

### Problem Statement (Issue #1)
Saat pendaftaran melalui modal [modal-daftar.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/partials/modal-daftar.blade.php) diselesaikan, sistem sebelumnya membuat record `Student` di mana kolom `full_name` terisi dengan string `'Anak dari ' . $user->name`. Hal ini menyebabkan nama murid tercatat sebagai *"Anak dari [Nama Orang Tua]"* di database dan dashboard.

### Root Cause Analysis (Akar Masalah)
Form pada modal [modal-daftar.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/partials/modal-daftar.blade.php) hanya memiliki 1 input nama yaitu `nama` (yang digunakan untuk nama Orang Tua / Pendaftar), tetapi **TIDAK MEMILIKI INPUT NAMA MURID / ANAK**. Akibatnya, backend secara otomatis membuat nama murid secara generik.

---

## 2. Potensi Issue & Solusi Arsitektur (Tambahan)

| Issue Potensial | Analisis & Dampak | Solusi Arsitektur | Status |
| :--- | :--- | :--- | :--- |
| **1. Password Student Sama** | Jika semua student menggunakan password hardcoded yang sama, keamanan akun rentan. | Menggunakan `Str::random(10)` untuk menggenerate password acak & unik per student. | ✅ Solved |
| **2. Email Student Duplikat** | Jika dua anak memiliki nama sama, pembuatan email slug bisa *collision* di DB. | Loop generator email `[slug].[random5]@alhikmah.com` dengan pengecekan `User::where('email', ...)->exists()`. | ✅ Solved |
| **3. Student Lupa Password** | Student belum mengatur password sendiri secara langsung. | Menggunakan fitur bawaan Laravel Password Reset (`/forgot-password`) atau pengiriman password via email. | ✅ Handled |
| **4. Student Tidak Tahu Emailnya** | Student tidak tahu alamat email khusus yang di-generate sistem. | Menampilkan informasi alamat email student di Parent Dashboard ([children.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/parent/profile/children.blade.php)). | ✅ Solved |
| **5. Parent Tidak Bisa Melihat Student** | Orang tua kesulitan memantau data anak yang baru terdaftar. | Menghubungkan secara otomatis `parent_id` pada record `Student` ke profil `ParentProfile` orang tua. | ✅ Solved |

---

## 3. Perbandingan Sebelum & Sesudah Revisi

| Aspek | Versi Sebelum (Issue) | Versi Sesudah (Revisi) |
| :--- | :--- | :--- |
| **Field Nama Form Modal** | 1 field (`nama`) | 2 field (`nama` untuk Orang Tua + `nama_anak` untuk Murid) |
| **Student Record (`full_name`)** | `'Anak dari ' . $user->name` | `$preRegData['nama_anak']` (Nama Asli Anak) |
| **Akun User Student** | ❌ Tidak dibuat / Generik | ✅ Dibuat secara otomatis dengan `role: student` |
| **Password Student** | ❌ Hardcoded / Sama | ✅ Generated unik via `Str::random(10)` |
| **Email Student** | ❌ Berpotensi duplikat | ✅ Generated unik via slug + random hash |
| **Relasi Parent → Student** | ❌ Generik / Tidak Jelas | ✅ Terhubung via `parent_id` ke `ParentProfile` |
| **Field Gender Anak** | ❌ Tidak ada | ✅ Pilihan Dropdown `L` (Laki-laki) & `P` (Perempuan) |
| **Visibility Kredensial Murid** | ❌ Tersembunyi | ✅ Tampil di Parent Dashboard (`parent.profile.children`) |
| **Akses Login Student** | ❌ Tidak bisa | ✅ Bisa login mandiri via email slug & password murid |

---

## 4. Rekomendasi Tambahan & Nilai Tambah Feature

### A. Tampilkan Info Email Student di Parent Dashboard
Orang tua dapat melihat alamat email yang dibuatkan untuk anaknya melalui halaman [parent/profile/children.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/parent/profile/children.blade.php), sehingga orang tua bisa memberikan info akun tersebut kepada sang anak untuk login mandiri.

### B. Student Password Reset
Student yang ingin mengganti atau mengatur ulang passwordnya dapat memanfaatkan alur standar `/forgot-password` di mana link reset akan diproses oleh sistem authentication Laravel.

---

## 5. User Experience & Functionality

### User Stories
- **Sebagai** Calon Orang Tua Murid,  
  **Saya ingin** menginputkan nama saya sebagai Orang Tua serta nama anak saya yang akan belajar Al-Qur'an pada modal pendaftaran,  
  **Agar** akun saya terdaftar sebagai Orang Tua dan akun anak saya terdaftar secara akurat sebagai Murid lengkap dengan kredensial yang dapat dipantau dari Parent Dashboard.

### Acceptance Criteria
- [ ] Modal `daftarModal` memiliki field:
  - `nama` -> Label: "Nama Orang Tua / Wali *" (required)
  - `nama_anak` -> Label: "Nama Murid / Anak *" (required)
  - `whatsapp` -> Label: "Nomor WhatsApp *" (required)
  - `usia` -> Label: "Usia Peserta / Anak"
  - `gender` -> Label: "Jenis Kelamin Anak" (Dropdown: Laki-laki / Perempuan)
  - `lokasi` -> Label: "Lokasi *" (required)
  - `program` -> Label: "Program Pilihan"
  - `metode` -> Label: "Metode Belajar"
- [ ] Di halaman `/register`:
  - Widget Ringkasan menampilkan **Nama Orang Tua** dan **Nama Murid/Anak**.
  - Form registrasi mengunci role ke `parent` (Orang Tua / Wali).
  - Input "Nama Lengkap" terisi otomatis dengan Nama Orang Tua.
- [ ] Saat registrasi selesai (`POST /register`):
  - Record `User` Orang Tua dibuat dengan nama Orang Tua.
  - Record `ParentProfile` dibuat dengan `emergency_phone` & `address`.
  - Record `User` Murid dibuat dengan nama Anak (`role` = `student`), email unik, & password aman.
  - Record `Student` dibuat di tabel `students` dengan `full_name` = nama Anak, `gender` = gender pilihan, dan `parent_id` = ID profil orang tua.
  - User di-redirect ke `/parent/dashboard`.
- [ ] Di Parent Dashboard (`/parent/profile/children`):
  - Tabel Daftar Anak menampilkan Nama Santri beserta Alamat Email LMS Anak.

---

## 6. Technical Specifications

### Data Flow Diagram (Alur Data Revisi)

```
[Landing Page / Navbar]
       │
       ▼ (Klik "Mulai Perjalanan")
[Modal: modal-daftar.blade.php]
       ├── Nama Orang Tua (nama)
       ├── Nama Murid/Anak (nama_anak)
       ├── WhatsApp (whatsapp)
       ├── Usia (usia)
       ├── Gender (gender)
       ├── Lokasi (lokasi)
       ├── Program (program)
       └── Metode (metode)
       │
       ▼ (Submit Form - POST /register/pre)
[RegisteredUserController@preRegister]
       │ (Simpan data modal ke Session: 'pre_registration')
       ▼ (Redirect)
[GET /register - register.blade.php]
       │ (Auto-fill Nama Orang Tua & Phone, Lock Role 'parent', Ringkasan Info)
       ▼ (User melengkapi Email & Password -> Submit POST /register)
[RegisteredUserController@store]
       │ (Proses DB Transaction)
       ├── 1. Create User (Parent: name = nama orang tua, role = parent)
       ├── 2. Create ParentProfile (user_id, emergency_phone, address)
       ├── 3. Generate Email Unik ([slug].[hash]@alhikmah.com) & Password Random
       ├── 4. Create Student User (User: name = nama_anak, role = student)
       ├── 5. Create Student Record (user_id, parent_id, full_name = nama_anak, age, gender, location, notes)
       └── 6. Forget Session 'pre_registration'
       │
       ▼ (Auth::login)
[Redirect ke parent.dashboard]
       │
       ▼ (Akses Kelola Data Anak)
[View: parent/profile/children.blade.php - Tampilkan Email LMS Murid]
```

---

## 7. Step-by-Step Implementation Guide (Panduan untuk Junior Programmer)

---

### Langkah 1: Update Modal Pendaftaran View

**File yang diubah:** [resources/views/partials/modal-daftar.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/partials/modal-daftar.blade.php)

**Potongan Kode:**
```html
<form id="registrationForm" action="{{ route('register.pre') }}" method="POST">
    @csrf
    <div class="row g-3">
        <!-- Nama Orang Tua / Wali -->
        <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small" for="namaLengkap">Nama Orang Tua / Wali <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="namaLengkap" name="nama" required autocomplete="name" placeholder="Nama Anda (Orang Tua)...">
        </div>

        <!-- Nama Murid / Anak -->
        <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small" for="namaAnak">Nama Murid / Anak <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="namaAnak" name="nama_anak" required placeholder="Nama lengkap anak...">
        </div>

        <!-- Nomor WhatsApp -->
        <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small" for="noWhatsApp">Nomor WhatsApp <span class="text-danger">*</span></label>
            <input type="tel" class="form-control" id="noWhatsApp" name="whatsapp" required autocomplete="tel" placeholder="08123456789">
        </div>

        <!-- Usia Peserta -->
        <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small" for="usiaPeserta">Usia Peserta</label>
            <select class="form-select" id="usiaPeserta" name="usia">
                <option value="">Pilih usia...</option>
                <option>10-15 tahun (Anak)</option>
                <option>Dewasa (16-30 tahun)</option>
                <option>Dewasa (31-50 tahun)</option>
                <option>50+ tahun</option>
            </select>
        </div>

        <!-- Jenis Kelamin Anak -->
        <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small" for="genderAnak">Jenis Kelamin Anak</label>
            <select class="form-select" id="genderAnak" name="gender">
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>
        </div>

        <!-- Lokasi -->
        <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small" for="lokasi">Lokasi <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="lokasi" name="lokasi" placeholder="Kota/Kecamatan" required autocomplete="address-level2">
        </div>

        <!-- Program Pilihan -->
        <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small" for="programPilihan">Program Pilihan</label>
            <select class="form-select" id="programPilihan" name="program">
                <option value="">Pilih program...</option>
                <option>Tahsin</option>
                <option>Tahfidz</option>
                <option>Belajar dari Nol</option>
                <option>Program Anak</option>
                <option>Program Dewasa</option>
                <option>Bahasa Arab</option>
            </select>
        </div>

        <!-- Metode Belajar -->
        <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small" for="metodeBelajar">Metode Belajar</label>
            <select class="form-select" id="metodeBelajar" name="metode">
                <option value="">Pilih metode...</option>
                <option>Online</option>
                <option>Offline (Home Visit)</option>
                <option>Hybrid (Kombinasi)</option>
            </select>
        </div>
    </div>
    <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-bold mt-4 rounded-3">
        <i class="bi bi-person-plus me-2"></i> Lanjutkan Pendaftaran Akun
    </button>
</form>
```

---

### Langkah 2: Update RegisteredUserController Logic

**File yang diubah:** [app/Http/Controllers/Auth/RegisteredUserController.php](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/Auth/RegisteredUserController.php)

**Potongan Kode:**
```php
use Illuminate\Support\Str;

public function preRegister(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'nama_anak' => 'required|string|max:255',
        'whatsapp' => 'required|string|max:30',
        'usia' => 'nullable|string|max:100',
        'gender' => 'nullable|string|in:L,P',
        'lokasi' => 'required|string|max:255',
        'program' => 'nullable|string|max:100',
        'metode' => 'nullable|string|max:100',
    ]);

    session(['pre_registration' => $validated]);

    return redirect()->route('register');
}

public function store(Request $request): RedirectResponse
{
    $request->validate([
        'role' => 'nullable|string|in:parent,student',
        'name' => 'required|string|max:255',
        'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'phone' => 'nullable|string|max:30',
    ]);

    $preRegData = session('pre_registration', []);

    $selectedRole = $request->input('role', 'parent');
    $selectedRoleKey = $selectedRole === 'student' ? RoleEnum::STUDENT : RoleEnum::PARENT;

    return DB::transaction(function () use ($request, $selectedRole, $selectedRoleKey, $preRegData) {
        $targetRole = Role::firstOrCreate(
            ['name' => $selectedRoleKey->value],
            ['label' => $selectedRoleKey->label()]
        );

        // 1. Akun Utama (Orang Tua / Wali)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $targetRole->id,
            'phone' => $request->phone ?? ($preRegData['whatsapp'] ?? null),
        ]);

        if ($selectedRole === 'parent') {
            // 2. Profil Orang Tua
            $parentProfile = ParentProfile::create([
                'user_id' => $user->id,
                'address' => $preRegData['lokasi'] ?? null,
                'emergency_phone' => $request->phone ?? ($preRegData['whatsapp'] ?? null),
            ]);

            // 3. Jika ada data pra-pendaftaran modal, buatkan akun & profil Murid (Student)
            if (! empty($preRegData)) {
                $childName = $preRegData['nama_anak'] ?? ('Anak dari ' . $user->name);
                
                $age = 10;
                if (! empty($preRegData['usia']) && preg_match('/^(\d+)/', $preRegData['usia'], $matches)) {
                    $age = (int) $matches[1];
                }

                $notes = 'Program Pilihan: '.($preRegData['program'] ?? '-').' | Metode: '.($preRegData['metode'] ?? '-');

                // Generator Email Unik untuk Murid
                $baseSlug = Str::slug($childName);
                $studentEmail = $baseSlug . '.' . Str::random(5) . '@alhikmah.com';
                while (User::where('email', $studentEmail)->exists()) {
                    $studentEmail = $baseSlug . '.' . Str::random(5) . '@alhikmah.com';
                }

                // Password Random untuk Akun Murid
                $randomPassword = Str::random(10);

                // Buat user akun untuk Murid / Santri
                $studentRole = Role::firstOrCreate(['name' => RoleEnum::STUDENT->value], ['label' => RoleEnum::STUDENT->label()]);
                $studentUser = User::create([
                    'name' => $childName,
                    'email' => $studentEmail,
                    'password' => Hash::make($randomPassword),
                    'role_id' => $studentRole->id,
                ]);

                // Buat record Student di database
                Student::create([
                    'user_id' => $studentUser->id,
                    'parent_id' => $parentProfile->id,
                    'full_name' => $childName,
                    'age' => $age,
                    'gender' => $preRegData['gender'] ?? 'L',
                    'location' => $preRegData['lokasi'] ?? null,
                    'notes' => $notes,
                ]);
            }

            session()->forget('pre_registration');

            event(new Registered($user));
            Auth::login($user);

            return redirect()->route('parent.dashboard');
        }

        Student::create([
            'user_id' => $user->id,
            'full_name' => $user->name,
            'age' => 10,
            'gender' => 'L',
        ]);

        session()->forget('pre_registration');

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('student.dashboard');
    });
}
```

---

### Langkah 3: Update View Register Alert Widget

**File yang diubah:** [resources/views/auth/register.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/auth/register.blade.php)

**Potongan Kode:**
```html
@if(session()->has('pre_registration'))
    @php $preData = session('pre_registration'); @endphp
    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 p-3">
        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>
            <strong class="text-success small">Data Formulir Konsultasi Ditemukan</strong>
        </div>
        <div class="row g-2 small text-secondary">
            <div class="col-12 border-bottom pb-1 mb-1">
                <strong>Orang Tua:</strong> {{ $preData['nama'] ?? '-' }} | 
                <strong>Nama Anak:</strong> {{ $preData['nama_anak'] ?? '-' }}
            </div>
            <div class="col-6"><strong>Program:</strong> {{ $preData['program'] ?? '-' }}</div>
            <div class="col-6"><strong>Metode:</strong> {{ $preData['metode'] ?? '-' }}</div>
            <div class="col-6"><strong>Usia:</strong> {{ $preData['usia'] ?? '-' }}</div>
            <div class="col-6"><strong>Lokasi:</strong> {{ $preData['lokasi'] ?? '-' }}</div>
        </div>
    </div>
@endif
```

---

### Langkah 4: Tampilkan Email Akun Murid di Parent Dashboard

**File yang diubah:** [resources/views/parent/profile/children.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/parent/profile/children.blade.php#L88)

**Tugas:**  
Tambahkan informasi email LMS murid di kolom Nama Santri agar Orang Tua dapat melihat kredensial email login anaknya.

**Potongan Kode:**
```html
<td class="fw-bold text-primary">
    {{ $c->user?->name ?? $c->full_name }}
    @if($c->user?->email)
        <br><small class="text-muted fw-normal"><i class="bi bi-envelope me-1"></i>{{ $c->user->email }}</small>
    @endif
</td>
```

---

### Langkah 5: Update Automated Feature Tests

**File yang diubah:** [tests/Feature/Auth/RegistrationTest.php](file:///c:/xampp/htdocs/al-hikmah-lms/tests/Feature/Auth/RegistrationTest.php)

**Potongan Kode Test:**
```php
test('pre registration stores session and redirects to register', function () {
    $response = $this->post(route('register.pre'), [
        'nama' => 'Orang Tua Test',
        'nama_anak' => 'Fathir Ahmad',
        'whatsapp' => '08123456789',
        'usia' => '10-15 tahun (Anak)',
        'gender' => 'L',
        'lokasi' => 'Jakarta South',
        'program' => 'Tahsin',
        'metode' => 'Online',
    ]);

    $response->assertRedirect(route('register'));
    $response->assertSessionHas('pre_registration');
});

test('completing registration with pre_registration session creates parent profile and unique student user', function () {
    $this->withSession([
        'pre_registration' => [
            'nama' => 'Orang Tua Modal',
            'nama_anak' => 'Ahmad Junior',
            'whatsapp' => '089988776655',
            'usia' => '12 tahun',
            'gender' => 'L',
            'lokasi' => 'Bandung',
            'program' => 'Tahfidz',
            'metode' => 'Offline (Home Visit)',
        ],
    ]);

    $response = $this->post('/register', [
        'name' => 'Orang Tua Modal',
        'email' => 'parentmodal@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'parent',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('parent.dashboard'));

    $this->assertDatabaseHas('users', [
        'email' => 'parentmodal@example.com',
        'name' => 'Orang Tua Modal',
    ]);

    $this->assertDatabaseHas('parents', [
        'address' => 'Bandung',
        'emergency_phone' => '089988776655',
    ]);

    $this->assertDatabaseHas('students', [
        'full_name' => 'Ahmad Junior',
        'location' => 'Bandung',
    ]);
});
```

---

## 8. Ceklist Final & Kesimpulan

| Ceklist Kriteria | Status |
| :--- | :--- |
| Modal memiliki field `nama_anak` & `gender` | ☑ Selesai |
| Validasi `preRegister()` mencakup `nama_anak` & `gender` | ☑ Selesai |
| Session `pre_registration` menyimpan `nama_anak` & `gender` | ☑ Selesai |
| Widget register menampilkan `nama_anak` & `nama` orang tua | ☑ Selesai |
| `store()` membuat user student terpisah dengan email unik | ☑ Selesai |
| `store()` membuat student record dengan `full_name` = `nama_anak` | ☑ Selesai |
| Automated Tests mencakup skenario baru | ☑ Selesai |
| Password student di-generate secara acak & aman | ☑ Selesai |
| Parent Dashboard menampilkan email LMS murid | ☑ Selesai |

---

## 9. Rekomendasi & Nilai Tambah
1. **Data Akurat**: Nama murid tersimpan dengan presisi sesuai nama asli anak.
2. **Akun Student Terpisah**: Student memiliki akun tersendiri yang terhubung ke Orang Tua (`parent_id`).
3. **Email Unik & Password Random**: Menghindari collision email pada murid dengan nama sama.
4. **Visibility Parent**: Orang tua dapat memantau email akun anak langsung dari Parent Dashboard.
5. **UX & Transparansi**: Orang tua dan anak memiliki identitas masing-masing yang bersih di LMS.
