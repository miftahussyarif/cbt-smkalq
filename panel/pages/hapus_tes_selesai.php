<?php
include "../../config/server.php";

if (!isset($_COOKIE['beeuser'])) {
    bee_log('WARN', 'TEST_DELETE_NOAUTH', 'Akses hapus tes ditolak (no auth)');
    echo "NOAUTH";
    exit;
}

$aksi = isset($_POST['aksi']) ? $_POST['aksi'] : '';
$urut = isset($_POST['txt_ujian']) ? mysql_real_escape_string($_POST['txt_ujian']) : '';

bee_log('INFO', 'TEST_DELETE_REQUEST', 'Permintaan hapus data/jadwal tes', array(
    'aksi' => $aksi,
    'urut' => $urut
));

if (($aksi !== 'hapus' && $aksi !== 'hapus_jadwal') || $urut === '') {
    bee_log('WARN', 'TEST_DELETE_INVALID', 'Permintaan hapus tes tidak valid', array(
        'aksi' => $aksi,
        'urut' => $urut
    ));
    echo "INVALID";
    exit;
}

$sqlujian = mysql_query("SELECT XTokenUjian, XKodeSoal, XStatusUjian, XTglUjian, XJamUjian, XLamaUjian FROM cbt_ujian WHERE Urut = '$urut'");
if (!$sqlujian || mysql_num_rows($sqlujian) < 1) {
    if ($aksi === 'hapus_jadwal') {
        // Just in case it's already gone or partial delete
        $check = mysql_query("DELETE FROM cbt_ujian WHERE Urut = '$urut'"); 
        bee_log('INFO', 'TEST_DELETE_SCHEDULE_OK', 'Jadwal tes dihapus (fallback not found)', array(
            'urut' => $urut
        ));
        echo "OK"; 
        exit;
    }
    bee_log('WARN', 'TEST_DELETE_NOTFOUND', 'Data ujian tidak ditemukan untuk hapus tes', array(
        'urut' => $urut
    ));
    echo "NOTFOUND";
    exit;
}

$uj = mysql_fetch_array($sqlujian);
$isDoneByStatus = ($uj['XStatusUjian'] === '9' || $uj['XStatusUjian'] === '0');
$endAt = strtotime($uj['XTglUjian'] . ' ' . $uj['XJamUjian']) + strtotime($uj['XLamaUjian']) - strtotime('00:00:00');
$isDoneByTime = (time() >= $endAt);

if (!$isDoneByStatus && !$isDoneByTime) {
    bee_log('WARN', 'TEST_DELETE_NOTDONE', 'Hapus ditolak karena tes belum selesai', array(
        'urut' => $urut,
        'status' => $uj['XStatusUjian'],
        'end_at' => date('Y-m-d H:i:s', $endAt)
    ));
    echo "NOTDONE";
    exit;
}

$tokenRaw = isset($uj['XTokenUjian']) ? $uj['XTokenUjian'] : '';
$kodesoalRaw = isset($uj['XKodeSoal']) ? $uj['XKodeSoal'] : '';
$token = mysql_real_escape_string(trim($tokenRaw));
$kodesoal = mysql_real_escape_string(trim($kodesoalRaw));

// Gunakan normalisasi TRIM/REPLACE agar data dengan spasi tersembunyi juga ikut terhapus.
mysql_query("DELETE FROM cbt_jawaban WHERE (TRIM(XTokenUjian) = '$token' OR REPLACE(XTokenUjian,' ','') = REPLACE('$token',' ','')) AND (TRIM(XKodeSoal) = '$kodesoal' OR REPLACE(XKodeSoal,' ','') = REPLACE('$kodesoal',' ',''))");
$deletedJawaban = mysql_affected_rows();
mysql_query("DELETE FROM cbt_nilai WHERE (TRIM(XTokenUjian) = '$token' OR REPLACE(XTokenUjian,' ','') = REPLACE('$token',' ','')) AND (TRIM(XKodeSoal) = '$kodesoal' OR REPLACE(XKodeSoal,' ','') = REPLACE('$kodesoal',' ',''))");
$deletedNilai = mysql_affected_rows();
mysql_query("DELETE FROM cbt_siswa_ujian WHERE (TRIM(XTokenUjian) = '$token' OR REPLACE(XTokenUjian,' ','') = REPLACE('$token',' ','')) AND (TRIM(XKodeSoal) = '$kodesoal' OR REPLACE(XKodeSoal,' ','') = REPLACE('$kodesoal',' ',''))");
$deletedSiswaUjian = mysql_affected_rows();
mysql_query("DELETE FROM cbt_audio WHERE (TRIM(XTokenUjian) = '$token' OR REPLACE(XTokenUjian,' ','') = REPLACE('$token',' ','')) AND (TRIM(XKodeSoal) = '$kodesoal' OR REPLACE(XKodeSoal,' ','') = REPLACE('$kodesoal',' ',''))");
$deletedAudio = mysql_affected_rows();

$cekPengawasan = mysql_query("SHOW TABLES LIKE 'cbt_pengawasan'");
if ($cekPengawasan && mysql_num_rows($cekPengawasan) > 0) {
    mysql_query("DELETE FROM cbt_pengawasan WHERE (TRIM(XTokenUjian) = '$token' OR REPLACE(XTokenUjian,' ','') = REPLACE('$token',' ','')) AND (TRIM(XKodeSoal) = '$kodesoal' OR REPLACE(XKodeSoal,' ','') = REPLACE('$kodesoal',' ',''))");
}

if ($aksi == 'hapus_jadwal') {
    mysql_query("DELETE FROM cbt_ujian WHERE Urut = '$urut'");
    bee_log('INFO', 'TEST_DELETE_SCHEDULE_OK', 'Hapus data dan jadwal tes selesai', array(
        'urut' => $urut,
        'token' => $token,
        'kodesoal' => $kodesoal,
        'deleted_jawaban' => $deletedJawaban,
        'deleted_nilai' => $deletedNilai,
        'deleted_siswa_ujian' => $deletedSiswaUjian,
        'deleted_audio' => $deletedAudio
    ));
} else {
    bee_log('INFO', 'TEST_DELETE_DATA_OK', 'Hapus data hasil tes selesai', array(
        'urut' => $urut,
        'token' => $token,
        'kodesoal' => $kodesoal,
        'deleted_jawaban' => $deletedJawaban,
        'deleted_nilai' => $deletedNilai,
        'deleted_siswa_ujian' => $deletedSiswaUjian,
        'deleted_audio' => $deletedAudio
    ));
}

echo "OK";
?>
