# DAFTAR USER & CREDENTIAL

## 📋 SEMUA USER DI SISTEM

### 👥 EMPLOYEES (7 orang)
1. **Elvina Emalia**
   - Email: `elvinae2468@gmail.com`
   - Role: Employee (Agency)
   - Password: `password123` (default)

2. **Herlambang Dwi Prasetyo**
   - Email: `herlambang.dwi@cmuchickens.com`
   - Role: Employee (Agency)
   - Password: `password123` (default)

3. **Ika Devi Noviansasi**
   - Email: `ikadevinoviansasi@gmail.com`
   - Role: Employee (Agency)
   - Password: `password123` (default)

4. **Ingge Septia Cahyadi**
   - Email: `ingge.cahyadi@gmail.com`
   - Role: Employee (Agency)
   - Password: `password123` (default)

5. **Mya Ramadhany**
   - Email: `9.putrimya@gmail.com`
   - Role: Employee (Agency)
   - Password: `password123` (default)

6. **Rita Ariyani**
   - Email: `rtariyani@gmail.com`
   - Role: Employee (Agency)
   - Password: `password123` (default)

7. **Stefanus Christian**
   - Email: `schristian.adip@gmail.com`
   - Role: Employee (Agency)
   - Password: `password123` (default)

---

### 👨‍💼 ADMINS (4 orang)

1. **Super Admin**
   - Email: `superadmin@cmuchickens.com`
   - Role: Super Admin (All Division)
   - Password: `admin123` (default)
   - Access: Full system access

2. **Admin Agency**
   - Email: `admin.agency@cmuchickens.com`
   - Role: Admin (Agency Division)
   - Password: `admin123` (default)
   - Access: Agency division only

3. **Admin Academy**
   - Email: `admin.academy@cmuchickens.com`
   - Role: Admin (Academy Division)
   - Password: `admin123` (default)
   - Access: Academy division only

4. **Raihan Dimas Fahriyanto**
   - Email: `yantofahri137@gmail.com`
   - Role: Admin
   - Password: `admin123` (default)
   - Created: 2026-01-12

---

## 🔐 INFORMASI PASSWORD

### Default Passwords:
- **Employee**: `password123`
- **Admin**: `admin123`

### Catatan Keamanan:
⚠️ Password tersimpan dalam bentuk **HASH (bcrypt)** di database
⚠️ Tidak bisa dilihat dalam plaintext
⚠️ Pastikan user mengganti password default setelah login pertama

---

## 🔧 RESET PASSWORD

Jika perlu reset password, gunakan Laravel Tinker:

```bash
php artisan tinker
```

Kemudian jalankan:
```php
# Reset password untuk user tertentu
User::find(ID)->update(['password' => Hash::make('password_baru')]);

# Contoh: Reset password Super Admin
User::find(17)->update(['password' => Hash::make('newpassword123')]);
```

Atau gunakan script yang sudah dibuat:
```bash
php reset_passwords.php
```

---

## 👤 CLIENT USERS

Client dibuat oleh Admin saat membuat order baru. 
Client otomatis mendapat:
- Email dari data order
- Password default: `password123`
- Role: `client`
- Akses: Dashboard client, monitoring project, chat

---

## 📊 STATISTIK

- Total Employees: **7 orang** (semua Agency division)
- Total Admins: **4 orang** (1 Super Admin, 2 Division Admin, 1 Regular Admin)
- Total Users: **11 orang**
- Client: Dinamis (bertambah saat ada order baru)

---

## 🚀 LOGIN

**URL Login**: `http://localhost/management_project/login`

Test dengan salah satu credential di atas!
