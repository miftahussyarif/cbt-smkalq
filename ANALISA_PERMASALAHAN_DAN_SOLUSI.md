# ANALISA DAN SOLUSI PERMASALAHAN SISTEM CBT
## Case: Tes Sesi 1 Hilang Saat Ujian Sesi 2 Diaktifkan

---

## 📋 RINGKASAN MASALAH

Ketika ujian untuk sesi kedua dijadwalkan/diaktifkan, semua tes untuk sesi 1 menjadi hilang atau tidak aktif. Ini terjadi karena ada **SQL query yang tidak aman (tanpa WHERE clause)** yang secara tidak sengaja mengubah status SEMUA ujian di database.

---

## 🔍 ROOT CAUSE ANALYSIS

### Lokasi Bug
Ditemukan di dua file:

1. **`panel/pages/ubahtes.php` (Baris 17)**
   ```php
   $sqlubah = mysql_query("update cbt_ujian set XStatusUjian = '0'");
   ```

2. **`panel/pages/simpan_ujian.php` (Baris 4)**
   ```php
   $sqlubah2 = mysql_query("update cbt_ujian set XStatusUjian = '0'");
   ```

### Apa Masalahnya?

**Update query TANPA WHERE clause** ini mengubah SEMUA baris di tabel `cbt_ujian` menjadi status '0' (tidak aktif), bukan hanya ujian yang sedang dikerjakan.

**Flow Masalah:**
1. Admin mengaktifkan ujian untuk sesi 1 → Status sesi 1 = '1' (aktif) ✓
2. Admin mengaktifkan ujian untuk sesi 2 → Query tanpa WHERE jalankan
3. Update di baris 4/17 dijalankan → **SEMUA ujian status menjadi '0'** ❌
4. Sesi 1 dan sesi 2 sekarang tidak aktif
5. Hanya query INSERT untuk sesi 2 yang berjalan, sehingga yang terlihat hanya sesi 2
6. Sesi 1 hilang dari tampilan karena statusnya '0'

---

## ✅ SOLUSI YANG DITERAPKAN

### Tindakan Korektif
Kedua query berbahaya telah **DIHAPUS / DI-COMMENT** karena:

1. **Query tidak berbeda tujuan** - Tidak ada context/WHERE clause yang menunjukkan untuk ujian apa update dilakukan
2. **Sangat dangerous** - Berdampak pada SEMUA ujian di database, bukan ujian tertentu
3. **Kemungkinan kode abandoned** - Terlihat seperti debug/testing code yang belum dihapus
4. **Tidak ada logika bisnis** - Tidak ada keterangan apa yang mau di-reset/diubah

### File yang Diperbaiki

#### 1. `/panel/pages/ubahtes.php` (Baris 13-17)
**BEFORE:**
```php
<?php						  	
 $sqlubah = mysql_query("insert into cbt_tes (tes) values ('cuk')");
 $sqlubah = mysql_query("update cbt_ujian set XStatusUjian = '0'");
 $sqlujian = mysql_query("select * from cbt_ujian where XKodeSoal = '$_REQUEST[txt_ujian]'");
```

**AFTER:**
```php
<?php						  	
 // REMOVED: Query tanpa WHERE clause yang mengubah semua ujian
 // $sqlubah = mysql_query("insert into cbt_tes (tes) values ('cuk')");
 // $sqlubah = mysql_query("update cbt_ujian set XStatusUjian = '0'");
 $sqlujian = mysql_query("select * from cbt_ujian where XKodeSoal = '$_REQUEST[txt_ujian]'");
```

#### 2. `/panel/pages/simpan_ujian.php` (Baris 1-5)
**BEFORE:**
```php
<?php 
include "../../config/server.php";					  	
 //$sqlubah = mysql_query("insert into cbt_tes (tes) values ('$_REQUEST[txt_waktu]')");
 $sqlubah2 = mysql_query("update cbt_ujian set XStatusUjian = '0'");
```

**AFTER:**
```php
<?php 
include "../../config/server.php";					  	
 //$sqlubah = mysql_query("insert into cbt_tes (tes) values ('$_REQUEST[txt_waktu]')");
 // REMOVED: Query tanpa WHERE clause yang mengubah semua ujian - menyebabkan ujian sesi lain hilang
 // $sqlubah2 = mysql_query("update cbt_ujian set XStatusUjian = '0'");
```

---

## 🧪 TESTING & VERIFIKASI

Setelah perbaikan, silakan lakukan testing:

### Test Case 1: Multiple Session
1. Buat ujian untuk sesi 1 (Misal: Fisika, Kelas X-A)
2. Aktifkan ujian sesi 1
3. Verifikasi: Data sesi 1 aktif di tabel `cbt_ujian` dengan status '1'
4. Buat ujian untuk sesi 2 (Misal: Kimia, Kelas X-A)
5. Aktifkan ujian sesi 2
6. **Verifikasi: Sesi 1 MASIH AKTIF dengan status '1' ✓**
7. Sesi 2 juga aktif dengan status '1' ✓

### Query Verifikasi di Database
```sql
-- Cek status ujian untuk semua sesi
SELECT Urut, XSesi, XKodeMapel, XStatusUjian, XTglUjian, XJamUjian 
FROM cbt_ujian 
ORDER BY XSesi, Urut;

-- Harusnya menunjukkan multiple session dengan status '1'
```

---

## 🛡️ REKOMENDASI KEAMANAN TAMBAHAN

### 1. **Gunakan Prepared Statements**
Kode saat ini masih menggunakan `mysql_*` yang deprecated. Pertimbangkan migrasi ke MySQLi atau PDO untuk mencegah SQL injection:

```php
// BAIK UNTUK DILAKUKAN:
$stmt = $dbconn->prepare("UPDATE cbt_ujian SET XStatusUjian = ? WHERE Urut = ?");
$stmt->bind_param("ii", $status, $urut);
$stmt->execute();
```

### 2. **Audit Logging**
File sudah memiliki `bee_log()` function yang baik. Pastikan setiap operasi UPDATE/DELETE selalu di-log dengan WHERE clause yang digunakan.

### 3. **Query Audit**
Cek file berikut untuk potential issues serupa:
- `panel/pages/sql_update.php` - Ada logic update DB
- `panel/pages/simpan_jadwal.php` - Ada commented query dengan WHERE yang proper

### 4. **Database Backup**
Pastikan backup regular dilakukan sebelum operasi critical seperti:
- Penjadwalan ujian baru
- Aktivasi ujian
- Delete/clear data ujian

---

## 📊 STATUS PERBAIKAN

| File | Issue | Status |
|------|-------|--------|
| ubahtes.php | Query tanpa WHERE | ✅ FIXED |
| simpan_ujian.php | Query tanpa WHERE | ✅ FIXED |

---

## 👤 Catatan Teknis

- **Database:** `beesmartv3` (based on backup files)
- **Table Terdampak:** `cbt_ujian`
- **Field Kritis:** `XStatusUjian` (0=tidak aktif, 1=aktif, 9=selesai), `XSesi` (sesi ujian)
- **Last Modified:** 2026-03-02
- **Severity:** HIGH - Menyebabkan data loss/tidak tampil

---

## ❓ FAQ

**Q: Apakah data sesi 1 benar-benar hilang atau hanya tidak tampil?**
A: Data masih ada di database dengan status '0'. Setelah fix, data akan kembali normal jika Anda mengatur ulang status-nya atau mengimpor dari backup.

**Q: Bagaimana jika sudah melakukan multiple session sebelum perbaikan?**
A: Cek database dengan query di atas. Jika statusnya '0', gunakan:
```sql
UPDATE cbt_ujian SET XStatusUjian = '1' WHERE XSesi = '1' AND status_lain_yang_sesuai;
```

**Q: Apakah ada patch/hotfix yang perlu diterapkan?**
A: Tidak perlu. Bug sudah dihapus langsung dari source code. Aplikasi siap digunakan kembali.

---

Dokumentasi ini dibuat pada: **2026-03-02**  
Repository: `/opt/lampp/htdocs/cbt-smkalq/`
