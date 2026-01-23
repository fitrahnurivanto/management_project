# 📧 NOTIFICATION SYSTEM - DOCUMENTATION

## ✅ Implemented Notifications

### 1. **PROJECT NOTIFICATIONS**

#### a. Project Created (ke Client & Team Members)
- **Trigger**: Admin membuat project baru
- **Penerima**: 
  - Client yang terkait dengan project
  - Team member yang di-assign ke project
- **Isi Email**: 
  - Nama project & kode project
  - Client name
  - Budget & deadline
  - Link untuk lihat detail project
- **File**: `ProjectCreatedNotification.php`

#### b. Project Status Changed (ke Client)
- **Trigger**: Admin mengubah status project (pending → in_progress → completed, dll)
- **Penerima**: Client yang memiliki project
- **Isi Email**:
  - Nama project
  - Status lama → Status baru (dengan emoji)
  - Link untuk lihat detail project
- **File**: `ProjectStatusChangedNotification.php`

#### c. Project Deadline Reminder (ke Client & Team Members)
- **Trigger**: Command otomatis (dijadwalkan setiap hari)
- **Penerima**: 
  - Client
  - Semua team member project
- **Kondisi**: Project yang deadline-nya 1-3 hari lagi
- **Isi Email**:
  - Nama project
  - Sisa hari sampai deadline
  - Status project saat ini
  - Link untuk lihat detail
- **File**: `ProjectDeadlineNotification.php`
- **Command**: `php artisan projects:send-deadline-reminders`

---

### 2. **ORDER NOTIFICATIONS**

#### a. Order Received (ke Admin)
- **Trigger**: Client membuat order baru
- **Penerima**: Semua admin & superadmin
- **Isi Email**:
  - Order number
  - Nama client & company
  - Total amount
  - List services yang dipesan
  - Link untuk approve/review order
- **File**: `OrderReceivedNotification.php`

#### b. Payment Confirmed (ke Client)
- **Trigger**: Admin konfirmasi pembayaran order
- **Penerima**: Client yang membuat order
- **Isi Email**:
  - Order number & PKS number
  - Total amount yang sudah dibayar
  - Status: Lunas
  - Link untuk lihat order
- **File**: `PaymentReceivedNotification.php`

---

### 3. **PAYMENT REQUEST NOTIFICATIONS**

#### a. Payment Request Created (ke Admin)
- **Trigger**: Employee mengajukan payment request
- **Penerima**: Semua admin & superadmin
- **Isi Email**:
  - Nama employee
  - Project/Class terkait
  - Jumlah yang diminta
  - Jam kerja
  - Notes dari employee
  - Link untuk approve/reject
- **File**: `PaymentReceivedNotification.php`

#### b. Payment Request Approved/Rejected (ke Employee)
- **Trigger**: Admin approve atau reject payment request
- **Penerima**: Employee yang mengajukan
- **Isi Email**:
  - Status: Approved ✅ / Rejected ❌
  - Jumlah yang disetujui (jika approved)
  - Notes dari admin
  - Link untuk lihat detail
- **File**: `PaymentReceivedNotification.php`

---

### 4. **EXPENSE NOTIFICATIONS (Finance System)**

#### a. Expense Approved (ke Admin)
- **Trigger**: Finance approve expense yang diajukan admin
- **Penerima**: Admin yang membuat expense
- **Isi Email**:
  - Status: Approved ✅
  - Expense type & amount
  - Project terkait
  - Approved by Finance
  - Link untuk lihat detail
- **File**: `PaymentReceivedNotification.php`

#### b. Expense Rejected (ke Admin)
- **Trigger**: Finance reject expense
- **Penerima**: Admin yang membuat expense
- **Isi Email**:
  - Status: Rejected ❌
  - Reason untuk reject
  - Expense details
  - Link untuk revisi
- **File**: `PaymentReceivedNotification.php`

---

## 🔧 CARA TESTING NOTIFIKASI

### Manual Testing (Lewat File PHP)

Saya sudah buat 2 file testing:

#### 1. **test_email.php** - Test koneksi email
```bash
php test_email.php
```
Output: Mengirim test email sederhana untuk verifikasi SMTP berfungsi

#### 2. **test_notification.php** - Test notifikasi spesifik
```bash
php test_notification.php
```
Menu:
1. Notifikasi Project Baru ke Client
2. Notifikasi Perubahan Status Project ke Client  
3. Notifikasi Project Baru ke Team Member

Pilih nomor 1-3, email otomatis terkirim ke user yang dituju.

---

### Testing via Application Flow

#### Test 1: Project Created Notification
1. Login sebagai **admin** (`admin@gmail.com` / `admin123`)
2. Buat project baru di menu Projects → Create Project
3. **Result**: Client akan menerima email "Project Baru"

#### Test 2: Project Status Changed
1. Login sebagai **admin**
2. Buka detail project → Ubah status (misal: pending → in_progress)
3. **Result**: Client menerima email "Status Project Berubah"

#### Test 3: Project Team Member Assigned
1. Login sebagai **admin**
2. Buka detail project → Assign Team Member
3. Pilih employee dan role
4. **Result**: Employee menerima email "Ditugaskan ke Project"

#### Test 4: Order Received
1. Login sebagai **client** atau buat order baru
2. Submit order dengan upload bukti transfer
3. **Result**: Admin menerima email "Order Baru dari Client"

#### Test 5: Payment Confirmed
1. Login sebagai **admin**
2. Buka Orders → Konfirmasi pembayaran order
3. **Result**: Client menerima email "Pembayaran Dikonfirmasi"

#### Test 6: Payment Request
1. Login sebagai **employee** (`employee@gmail.com` / `employee123`)
2. Buat payment request baru
3. **Result**: Admin menerima email "Payment Request Baru"
4. Login sebagai **admin** → Approve/Reject request
5. **Result**: Employee menerima email "Payment Request Disetujui/Ditolak"

#### Test 7: Expense Approval (Finance)
1. Login sebagai **admin** → Buat expense di project
2. Login sebagai **finance** (`finance@gmail.com` / `finance123`)
3. Approve atau reject expense
4. **Result**: Admin menerima email "Expense Disetujui/Ditolak"

#### Test 8: Deadline Reminder
```bash
php artisan projects:send-deadline-reminders
```
Mengirim email ke client & team member untuk project yang deadline-nya 1-3 hari lagi.

---

## 📅 AUTOMATED NOTIFICATIONS (Scheduled)

Untuk menjalankan deadline reminder otomatis setiap hari, tambahkan ke `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Kirim deadline reminder setiap hari jam 9 pagi
    $schedule->command('projects:send-deadline-reminders')
             ->dailyAt('09:00');
}
```

Lalu jalankan Laravel scheduler:
```bash
php artisan schedule:work
```

Atau setup di server dengan cron job:
```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🎯 NOTIFICATION CHANNELS

Semua notifikasi menggunakan 2 channel:
1. **Email** - Dikirim ke inbox email user
2. **Database** - Disimpan di tabel `notifications` untuk notifikasi bell icon

---

## 📊 NOTIFICATION STATISTICS

Cek notifikasi yang terkirim:
```bash
# Total notifikasi di database
php artisan tinker
>>> \App\Models\User::find(1)->notifications->count()

# Unread notifications
>>> \App\Models\User::find(1)->unreadNotifications->count()

# Mark as read
>>> \App\Models\User::find(1)->unreadNotifications->markAsRead()
```

---

## ✉️ EMAIL CONFIGURATION

Email sudah dikonfigurasi di `.env`:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=fitrahnurivanto@gmail.com
MAIL_PASSWORD=ljycqtzhqhueljzs
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=fitrahnurivanto@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 🎉 SUMMARY

**Total Notifications Implemented: 8**

✅ Project Created → Client & Team Members  
✅ Project Status Changed → Client  
✅ Project Deadline Reminder → Client & Team Members  
✅ Order Received → Admin  
✅ Payment Confirmed → Client  
✅ Payment Request Created → Admin  
✅ Payment Request Approved/Rejected → Employee  
✅ Expense Approved/Rejected → Admin  

**Controllers Updated:**
- `ProjectController.php` ✅
- `OrderController.php` ✅
- `Employee/PaymentRequestController.php` ✅
- `Admin/PaymentRequestController.php` ✅
- `Finance/ExpenseController.php` ✅

**Commands Added:**
- `SendDeadlineReminders.php` ✅

---

## 🚀 NEXT STEPS (Optional)

1. **Real-time Notifications**: Implementasi WebSocket dengan Laravel Echo + Pusher
2. **SMS Notifications**: Tambah SMS gateway untuk notifikasi urgent
3. **WhatsApp Notifications**: Integrasi WhatsApp Business API
4. **Notification Preferences**: User bisa pilih jenis notifikasi yang ingin diterima
5. **Email Templates**: Customize design email dengan Blade components

---

🎯 **All critical notifications are now implemented and working!**
