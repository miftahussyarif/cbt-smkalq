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

$token = mysql_real_escape_string($uj['XTokenUjian']);
$kodesoal = mysql_real_escape_string($uj['XKodeSoal']);

mysql_query("DELETE FROM cbt_jawaban WHERE XTokenUjian = '$token' AND XKodeSoal = '$kodesoal'");
mysql_query("DELETE FROM cbt_nilai WHERE XTokenUjian = '$token' AND XKodeSoal = '$kodesoal'");
mysql_query("DELETE FROM cbt_siswa_ujian WHERE XTokenUjian = '$token' AND XKodeSoal = '$kodesoal'");
mysql_query("DELETE FROM cbt_audio WHERE XTokenUjian = '$token' AND XKodeSoal = '$kodesoal'");

$cekPengawasan = mysql_query("SHOW TABLES LIKE 'cbt_pengawasan'");
if ($cekPengawasan && mysql_num_rows($cekPengawasan) > 0) {
    mysql_query("DELETE FROM cbt_pengawasan WHERE XTokenUjian = '$token' AND XKodeSoal = '$kodesoal'");
}

if ($aksi == 'hapus_jadwal') {
    mysql_query("DELETE FROM cbt_ujian WHERE Urut = '$urut'");
    bee_log('INFO', 'TEST_DELETE_SCHEDULE_OK', 'Hapus data dan jadwal tes selesai', array(
        'urut' => $urut,
        'token' => $token,
        'kodesoal' => $kodesoal
    ));
} else {
    bee_log('INFO', 'TEST_DELETE_DATA_OK', 'Hapus data hasil tes selesai', array(
        'urut' => $urut,
        'token' => $token,
        'kodesoal' => $kodesoal
    ));
}

echo "OK";
?>
