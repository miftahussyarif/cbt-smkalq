# Rekomendasi Improvement Tambahan untuk Sistem CBT

## 1. Database Constraints (HIGH PRIORITY)
Tambahkan UNIQUE constraint atau INDEX untuk mencegah duplikasi ujian pada kombinasi field yang sama:

```sql
-- Tambahkan UNIQUE INDEX untuk prevent duplikasi
ALTER TABLE cbt_ujian ADD UNIQUE INDEX uk_ujian_unique (
    XKodeSoal, 
    XKodeUjian, 
    XSemester, 
    XKodeKelas, 
    XKodeJurusan, 
    XKodeMapel, 
    XSetId, 
    XSesi
);

-- Ini akan:
-- ✓ Automatically prevent duplikat di database level
-- ✓ Memberikan error message yang jelas jika user coba membuat duplicate
-- ✓ Improve performa query dengan indexing
```

## 2. Query Pattern Review (MEDIUM PRIORITY)
Review semua file yang melakukan query terhadap `cbt_ujian` untuk memastikan:

### Files yang sudah dicheck:
- ✓ `panel/pages/simpan_jadwal.php` - PERBAIKAN SUDAH DILAKUKAN (+ XSesi filter)
- ☐ `panel/pages/daftar_tesbaru.php` - Ada query di baris 135 yang hanya menampilkan 1 record (perlu verifikasi apakah ini intentional)
- ☐ `panel/pages/atur_tes.php` - Perlu review apakah ada query serupa
- ☐ `panel/pages/status_tes.php` - File untuk status tes, perlu check query-nya
- ☐ `panel/pages/daftar_tes.php` - File untuk display daftar tes aktif

### Setiap query harus mempertimbangkan:
```
Apakah query ini seharusnya:
1. Return semua record untuk ALL sesi? → Jangan perlu filter XSesi
2. Return record untuk sesi spesifik? → HARUS ada filter XSesi
3. Return record pertama saja? → Jangan gunakan LIMIT 1 tanpa ORDER BY dan filter yang tepat
```

## 3. Code Refactoring (LOW PRIORITY - Untuk Future Release)

### Parameter Validation & Sanitization
Semua query di dalam kode masih menggunakan direct string concatenation (vulnerable to SQL injection):

```php
// SEBELUM (vulnerable):
$sqlubah = mysql_query("select * from cbt_ujian where XKodeSoal = '$requestKodeSoal' ...");

// SESUDAH (recommended):
// Gunakan prepared statements dengan parameter binding:
$sql = "SELECT * FROM cbt_ujian WHERE XKodeSoal = ? AND XKodeUjian = ? AND XSesi = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ssi", $requestKodeSoal, $requestKodeUjian, $requestSesi);
$stmt->execute();
$result = $stmt->get_result();
```

### Deprecation Warning
```
⚠️ PENTING: Script ini menggunakan mysql_* functions yang DEPRECATED dan dihapus di PHP 7.0+
   
Rekomendasi: Migrasi ke MySQLi atau PDO:
- Lebih secure (prevent SQL injection)
- Better error handling
- Prepared statements
- Existing maintenance di future
```

## 4. Testing Checklist

Sebelum deploy perbaikan ini ke production, lakukan test cases:

- [ ] Test 1: Membuat ujian Sesi 1 → Verifikasi data tersimpan dengan XSesi=1
- [ ] Test 2: Membuat ujian Sesi 2 dengan mapel sama → Verifikasi data baru, bukan timpa sesi 1
- [ ] Test 3: Membuat ujian Sesi 3 dengan mapel sama → Verifikasi tetap ada 3 record terpisah
- [ ] Test 4: Check tabel cbt_ujian langsung di database → Pastikan semua record ada
- [ ] Test 5: Check tabel cbt_siswa_ujian → Pastikan siswa assignment benar untuk setiap sesi
- [ ] Test 6: Check tabel cbt_jawaban → Pastikan jawaban terekam dengan token yang benar
- [ ] Test 7: Jalankan SELECT query yang diperbaiki → Pastikan hanya return record yang sesuai sesi

## 5. Monitoring & Prevention

### Add Logging
Tambahkan logging untuk semua operasi ujian:
```php
bee_log('INFO', 'UJIAN_CREATED', 'Ujian baru dibuat', array(
    'sesi' => $requestSesi,
    'mapel' => $requestKodeMapel,
    'ujian' => $requestKodeUjian,
    'timestamp' => date('Y-m-d H:i:s')
));

bee_log('INFO', 'UJIAN_UPDATED', 'Ujian diubah', array(
    'urut' => $urutUjian,
    'sesi' => $requestSesi,
    'timestamp' => date('Y-m-d H:i:s')
));
```

### Database Audit Trail
Pertimbangkan membuat tabel `cbt_ujian_audit` untuk track perubahan:
```sql
CREATE TABLE cbt_ujian_audit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ujian_urut INT,
    action VARCHAR(20), -- 'INSERT', 'UPDATE', 'DELETE'
    changed_at TIMESTAMP,
    changed_by VARCHAR(50),
    old_values JSON,
    new_values JSON
);
```

## 6. Documentation Updates

- [ ] Update user manual untuk menjelaskan konsep "Sesi" dalam membuat ujian
- [ ] Add warning jika user mencoba membuat ujian dengan mapel sama tapi sesi berbeda
- [ ] Documentasi error message yang mungkin muncul (e.g., "Nilai lama masih ada")

## Implementation Priority

| Priority | Item | Effort | Impact |
|----------|------|--------|--------|
| HIGH | Add UNIQUE INDEX ke database | 5 min | Prevent semua masalah serupa di masa depan |
| HIGH | Test perbaikan sesuai checklist | 30 min | Ensure perbaikan bekerja |
| MEDIUM | Review other query files | 1 hour | Find similar issues |
| MEDIUM | Add logging | 2 hours | Better monitoring |
| LOW | Migrate ke MySQLi/PDO | 2+ days | Long-term robustness |
| LOW | Create audit trail | 1 day | Compliance & debugging |

## Links & References
- [simpan_jadwal.php](panel/pages/simpan_jadwal.php) - File yang diperbaiki
- [PERBAIKAN_TES_GANDA_SESI.md](PERBAIKAN_TES_GANDA_SESI.md) - Dokumentasi perbaikan
- [TEST_PERBAIKAN_TESKODE_DELPLIK.sql](TEST_PERBAIKAN_TESKODE_DELPLIK.sql) - Script testing
