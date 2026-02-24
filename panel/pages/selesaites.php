<?php 
include "../../config/server.php";	
?>
<?php
$urut = isset($_REQUEST['txt_ujian']) ? mysql_real_escape_string($_REQUEST['txt_ujian']) : '';
bee_log('INFO', 'TEST_FINISH_REQUEST', 'Permintaan selesai tes', array(
    'id_ujian' => $urut
));

if ($urut === '') {
    echo "INVALID";
    exit;
}

$sqlujian = mysql_query("SELECT XTokenUjian, XKodeSoal FROM cbt_ujian WHERE Urut = '$urut' LIMIT 1");
if (!$sqlujian || mysql_num_rows($sqlujian) < 1) {
    echo "NOTFOUND";
    exit;
}
$uj = mysql_fetch_array($sqlujian);
$token = mysql_real_escape_string($uj['XTokenUjian']);
$kodesoal = mysql_real_escape_string($uj['XKodeSoal']);

$sqlaktif = mysql_query("SELECT COUNT(*) AS jml FROM cbt_siswa_ujian WHERE XStatusUjian = '1' AND XTokenUjian = '$token' AND XKodeSoal = '$kodesoal'");
$rowaktif = $sqlaktif ? mysql_fetch_array($sqlaktif) : array('jml' => 0);
$jmlaktif = isset($rowaktif['jml']) ? (int)$rowaktif['jml'] : 0;

if ($jmlaktif > 0) {
    bee_log('WARN', 'TEST_FINISH_BLOCKED_ACTIVE', 'Tes tidak bisa diakhiri karena masih ada peserta aktif', array(
        'id_ujian' => $urut,
        'token' => $token,
        'kodesoal' => $kodesoal,
        'peserta_aktif' => $jmlaktif
    ));
    echo "BUSY";
    exit;
}

$sqlselesai = mysql_query("update cbt_ujian set XStatusUjian = '9' where Urut = '$urut'");
if ($sqlselesai) {
    bee_log('INFO', 'TEST_FINISH_SUCCESS', 'Tes berhasil diakhiri', array(
        'id_ujian' => $urut,
        'affected_rows' => mysql_affected_rows()
    ));
    echo "OK";
} else {
    bee_log('ERROR', 'TEST_FINISH_FAILED', 'Gagal mengakhiri tes', array(
        'id_ujian' => $urut,
        'db_error' => mysql_error()
    ));
    echo "ERROR";
}
?>

