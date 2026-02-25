# CBT SMK AL QODIRIYAH

Aplikasi CBT berbasis web untuk pelaksanaan ujian sekolah.

## Fitur Utama
- Multi-role pengguna: Admin, Guru, Pengawas, Siswa.
- Manajemen data master: siswa, kelas, jurusan, mapel, tahun ajaran.
- Bank soal pilihan ganda/esai dengan dukungan media gambar, audio, video.
- Import data dan soal dari file Excel.
- Pengaturan jadwal, sesi, token, durasi, dan paket ujian.
- Pelaksanaan ujian online dengan timer, auto-save jawaban, dan navigasi soal.
- Monitoring peserta saat ujian (status, aktivitas, dan kontrol pengawas).
- Penilaian otomatis dan rekap hasil.
- Cetak dokumen ujian (kartu, daftar hadir, berita acara, hasil).
- Backup/restore database dan backup/restore file upload (pictures/audio/video/fotosiswa).

## Instalasi (Environment yang Dipakai Saat Ini)
Panduan ini khusus untuk server lokal dengan XAMPP/LAMPP seperti setup saat ini:
- OS Linux (Ubuntu/Debian desktop/server)
- XAMPP/LAMPP di `/opt/lampp`
- RAM 8 GB (opsional preset tuning tersedia)

### 1. Siapkan aplikasi di web root XAMPP
```bash
sudo cp -r cbt-smkalq /opt/lampp/htdocs/
cd /opt/lampp/htdocs/cbt-smkalq
```

### 2. Buat database dan import schema
```bash
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS beesmartv3;"
mysql -uroot beesmartv3 < config/beesmartv3.sql
```

### 3. Atur permission, folder runtime, dan inisialisasi tabel
```bash
sudo chmod +x /opt/lampp/htdocs/cbt-smkalq/install.sh
sudo /opt/lampp/htdocs/cbt-smkalq/install.sh
```

Script `install.sh` akan:
- Menyiapkan direktori yang dibutuhkan aplikasi.
- Menetapkan owner/group yang cocok untuk web user (`www-data`/`daemon`).
- Menetapkan permission writable untuk folder upload dan backup.
- Menjalankan inisialisasi tabel dari `database/schema.sql` bila tersedia.

### 4. (Opsional) Apply preset performa XAMPP 8GB
Preset ada di `config/preset-xampp-8gb/`.

```bash
sudo /opt/lampp/htdocs/cbt-smkalq/config/preset-xampp-8gb/apply-auto.sh
```

### 5. Jalankan layanan XAMPP
```bash
sudo /opt/lampp/lampp start
```

### 6. Akses aplikasi
- Siswa: `http://localhost/cbt-smkalq/`
- Admin: `http://localhost/cbt-smkalq/panel/`
