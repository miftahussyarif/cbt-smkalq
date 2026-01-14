# CBT SMK AL QODIRIYAH

Aplikasi Computer Based Test (CBT) untuk SMK Al Qodiriyah.

## Ringkasan Fitur
- Manajemen user (admin/pengawas/siswa), termasuk ubah password.
- Manajemen data master: siswa, kelas, jurusan, mapel.
- Import data kelas/mapel/siswa via Excel serta upload foto siswa.
- Bank soal pilihan ganda (4/5 opsi) dan esai, input manual dan import Excel.
- Lampiran media soal (gambar, audio, video) serta upload file pendukung.
- Paket soal, penjadwalan ujian, token, durasi, acak soal/jawaban, reset peserta.
- Pelaksanaan ujian siswa dengan timer, simpan jawaban, ragu, lanjut.
- Penilaian esai manual dan rekap nilai per peserta.
- Monitoring/pengawasan ujian: status peserta, ping, dan event.
- Rekap nilai, analisa butir soal, ekspor Excel/PDF, cetak kartu/absen/berita acara.
- Backup/restore database dan sinkron data (mode lokal/pusat).

## Requirements
- Web server: Apache/Nginx (contoh: XAMPP/LAMPP).
- PHP 5.x (menggunakan fungsi `mysql_*`) dengan ekstensi mysql aktif.
- MySQL/MariaDB.
- Hak akses tulis untuk folder upload dan backup.

## Cara Deploy (Linux/XAMPP)
1. Salin project ke web root, contoh: `/opt/lampp/htdocs/cbt-smkalq`.
2. Buat database `beesmartv3`, lalu import `config/beesmartv3.sql`.
3. Atur koneksi database di `config/server.php`.
4. Jika memakai mode pusat, sesuaikan `config/ipserver.php` dan `config/server_pusat.php`, lalu ubah `$mode` di `config/server.php` ke `pusat`.
5. Set permission folder upload dan backup (lihat daftar di bawah), atau jalankan `setup_permissions.sh` (sesuaikan `BASE_DIR` bila path berbeda).
6. Akses aplikasi:
   - Siswa: `http://localhost/cbt-smkalq/`
   - Admin: `http://localhost/cbt-smkalq/panel/`

## Catatan Migrasi & Perbaikan (Windows ke Linux)

Berikut adalah dokumentasi perubahan yang dilakukan untuk menjalankan aplikasi ini di lingkungan Linux (XAMPP/LAMPP).

### 1. Perbaikan Path (Windows → Linux)
**Dalam file konfigurasi database (`/database/`):**

| File | Path Lama | Path Baru |
|------|-----------|-----------|
| `cbt_semua.php` | `C:/CBT_BEESMART` | `/opt/lampp/backup` |
| `cbt_ujian.php` | `C:/CBT_BEESMART` | `/opt/lampp/backup` |
| `cbt_siswa.php` | `C:/CBT_BEESMART` | `/opt/lampp/backup` |
| `cbt_jawaban.php` | `C:/CBT_BEESMART` | `/opt/lampp/backup` |
| `restore.php` | `C:/CBT_BEESMART` | `/opt/lampp/backup` |

**Dalam tampilan panel (`/panel/pages/`):**
- **`backup.php`**: Pesan lokasi backup diubah agar sesuai dengan path Linux (`/opt/lampp/backup/`).

### 2. Konfigurasi Permission Folder
Untuk memastikan fitur upload dan backup berfungsi, permission folder telah dikonfigurasi sebagai berikut (Owner: `daemon:daemon`, Mode: `755`). Sesuaikan nama folder jika berbeda:

- `/opt/lampp/htdocs/cbt-smkalq/audio`
- `/opt/lampp/htdocs/cbt-smkalq/file-excel`
- `/opt/lampp/htdocs/cbt-smkalq/fotosiswa`
- `/opt/lampp/htdocs/cbt-smkalq/images`
- `/opt/lampp/htdocs/cbt-smkalq/output`
- `/opt/lampp/htdocs/cbt-smkalq/pictures`
- `/opt/lampp/htdocs/cbt-smkalq/video`
- `/opt/lampp/backup` (Folder Database Backup)

**Script Utility:**
- `setup_permissions.sh`: Script bash disertakan untuk mengatur ulang permission jika diperlukan di masa mendatang.

---

## Log Perubahan & Fitur Baru (Changelog)

### A. Perbaikan Fitur Upload Siswa
- **Fix Error Variable**: Memperbaiki error `Undefined variable: kata` pada `upload_siswa.php`.
- **Fix Permission Temp**: Mengatasi masalah "File is not readable" dengan memperbaiki permission folder temporary PHP (`/opt/lampp/temp/`).
- **Validasi**: Menambahkan pengecekan `is_readable` sebelum memproses file Excel.

### B. Pembaruan Manajemen User
- **Fitur Ubah Password**: Menambahkan tombol dan modal popup untuk mengubah password user/admin secara langsung dari panel admin.
- **Security Fix**: Memperbaiki penyimpanan password baru agar menggunakan hashing `md5()` sehingga kompatibel dengan sistem login yang ada.

### C. Redesign Halaman Login
- **Full Height Image**: Gambar banner di halaman login sekarang menyesuaikan tinggi layar sepenuhnya (`100vh`) menggunakan `object-fit: contain` agar tidak terpotong di layar kecil.
- **Layout Responsif**: Tata letak diperbaiki (Gambar di Kiri, Form Login di Kanan).
- **Styling**: Menghapus background warna biru yang tidak sesuai dan merapikan perataan form menjadi rata kiri (`text-align: left`).
- **Fix Syntax**: Memperbaiki syntax error di `atur_tes.php` (masalah octal number dan loop structure).

