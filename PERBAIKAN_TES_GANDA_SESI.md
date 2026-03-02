# Perbaikan: Ujian Sesi 1 Hilang Saat Membuat Ujian Sesi 2 dengan Mapel Sama

## Ringkasan Masalah
Ketika ada tes aktif untuk sesi 1 dengan mapel tertentu, dan kemudian dijadwalkan untuk sesi 2 dengan mapel tes yang sama, ujian sesi 1 menjadi hilang/tertimpa.

## Root Cause Analysis
Penyebab masalah ditemukan di file `panel/pages/simpan_jadwal.php`:

### Query yang Bermasalah (Baris 119 Original)
```php
$sqlubah = mysql_num_rows(mysql_query("select * from cbt_ujian where 
  XKodeSoal = '$requestKodeSoal' and  
  XKodeUjian = '$requestKodeUjian' and 
  XSemester = '$requestSemester' and 
  XKodeKelas = '$s[XKodeKelas]' and 
  XKodeJurusan = '$s[XKodeJurusan]' and 
  XKodeMapel = '$s[XKodeMapel]' and 
  XSetId = '$_COOKIE[beetahun]' "));  // <-- TIDAK ADA XSesi!
```

### Skenario Bug
1. **Sesi 1 dibuat**: 
   - Query SELECT mencari ujian dengan kriteria di atas → Tidak ditemukan (0 hasil)
   - INSERT baru ke cbt_ujian: Urut=1, XSesi=1, XKodeMapel='MTK', XStatusUjian=1

2. **Sesi 2 dibuat dengan mapel sama (MTK)**:
   - Query SELECT mencari ujian dengan kriteria yang SAMA (TANPA cek XSesi)
   - Query MENEMUKAN 1 hasil: ujian sesi 1 (karena mapel, kelas, jurusan semuanya sama)
   - Sistem berpikir ujian sudah ada
   - UPDATE record Urut=1 dengan data sesi 2 → **Ujian sesi 1 TERTIMPA**

## Perbaikan yang Dilakukan

### File: `panel/pages/simpan_jadwal.php`

**Perubahan Baris 119** (Menambahkan filter XSesi):
```php
// SEBELUM:
$sqlubah = mysql_num_rows(mysql_query("select * from cbt_ujian where XKodeSoal = '$requestKodeSoal' and  XKodeUjian = '$requestKodeUjian' and XSemester = '$requestSemester' and XKodeKelas = '$s[XKodeKelas]' and XKodeJurusan = '$s[XKodeJurusan]' and XKodeMapel = '$s[XKodeMapel]' and XSetId = '$_COOKIE[beetahun]' "));

// SESUDAH:
$sqlubah = mysql_num_rows(mysql_query("select * from cbt_ujian where XKodeSoal = '$requestKodeSoal' and  XKodeUjian = '$requestKodeUjian' and XSemester = '$requestSemester' and XKodeKelas = '$s[XKodeKelas]' and XKodeJurusan = '$s[XKodeJurusan]' and XKodeMapel = '$s[XKodeMapel]' and XSetId = '$_COOKIE[beetahun]' and XSesi = '$requestSesi' "));
```

**Perubahan Baris 123** (Menambahkan filter XSesi pada cek nilai):
```php
// SEBELUM:
$cekNilai = mysql_num_rows(mysql_query("select 1 from cbt_nilai where XKodeKelas = '$s[XKodeKelas]' and XKodeMapel = '$s[XKodeMapel]' and XKodeUjian = '$requestKodeUjian' and XSemester = '$requestSemester' and XSetId = '$_COOKIE[beetahun]' limit 1"));

// SESUDAH:
$cekNilai = mysql_num_rows(mysql_query("select 1 from cbt_nilai where XKodeKelas = '$s[XKodeKelas]' and XKodeMapel = '$s[XKodeMapel]' and XKodeUjian = '$requestKodeUjian' and XSemester = '$requestSemester' and XSetId = '$_COOKIE[beetahun]' and XSesi = '$requestSesi' limit 1"));
```

## Dampak Perbaikan
Dengan menambahkan filter `and XSesi = '$requestSesi'`, sistem sekarang akan:
1. Hanya menemukan ujian dengan sesi yang SAMA
2. Tidak akan keliru mendeteksi ujian sesi lain sebagai duplikat
3. Memungkinkan membuat ujian untuk mapel yang sama di sesi berbeda tanpa saling menimpa

## Test Case
**Scenario untuk memverifikasi perbaikan:**

| Langkah | Aksi | Expected Result |
|---------|------|-----------------|
| 1 | Buat tes Sesi 1: Mapel MTK, Jenis UH | ✓ Tersimpan, XSesi=1 |
| 2 | Buat tes Sesi 2: Mapel MTK, Jenis UH | ✓ Tersimpan, XSesi=2 (BARU, tidak timpa sesi 1) |
| 3 | Lihat daftar tes aktif | ✓ Menampilkan 2 record: sesi 1 dan sesi 2 |
| 4 | Lihat database tabel cbt_ujian | ✓ 2 records dengan Sesi berbeda |

## Catatan Penting
- Perbaikan ini mengasumsikan bahwa `$requestSesi` sudah diterima dari form/AJAX dengan benar
- Verifikasi juga dilakukan di file `edit_tes.php`, `atur_tes.php`, dan `daftar_tesbaru.php` bahwa mereka mengirim `txt_sesi` parameter ke `simpan_jadwal.php`
- Parameter `txt_sesi` memang dikirim oleh ketiga file tersebut

## Database Index Recommendation (Untuk performa)
Tambahkan composite index di tabel `cbt_ujian`:
```sql
ALTER TABLE cbt_ujian ADD INDEX idx_check_ujian 
(XKodeSoal, XKodeUjian, XSemester, XKodeKelas, XKodeJurusan, XKodeMapel, XSetId, XSesi);
```

## Files Dimodifikasi
- [panel/pages/simpan_jadwal.php](panel/pages/simpan_jadwal.php) - Perbaikan 2 query
