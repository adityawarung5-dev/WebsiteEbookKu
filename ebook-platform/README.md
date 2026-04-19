# 📚 EbookKu - Online Ebook Reading Platform

## Tech Stack
- **Backend:** PHP Native
- **Database:** MySQL
- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5
- **PDF Reader:** PDF.js (CDN)

---

## 🚀 Cara Menjalankan dengan XAMPP

### 1. Persyaratan
- XAMPP (PHP 7.4+ / 8.x, MySQL 5.7+)
- Web Browser modern (Chrome, Firefox, Edge)

---

### 2. Instalasi

**Langkah 1 — Copy Project**
```
Salin folder `ebook-platform` ke:
C:\xampp\htdocs\ebook-platform\
```

**Langkah 2 — Buat Database**
1. Buka XAMPP Control Panel → Start **Apache** dan **MySQL**
2. Buka browser → `http://localhost/phpmyadmin`
3. Klik **New** → Buat database baru: `ebook_platform`
4. Pilih database `ebook_platform` → Klik tab **Import**
5. Pilih file `database.sql` → Klik **Go**

**Langkah 3 — Konfigurasi Database** *(opsional)*
Edit `config/koneksi.php` jika perlu:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // username MySQL Anda
define('DB_PASS', '');          // password MySQL Anda (default XAMPP: kosong)
define('DB_NAME', 'ebook_platform');
```

**Langkah 4 — Set Permission Upload Folder**

Di Linux/Mac:
```bash
chmod 755 uploads/ebooks uploads/covers
```

Di Windows: Klik kanan folder → Properties → pastikan tidak Read-Only.

**Langkah 5 — Akses Website**
Buka browser → `http://localhost/ebook-platform/`

---

## 🔑 Akun Default

| Role  | Email                | Password |
|-------|----------------------|----------|
| Admin | admin@ebookku.com    | password |
| User  | budi@example.com     | password |
| User  | siti@example.com     | password |

---

## 📁 Struktur Project

```
ebook-platform/
├── index.php               # Halaman utama
├── login.php               # Login
├── register.php            # Registrasi
├── logout.php              # Logout
├── ebook.php               # Daftar ebook + search + filter
├── detail_ebook.php        # Detail ebook + komentar
├── baca_ebook.php          # PDF reader (PDF.js)
├── favorit.php             # Ebook favorit user
├── favorit_action.php      # AJAX: toggle favorit
│
├── admin/
│   ├── dashboard.php       # Dashboard admin
│   ├── tambah_ebook.php    # Upload ebook baru
│   ├── edit_ebook.php      # Edit ebook
│   ├── hapus_ebook.php     # Hapus ebook
│   ├── ebook_list.php      # Daftar semua ebook
│   ├── kategori.php        # Kelola kategori
│   ├── users.php           # Kelola pengguna
│   ├── komentar.php        # Moderasi komentar
│   └── includes/
│       └── sidebar.php     # Sidebar admin
│
├── config/
│   └── koneksi.php         # Konfigurasi DB + helper functions
│
├── includes/
│   ├── navbar.php          # Navbar utama
│   ├── footer.php          # Footer
│   └── ebook_card.php      # Komponen kartu ebook
│
├── assets/
│   ├── css/style.css       # Stylesheet utama
│   ├── js/app.js           # JavaScript utama
│   └── images/             # Gambar statis
│
├── uploads/
│   ├── ebooks/             # File PDF ebook
│   └── covers/             # Gambar cover ebook
│
└── database.sql            # Script database
```

---

## ✨ Fitur Lengkap

### 👤 User
- ✅ Register & Login
- ✅ Logout
- ✅ Cari ebook (search)
- ✅ Filter ebook per kategori
- ✅ Baca ebook online (PDF.js)
- ✅ Komentar pada ebook
- ✅ Favorit ebook (❤️ toggle + toast notifikasi)
- ✅ Halaman favorit saya

### 🛡️ Admin
- ✅ Dashboard statistik
- ✅ Upload ebook PDF + cover
- ✅ Edit & hapus ebook
- ✅ Kelola kategori
- ✅ Kelola pengguna + ubah role
- ✅ Moderasi komentar

---

## 🎨 Fitur UI/UX
- Modern navbar sticky
- Hero section dengan search bar
- Card grid responsif (2/3/4 kolom)
- Hover animation & shadow effect
- Favorit toggle dengan animasi ❤️
- Toast notification (TOP CENTER)
- PDF reader dengan navigasi halaman & zoom
- Filter kategori interaktif
- Mobile-friendly responsive

---

## ⚠️ Catatan Penting
1. Untuk upload PDF besar, edit `php.ini` XAMPP:
   ```ini
   upload_max_filesize = 50M
   post_max_size = 50M
   max_execution_time = 300
   ```
2. PDF.js memuat PDF via URL — pastikan Apache berjalan
3. Folder `uploads/` harus writable oleh web server

---

## 🔧 Troubleshooting

**Database error?** → Pastikan MySQL berjalan dan database sudah diimport

**Upload gagal?** → Cek permission folder `uploads/` dan setting `php.ini`

**PDF tidak muncul?** → Pastikan file PDF ada di `uploads/ebooks/` dan Apache berjalan

**Cover tidak tampil?** → Pastikan file cover ada di `uploads/covers/`
