# CBT SMK AL QODIRIYAH

[![PHP Version](https://img.shields.io/badge/PHP-5.x-777BB4?logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-MariaDB-4479A1?logo=mysql)](https://mariadb.org)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-3.x-7952B3?logo=bootstrap)](https://getbootstrap.com)

Aplikasi **Computer Based Test (CBT)** untuk SMK Al Qodiriyah. Sistem ujian online berbasis web yang mendukung soal pilihan ganda, esai, serta berbagai tipe media (gambar, audio, video).

---

## 📋 Daftar Isi

- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Fitur Utama](#-fitur-utama)
- [Requirements](#-requirements)
- [Panduan Instalasi](#-panduan-instalasi)
  - [Localhost (XAMPP/LAMPP)](#1-localhost-xampplampp)
  - [VPS (Ubuntu/Debian)](#2-vps-ubuntudebian)
  - [Shared Hosting (cPanel)](#3-shared-hosting-cpanel)
- [Struktur Database](#-struktur-database)
- [Kredensial Default](#-kredensial-default)
- [Catatan Migrasi & Perbaikan](#-catatan-migrasi--perbaikan)
- [Changelog](#-changelog)

---

## 🔧 Teknologi yang Digunakan

### Backend
| Komponen | Teknologi | Keterangan |
|----------|-----------|------------|
| **Bahasa** | PHP 5.x | Menggunakan fungsi `mysql_*` (deprecated) |
| **Database** | MySQL / MariaDB | Database `beesmartv3` dengan 16 tabel |
| **PDF Generator** | FPDF | Untuk cetak kartu, daftar hadir, berita acara |
| **Spreadsheet** | PHPExcel | Import/export data via Excel (.xls/.xlsx) |
| **Session** | Cookie-based | Manajemen sesi login menggunakan cookies |

### Frontend
| Komponen | Teknologi | Keterangan |
|----------|-----------|------------|
| **CSS Framework** | Bootstrap 3.x | UI responsif dan komponen siap pakai |
| **JavaScript** | jQuery 1.4+ | Interaksi DOM dan AJAX requests |
| **Rich Text Editor** | TinyMCE | WYSIWYG editor untuk input soal |
| **Math Rendering** | MathJax | Render rumus matematika (LaTeX) |
| **Charts** | Morris.js | Grafik dan visualisasi data |
| **Icons** | Font Awesome 4.x | Ikon UI |
| **Date Picker** | jQuery DateTimePicker | Input tanggal dan waktu |

### Arsitektur
```
cbt-smkalq/
├── config/           # Konfigurasi database & server
├── panel/            # Admin panel (backend management)
│   ├── pages/        # Modul-modul panel admin
│   └── vendor/       # Library frontend (Bootstrap, etc.)
├── lib/              # Library pendukung (PHPExcel, FPDF)
├── database/         # Script backup & restore
├── css/, js/         # Assets frontend
├── images/, pictures/# Media files
├── fotosiswa/        # Foto peserta ujian
├── MathJax/          # Library MathJax
└── tinymce/          # TinyMCE editor
```

---

## ✨ Fitur Utama

### 👥 Manajemen Pengguna
- **Multi-role**: Admin, Guru, Pengawas, Siswa
- Ubah password user dari panel admin
- Login dengan validasi IP (mencegah login ganda)
- Upload foto siswa secara batch

### 📚 Data Master
- Manajemen data siswa, kelas, jurusan, mata pelajaran
- Import data via Excel (.xls/.xlsx)
- Pengaturan tahun ajaran aktif
- Identitas sekolah (logo, banner, warna tema)

### 📝 Bank Soal
- **Tipe Soal**: Pilihan ganda (4 atau 5 opsi) dan esai
- Lampiran media: gambar, audio (MP3), video (MP4)
- Input soal manual atau import dari Excel
- Dukungan rumus matematika (MathJax/LaTeX)
- Acak soal dan acak urutan jawaban
- Kategori dan level kesulitan

### 📋 Paket & Penjadwalan Ujian
- Buat paket soal dari bank soal
- Penjadwalan ujian dengan tanggal, jam, dan durasi
- Token ujian untuk keamanan
- Setting batas waktu masuk (terlambat)
- Pengaturan sesi (multi-shift)
- Aktivasi/nonaktifkan ujian

### 🖥️ Pelaksanaan Ujian
- Timer countdown real-time
- Simpan jawaban otomatis
- Fitur "ragu-ragu" untuk review
- Navigasi soal (next/prev/langsung ke nomor)
- Lock browser (disable right-click, Ctrl+C/V)
- Idle timeout detection
- Resume ujian jika terputus

### 👁️ Monitoring & Pengawasan
- **Status Peserta**: sedang ujian, selesai, belum mulai
- Monitoring ping (koneksi peserta)
- Log event (aktivitas peserta)
- Reset login peserta
- Akhiri ujian paksa

### 📊 Penilaian & Rekap
- Penilaian otomatis (pilihan ganda)
- Penilaian manual untuk soal esai
- Rekap nilai per kelas, mapel, jenis ujian
- Analisa butir soal (tingkat kesulitan, daya beda)
- Sebaran nilai (grafik)
- Export ke Excel dan PDF

### 🖨️ Cetak Dokumen
- Kartu peserta ujian (dengan foto)
- Daftar hadir per sesi/ruang
- Berita acara ujian
- Daftar nilai per kelas/mapel

### 💾 Database
- Backup database (per tabel atau semua)
- Restore database dari file backup
- Download file backup
- Mode sinkronisasi (lokal/pusat)

---

## 📦 Requirements

### Minimum Server Requirements
- **Web Server**: Apache 2.4+ atau Nginx
- **PHP**: 5.4 - 5.6 (dengan ekstensi `mysql`)
- **MySQL**: 5.5+ atau MariaDB 10.0+
- **RAM**: Minimal 512 MB
- **Storage**: Minimal 500 MB (+ space untuk media)

### PHP Extensions (Wajib)
```
mysql, gd, mbstring, zip, xml
```

### Folder dengan Write Permission
```
/fotosiswa, /images, /pictures, /audio, /video, /output, /file-excel
/opt/lampp/backup (untuk file backup database)
```

---

## 🚀 Panduan Instalasi

### 1. Localhost (XAMPP/LAMPP)

#### Windows (XAMPP)
```bash
# 1. Download dan install XAMPP (versi dengan PHP 5.6)
#    https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/5.6.40/

# 2. Salin folder project ke htdocs
#    C:\xampp\htdocs\cbt-smkalq\

# 3. Buat database via phpMyAdmin
#    - Buka http://localhost/phpmyadmin
#    - Buat database baru: beesmartv3
#    - Import file: config/beesmartv3.sql

# 4. Konfigurasi koneksi database
#    Edit file: config/server.php
```

**config/server.php:**
```php
<?php
$sqlconn = @mysql_connect("localhost:3306", "root", "");
mysql_select_db("beesmartv3", $sqlconn);
$mode = "lokal"; // pilih 'lokal' atau 'pusat'
?>
```

```bash
# 5. Akses aplikasi
#    Siswa: http://localhost/cbt-smkalq/
#    Admin: http://localhost/cbt-smkalq/panel/
```

#### Linux (LAMPP)
```bash
# 1. Install LAMPP
sudo chmod +x xampp-linux-*-installer.run
sudo ./xampp-linux-*-installer.run

# 2. Salin project ke htdocs
sudo cp -r cbt-smkalq /opt/lampp/htdocs/

# 3. Buat database
/opt/lampp/bin/mysql -u root -e "CREATE DATABASE beesmartv3"
/opt/lampp/bin/mysql -u root beesmartv3 < /opt/lampp/htdocs/cbt-smkalq/config/beesmartv3.sql

# 4. Konfigurasi koneksi (edit config/server.php)

# 5. Set permission folder
sudo chmod +x /opt/lampp/htdocs/cbt-smkalq/setup_permissions.sh
sudo /opt/lampp/htdocs/cbt-smkalq/setup_permissions.sh

# Atau manual:
sudo chown -R daemon:daemon /opt/lampp/htdocs/cbt-smkalq/fotosiswa
sudo chown -R daemon:daemon /opt/lampp/htdocs/cbt-smkalq/images
sudo chown -R daemon:daemon /opt/lampp/htdocs/cbt-smkalq/pictures
sudo chown -R daemon:daemon /opt/lampp/htdocs/cbt-smkalq/audio
sudo chown -R daemon:daemon /opt/lampp/htdocs/cbt-smkalq/video
sudo chown -R daemon:daemon /opt/lampp/htdocs/cbt-smkalq/output
sudo chown -R daemon:daemon /opt/lampp/htdocs/cbt-smkalq/file-excel
sudo mkdir -p /opt/lampp/backup && sudo chown daemon:daemon /opt/lampp/backup

# 6. Start LAMPP
sudo /opt/lampp/lampp start

# 7. Akses aplikasi
#    Siswa: http://localhost/cbt-smkalq/
#    Admin: http://localhost/cbt-smkalq/panel/
```

---

### 2. VPS (Ubuntu/Debian)

#### Langkah 1: Install LAMP Stack
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Apache
sudo apt install apache2 -y

# Install MySQL/MariaDB
sudo apt install mariadb-server mariadb-client -y
sudo mysql_secure_installation

# Install PHP 5.6 (dari PPA untuk Ubuntu)
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php5.6 php5.6-mysql php5.6-gd php5.6-mbstring php5.6-xml php5.6-zip libapache2-mod-php5.6 -y

# Aktifkan PHP 5.6
sudo a]2dismod php7.* php8.*
sudo a2enmod php5.6
sudo systemctl restart apache2
```

#### Langkah 2: Setup Database
```bash
# Login ke MySQL
sudo mysql -u root -p

# Buat database dan user
CREATE DATABASE beesmartv3;
CREATE USER 'cbtuser'@'localhost' IDENTIFIED BY 'password_aman_anda';
GRANT ALL PRIVILEGES ON beesmartv3.* TO 'cbtuser'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import database
mysql -u cbtuser -p beesmartv3 < /var/www/html/cbt-smkalq/config/beesmartv3.sql
```

#### Langkah 3: Upload & Konfigurasi Project
```bash
# Clone atau upload project ke web root
cd /var/www/html/
sudo git clone https://github.com/username/cbt-smkalq.git
# Atau upload via SFTP ke /var/www/html/cbt-smkalq/

# Edit konfigurasi database
sudo nano /var/www/html/cbt-smkalq/config/server.php
```

**config/server.php (VPS):**
```php
<?php
$sqlconn = @mysql_connect("localhost:3306", "cbtuser", "password_aman_anda");
mysql_select_db("beesmartv3", $sqlconn);
$mode = "lokal";
?>
```

#### Langkah 4: Set Permission
```bash
# Set ownership ke web server user
sudo chown -R www-data:www-data /var/www/html/cbt-smkalq/

# Set permission folder upload
sudo chmod 755 /var/www/html/cbt-smkalq/fotosiswa
sudo chmod 755 /var/www/html/cbt-smkalq/images
sudo chmod 755 /var/www/html/cbt-smkalq/pictures
sudo chmod 755 /var/www/html/cbt-smkalq/audio
sudo chmod 755 /var/www/html/cbt-smkalq/video
sudo chmod 755 /var/www/html/cbt-smkalq/output
sudo chmod 755 /var/www/html/cbt-smkalq/file-excel

# Buat folder backup
sudo mkdir -p /var/www/backup
sudo chown www-data:www-data /var/www/backup

# Update path backup di config (edit file database/*.php)
# Ubah /opt/lampp/backup menjadi /var/www/backup
```

#### Langkah 5: Konfigurasi Virtual Host (Opsional)
```bash
sudo nano /etc/apache2/sites-available/cbt.conf
```

```apache
<VirtualHost *:80>
    ServerName cbt.smkalqodiriyah.sch.id
    DocumentRoot /var/www/html/cbt-smkalq
    
    <Directory /var/www/html/cbt-smkalq>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/cbt_error.log
    CustomLog ${APACHE_LOG_DIR}/cbt_access.log combined
</VirtualHost>
```

```bash
sudo a2ensite cbt.conf
sudo a2enmod rewrite
sudo systemctl reload apache2
```

#### Langkah 6: SSL dengan Let's Encrypt (Recommended)
```bash
sudo apt install certbot python3-certbot-apache -y
sudo certbot --apache -d cbt.smkalqodiriyah.sch.id
```

---

### 3. Shared Hosting (cPanel)

#### Langkah 1: Upload Files
1. Login ke cPanel
2. Buka **File Manager**
3. Navigate ke `public_html` atau subdirectory yang diinginkan
4. Upload file ZIP project, lalu **Extract**

#### Langkah 2: Buat Database
1. Di cPanel, buka **MySQL Databases**
2. Buat database baru (contoh: `username_beesmartv3`)
3. Buat user database dengan password
4. Assign user ke database dengan **ALL PRIVILEGES**

#### Langkah 3: Import Database
1. Buka **phpMyAdmin**
2. Pilih database yang baru dibuat
3. Klik **Import**
4. Upload file `config/beesmartv3.sql`
5. Klik **Go**

#### Langkah 4: Konfigurasi
1. Edit file `config/server.php` via File Manager:

```php
<?php
$sqlconn = @mysql_connect("localhost", "username_dbuser", "password_database");
mysql_select_db("username_beesmartv3", $sqlconn);
$mode = "lokal";
?>
```

2. Perbarui path backup di `database/*.php`:
   - Ubah `/opt/lampp/backup` → `/home/username/backup` (sesuaikan)

#### Langkah 5: Set Permission
1. Di cPanel File Manager, klik kanan folder berikut → **Change Permissions** → `755`:
   - `fotosiswa`, `images`, `pictures`, `audio`, `video`, `output`, `file-excel`

#### Langkah 6: Akses Aplikasi
```
Siswa: https://yourdomain.com/cbt-smkalq/
Admin: https://yourdomain.com/cbt-smkalq/panel/
```

> ⚠️ **Catatan Penting untuk Shared Hosting:**
> - Pastikan hosting mendukung PHP 5.6 (beberapa hosting sudah tidak support)
> - Aktifkan ekstensi `mysql` via **Select PHP Version** di cPanel
> - Jika menggunakan PHP 7+, perlu migrasi kode ke `mysqli` atau `PDO`

---

## 🗄️ Struktur Database

Database `beesmartv3` terdiri dari 16 tabel:

| Tabel | Fungsi |
|-------|--------|
| `cbt_admin` | Data identitas sekolah dan admin |
| `cbt_user` | User admin, guru, pengawas |
| `cbt_siswa` | Data siswa peserta ujian |
| `cbt_kelas` | Daftar kelas |
| `cbt_mapel` | Mata pelajaran |
| `cbt_soal` | Bank soal (pertanyaan + jawaban) |
| `cbt_paketsoal` | Paket soal untuk ujian |
| `cbt_tes` | Jenis ujian (UH, UTS, UAS, TO) |
| `cbt_ujian` | Jadwal ujian |
| `cbt_siswa_ujian` | Status ujian per siswa |
| `cbt_jawaban` | Jawaban siswa |
| `cbt_nilai` | Rekap nilai |
| `cbt_audio` | Tracking audio playback |
| `cbt_tugas` | Nilai tugas |
| `cbt_setid` | Tahun ajaran |
| `cbt_upload_file` | File yang diupload |

---

## 🔑 Kredensial Default

### Admin Panel
| Username | Password | Role |
|----------|----------|------|
| `admin` | `admin` | Administrator |
| `guru` | `guru` | Guru |

### Siswa
- Username: Nomor Ujian / NIS
- Password: Sesuai yang diinput di data siswa

> ⚠️ **Segera ganti password default setelah instalasi!**

---

## 📝 Catatan Migrasi & Perbaikan

### Perbaikan Path (Windows → Linux)

| File | Path Lama | Path Baru |
|------|-----------|-----------|
| `database/cbt_semua.php` | `C:/CBT_BEESMART` | `/opt/lampp/backup` |
| `database/cbt_ujian.php` | `C:/CBT_BEESMART` | `/opt/lampp/backup` |
| `database/cbt_siswa.php` | `C:/CBT_BEESMART` | `/opt/lampp/backup` |
| `database/cbt_jawaban.php` | `C:/CBT_BEESMART` | `/opt/lampp/backup` |
| `database/restore.php` | `C:/CBT_BEESMART` | `/opt/lampp/backup` |

### Konfigurasi Permission Folder
Folder dengan owner `daemon:daemon`, mode `755`:
- `/opt/lampp/htdocs/cbt-smkalq/audio`
- `/opt/lampp/htdocs/cbt-smkalq/file-excel`
- `/opt/lampp/htdocs/cbt-smkalq/fotosiswa`
- `/opt/lampp/htdocs/cbt-smkalq/images`
- `/opt/lampp/htdocs/cbt-smkalq/output`
- `/opt/lampp/htdocs/cbt-smkalq/pictures`
- `/opt/lampp/htdocs/cbt-smkalq/video`
- `/opt/lampp/backup`

Gunakan script `setup_permissions.sh` untuk mengatur ulang permission.

---

## 📜 Changelog

### ✅ Perbaikan Fitur Upload Siswa
- Fix error `Undefined variable: kata` pada `upload_siswa.php`
- Atasi masalah "File is not readable" dengan perbaikan permission folder temporary PHP
- Tambah validasi `is_readable` sebelum memproses file Excel

### ✅ Pembaruan Manajemen User
- Fitur ubah password langsung dari panel admin
- Security fix: password menggunakan hashing `md5()` yang kompatibel

### ✅ Redesign Halaman Login
- Gambar banner full height (`100vh`) dengan `object-fit: contain`
- Layout responsif (Gambar Kiri, Form Kanan)
- Perbaikan styling dan perataan form

### ✅ Role Pengawas & Akses Panel
- Role baru: `pengawas` dengan akses terbatas
- Menu sidebar disesuaikan berdasarkan role

### ✅ Backup Database
- Tampilkan maksimal 2 backup terakhir per jenis data
- Tombol download untuk file backup

### ✅ Stabilitas Panel di Mobile
- Tombol "Akhiri Tes" berfungsi di tablet/smartphone

### ✅ Security: Disable Copy/Paste saat Ujian
- Disable right-click pada halaman ujian
- Disable Ctrl+C dan Ctrl+V

---

## 📞 Dukungan

**Developed by:** Miftahus Syarif  
**Version:** 3.0  
**Last Updated:** Januari 2026

---

## 📄 License

Aplikasi ini dikembangkan untuk keperluan internal SMK Al Qodiriyah.
