# CBT SMK AL QODIRIYAH

Aplikasi Computer Based Test (CBT) untuk SMK Al Qodiriyah.

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
Untuk memastikan fitur upload dan backup berfungsi, permission folder telah dikonfigurasi sebagai berikut (Owner: `daemon:daemon`, Mode: `755`):

- `/opt/lampp/htdocs/cbtsmkalq/audio`
- `/opt/lampp/htdocs/cbtsmkalq/file-excel`
- `/opt/lampp/htdocs/cbtsmkalq/fotosiswa`
- `/opt/lampp/htdocs/cbtsmkalq/images`
- `/opt/lampp/htdocs/cbtsmkalq/output`
- `/opt/lampp/htdocs/cbtsmkalq/pictures`
- `/opt/lampp/htdocs/cbtsmkalq/video`
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

### D. Repository
- Repository dipublikasikan ke GitHub: `https://github.com/miftahussyarif/cbt-smkalq` (Private).
