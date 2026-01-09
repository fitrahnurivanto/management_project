# 📧 NOTIFICATION SYSTEM SETUP GUIDE

## 1. GMAIL SMTP CONFIGURATION

### A. Generate App Password (WAJIB!)

Karena Gmail tidak lagi support "less secure apps", kamu harus pakai **App Password**:

1. **Buka Google Account**
   - Go to: https://myaccount.google.com/
   - Login dengan Gmail yang mau dipakai

2. **Enable 2-Step Verification** (jika belum)
   - Klik "Security" di menu kiri
   - Cari "2-Step Verification"
   - Klik "Get Started" dan ikuti langkah-langkahnya

3. **Generate App Password**
   - Tetap di halaman Security
   - Cari "App passwords" (paling bawah)
   - Klik "App passwords"
   - Select app: **Mail**
   - Select device: **Other (Custom name)**
   - Ketik: "Laravel Management System"
   - Klik **Generate**
   - **COPY password 16 digit** yang muncul (contoh: `abcd efgh ijkl mnop`)
   - **SIMPAN baik-baik!** Ini hanya muncul sekali

### B. Update File .env

Buka file `.env` di root project, cari bagian MAIL dan ganti:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=emailkamu@gmail.com          # ← Ganti dengan email
MAIL_PASSWORD=abcdefghijklmnop             # ← Paste App Password (TANPA SPASI!)
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=emailkamu@gmail.com      # ← Sama dengan MAIL_USERNAME
MAIL_FROM_NAME="Management System"
```

**⚠️ PENTING:**
- `MAIL_PASSWORD` harus App Password 16 digit, **BUKAN password Gmail biasa!**
- Hapus semua spasi di App Password (copy: `abcd efgh ijkl mnop` → paste: `abcdefghijklmnop`)
- `MAIL_USERNAME` dan `MAIL_FROM_ADDRESS` harus sama

### C. Clear Config Cache

Setelah edit .env, run:

```bash
php artisan config:clear
```

---

## 2. TEST EMAIL FUNCTIONALITY

### Test kirim email:

```bash
php artisan tinker
```

Lalu ketik:

```php
Mail::raw('Test email dari Laravel', function($message) {
    $message->to('emailtujuan@gmail.com')
            ->subject('Test Email');
});
```

Jika berhasil, akan return `true` dan email masuk ke inbox.

**Jika error:**
- Cek spam folder
- Pastikan App Password benar (16 digit, no space)
- Pastikan 2FA sudah aktif
- Cek MAIL_USERNAME = MAIL_FROM_ADDRESS

---

## 3. SETUP CRON JOB (SCHEDULER)

Laravel Scheduler butuh 1 cron job yang jalan setiap menit. Ada 2 cara:

### CARA 1: Windows Task Scheduler (Production/Local yang always on)

1. Buka **Task Scheduler** (cari di Start Menu)
2. Klik **Create Basic Task**
3. Name: `Laravel Scheduler`
4. Trigger: **Daily** → Start jam 00:00
5. Action: **Start a program**
   - Program: `C:\laragon\bin\php\php-8.2.12-Win32-vs16-x64\php.exe`
   - Arguments: `C:\laragon\www\management_project\artisan schedule:run`
6. Finish

7. **Edit task** (double-click task yang baru dibuat)
   - Tab "Triggers" → Edit → Repeat task every: **1 minute** for a duration of: **Indefinitely**
   - Tab "Settings" → Uncheck "Stop the task if it runs longer than: 3 days"
   - OK

### CARA 2: Manual Testing (Development)

Run command ini setiap mau test cron:

```bash
php artisan schedule:run
```

Atau biarkan jalan terus dengan:

```bash
php artisan schedule:work
```

---

## 4. NOTIFICATION TYPES

Sistem akan kirim notifikasi untuk:

### 📅 Project Deadline
- **H-3** (3 hari sebelum deadline)
- **H-1** (1 hari sebelum deadline)
- **Recipients:** PIC Internal + Team Members
- **Schedule:** Daily at 8:00 AM
- **Condition:** Only for projects with status `in_progress` or `on_hold`

### 📋 Project Created
- **Trigger:** New project dibuat
- **Recipients:** All team members assigned
- **Method:** Email + In-app notification

### 💰 Order Received
- **Trigger:** Order baru masuk
- **Recipients:** All admins (Super Admin, Agency Admin, Academy Admin)
- **Method:** Email + In-app notification

### 💵 Payment Received
- **Trigger:** Payment status changed to 'paid'
- **Recipients:** All admins
- **Method:** Email + In-app notification

### 🔄 Project Status Changed
- **Trigger:** Project status updated
- **Recipients:** PIC + Team + Client (if completed)
- **Method:** Email + In-app notification

---

## 5. USER NOTIFICATION SETTINGS

Setiap user bisa mengatur notifikasi mereka di:
- **URL:** `/admin/settings/notifications` (akan dibuat)

**Default settings:**
- ✅ Email: ON
- ✅ In-app: ON

User bisa turn off per jenis notifikasi.

---

## 6. TROUBLESHOOTING

### Email tidak terkirim?

1. **Check .env file:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. **Test SMTP connection:**
   ```bash
   php artisan tinker
   >>> \Config::get('mail')
   ```
   Pastikan semua config benar.

3. **Check Laravel logs:**
   ```
   storage/logs/laravel.log
   ```

4. **Gmail blocked?**
   - Cek email "Security alert" dari Google
   - Pastikan 2FA aktif
   - Generate ulang App Password

### Cron job tidak jalan?

1. **Manual test:**
   ```bash
   php artisan notifications:check-deadlines
   ```

2. **Check schedule list:**
   ```bash
   php artisan schedule:list
   ```

3. **Force run scheduler:**
   ```bash
   php artisan schedule:run
   ```

---

## 7. MONITORING

### Check notifications terkirim:

```bash
php artisan tinker
>>> App\Models\Notification::latest()->limit(10)->get()
```

### Check notification settings:

```bash
>>> App\Models\NotificationSetting::with('user')->get()
```

### Count unread notifications per user:

```bash
>>> App\Models\Notification::whereNull('read_at')->count()
```

---

## 8. SECURITY

⚠️ **JANGAN COMMIT .env ke Git!**

File `.env` sudah ada di `.gitignore`, tapi pastikan tidak commit email/password.

Jika tidak sengaja commit:
```bash
git rm --cached .env
git commit -m "Remove .env from git"
```

---

**Setup Complete! 🎉**

Sistem notifikasi sekarang siap digunakan. Email akan terkirim otomatis sesuai trigger yang sudah di-set.
