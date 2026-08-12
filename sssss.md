# 🚀 Rencana Implementasi Technical Fitur Notifikasi Tagihan SPP & Real-time Livewire Notifications

Dokumen ini memuat langkah-langkah implementasi teknis untuk membangun **Fitur Notifikasi Tagihan SPP Terpadu** pada Portal Orang Tua dan Admin Panel.

---

## 📌 Ringkasan Rencana Eksekusi

### 1. Database Migrations & Models
- **[NEW] Migrasi**: `2026_08_12_000004_add_due_date_to_payments_table.php` (menambahkan kolom `due_date` pada tabel `payments`).
- **[MODIFY] Models**: Ensure `Payment.php` casts `due_date` as date, ensure `Notification.php` fillable attributes include `type`, `title`, `message`, `read_at`, `is_read`.

### 2. Livewire Real-time Component
- **[NEW] Livewire Component**: `App\Livewire\Parent\ParentNotifications.php` (`parent-notifications`).
- **[NEW] Livewire View**: `resources/views/livewire/parent/notifications.blade.php`.

### 3. Backend Controllers & Admin Features
- **[NEW] Controller**: `App\Http\Controllers\Admin\PaymentController.php` dengan method `index()` & `sendReminder()`.
- **[MODIFY] `routes/web.php`**: Mendaftarkan rute `admin.payments.index` & `admin.payments.send-reminder`.
- **[MODIFY] Controllers**: `ParentDashboardController.php` dan `ParentPaymentController.php`.

### 4. Layouts & Frontend Views
- **[MODIFY] `resources/views/layouts/parent.blade.php`**: Mengganti ikon lonceng statis dengan `@livewire('parent.parent-notifications')`.
- **[MODIFY] `resources/views/parent/payments/index.blade.php`**: Menampilkan informasi tanggal jatuh tempo (`due_date`).
- **[MODIFY] `resources/views/admin/dashboard.blade.php`**: Menambahkan tombol *Kirim Pengingat Tagihan SPP* di widget pembayaran admin.

### 5. Automated Testing & Verification
- **[NEW] Pest Feature Test**: `tests/Feature/PaymentNotificationTest.php`.
- **Formatting**: `vendor/bin/pint --dirty --format agent`.
- **Dokumentasi**: Update `tentang.md`.

---

## 🧪 Proposed Changes

### Database & Migrations

#### [NEW] [2026_08_12_000004_add_due_date_to_payments_table.php](file:///c:/xampp/htdocs/al-hikmah-lms/database/migrations/2026_08_12_000004_add_due_date_to_payments_table.php)
#### [MODIFY] [Payment.php](file:///c:/xampp/htdocs/al-hikmah-lms/app/Models/Payment.php)
#### [MODIFY] [Notification.php](file:///c:/xampp/htdocs/al-hikmah-lms/app/Models/Notification.php)

---

### Livewire & Controllers

#### [NEW] [ParentNotifications.php](file:///c:/xampp/htdocs/al-hikmah-lms/app/Livewire/Parent/ParentNotifications.php)
#### [NEW] [notifications.blade.php](file:///c:/xampp/htdocs/al-hikmah-lms/resources/views/livewire/parent/notifications.blade.php)
#### [NEW] [PaymentController.php](file:///c:/xampp/htdocs/al-hikmah-lms/app/Http/Controllers/Admin/PaymentController.php)
#### [MODIFY] [web.php](file:///c:/xampp/htdocs/al-hikmah-lms/routes/web.php)

---

### Verification Plan

1. **Database Migration**: `php artisan migrate`
2. **Pest Feature Test**: `php artisan test --filter=PaymentNotificationTest`
3. **Pint Formatter**: `vendor/bin/pint --dirty --format agent`
